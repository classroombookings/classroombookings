<?php

namespace app\components\bookings\agent;

defined('BASEPATH') OR exit('No direct script access allowed');


use app\components\bookings\exceptions\AgentException;
use app\components\bookings\Slot;
use \Bookings_model;


/**
 * Agent handles the creation/editing/cancellation of bookings.
 *
 */
class MultiAgent extends BaseAgent
{


	// Agent type
	const TYPE = 'multi';

	protected $department;

	private $selected_slots;
	private $multibooking;
	private $max_allowed_bookings = NULL;


	public function get_view_data()
	{
		return [
			'department' => $this->department,
		];
	}


	/**
	 * Initialise the Agent with some values.
	 *
	 * Depending on the type of booking, these will be retrieved from different places.
	 *
	 */
	public function load()
	{
		$department_id = $this->user->department_id;
		if ($this->is_admin && $this->CI->input->post('department_id')) {
			$department_id = $this->CI->input->post('department_id');
		}

		if (!empty($department_id)) {
			$this->department = $this->CI->departments_model->Get($department_id);
		}

		// Check if the number of bookings selected is within the user's quota
		if ( ! $this->is_admin) {
			$max_active_bookings = (int) abs(setting('num_max_bookings'));
			if ($max_active_bookings > 0) {
				$user_active_booking_count = $this->CI->bookings_model->CountScheduledByUser($this->user->user_id);
				$this->max_allowed_bookings = ($max_active_bookings - $user_active_booking_count);
			}
		}

		$this->view = 'bookings/create/multi';
		$this->title = 'Create multiple bookings';

		$mb_id = (int) $this->CI->input->post_get('mb_id');
		$step = $this->CI->input->post_get('step');

		// Load the multibooking data from the DB if ID is provided.
		//
		if ($mb_id) {

			$this->view_data['mb_id'] = $mb_id;

			$multibooking = $this->CI->multi_booking_model->get($mb_id, $this->user->user_id);

			if ( ! $multibooking) {
				throw new AgentException('Could not load booking data.');
			}

			$this->multibooking = $multibooking;
			$this->session = $this->CI->sessions_model->get($multibooking->session_id);
			$this->view_data['multibooking'] = $multibooking;
		}

		// Determine the handler method based on the provided 'step' value.
		//

		switch ($step) {

			case 'selection':
				$this->handle_selection();
				break;

			case 'details':
				$this->handle_details();
				break;

			case 'recurring_customise':
				$this->handle_recurring_customise();
				break;

			case 'recurring_preview':
				$this->handle_recurring_preview();
				break;
		}
	}


	/**
	 * Handle the POST input from the bookings grid.
	 *
	 * No 'view' page for this one so immediately defer to processing the data.
	 *
	 */
	private function handle_selection()
	{
		if ($this->CI->input->post()) {
			$this->process_selection();
		}
	}


	/**
	 * First step of creating bookings.
	 *
	 * Show form to choose single/recurring.
	 *
	 *  For single bookings:
	 *  	- individual bookings can be (un-)selected and user can enter department/user/notes.
	 *
	 *  For recurring bookings:
	 *  	- Provide default user/department/notes.
	 *
	 */
	private function handle_details()
	{
		$this->view = 'bookings/create/multi/details';
		$this->title = 'Create multiple bookings';

		switch ($this->CI->input->post('type')) {

			case 'single':
				$this->process_create_single();
				break;

			case 'recurring':
				$this->process_recurring_defaults();
				break;
		}
	}


	private function handle_recurring_customise()
	{
		$this->view = 'bookings/create/multi/recurring_customise';
		$this->title = 'Create multiple recurring bookings';

		$session_key = sprintf('mb_%d', $this->view_data['mb_id']);
		$this->view_data['default_values'] = isset($_SESSION[$session_key]) ? $_SESSION[$session_key] : [];

		if ($this->CI->input->post()) {
			$this->process_recurring_customise();
		}
	}


	private function handle_recurring_preview()
	{
		$this->view = 'bookings/create/multi/recurring_preview';
		$this->title = 'Create multiple recurring bookings';

		$session_key = sprintf('mb_%d_slots', $this->view_data['mb_id']);

		$slot_data = isset($_SESSION[$session_key]) ? $_SESSION[$session_key] : [];

		// die(print_r($slot_data));

		// Get existing bookings for conflicts

		// Loop through all slots in this multibooking
		foreach ($this->multibooking->slots as &$slot) {

			$mbs_id = $slot->mbs_id;

			$slot->conflict_count = 0;

			// Get booking data values from previous step
			$data = isset($slot_data[$mbs_id]) ? $slot_data[$mbs_id] : FALSE;
			if ( ! $data) continue;

			$recurring_start = datetime_from_string($data['recurring_start']);
			$recurring_end = datetime_from_string($data['recurring_end']);

			$dates = [];
			$instances = [];

			foreach ($slot->recurring_dates as $row) {
				if ($row->date < $recurring_start) continue;
				if ($row->date > $recurring_end) continue;
				$date_ymd = $row->date->format('Y-m-d');
				// Add date to list of dates for find_conflicts() check
				$dates[] = $date_ymd;
				// Generate key and add the date to the list of instances that will be created
				$key = Slot::generate_key($date_ymd, $slot->period_id, $slot->room_id);
				$instances[$key]['datetime'] = $row->date;
			}

			// Find conflicts for this booking
			$existing_bookings = $this->CI->bookings_model->find_conflicts($dates, $slot->period_id, $slot->room_id);

			// Update 'instances' data with the options for each one
			foreach ($instances as $key => $instance) {

				$actions = [];

				if (array_key_exists($key, $existing_bookings)) {
					$actions['do_not_book'] = 'Keep existing booking';
					$actions['replace'] = 'Replace existing booking';
					$instances[$key]['booking'] = $existing_bookings[$key];
					$slot->conflict_count++;
				} else {
					$actions['book'] = 'Book';
					$actions['do_not_book'] = 'Do not book';
				}

				$instances[$key]['actions'] = $actions;
			}

			$slot->existing_bookings = $existing_bookings;
			$slot->instances = $instances;
		}

		$this->view_data['slot_data'] = $slot_data;

		if ($this->CI->input->post()) {
			$this->process_create_recurring();
		}
	}




	/**
	 * Initial selection step.
	 *
	 * Create new multibookingentry and go to next step.
	 *
	 */
	public function process_selection()
	{
		$slots = $this->CI->input->post('slots');

		if ( ! $slots || empty($slots)) {
			throw new AgentException("You did not select any free slots to book.");
		}


		if ( ! $this->is_admin && is_numeric($this->max_allowed_bookings)) {
			if (count($slots) > $this->max_allowed_bookings) {
				$msg = "You can only create a maximum of %d booking(s), please select fewer periods.";
				throw new AgentException(sprintf($msg, $this->max_allowed_bookings));
			}
		}

		// Rows of data for multibooking.
		$rows = [];

		// Validation rules
		$rules = [
			['field' => 'date', 'label' => 'Date', 'rules' => 'required|valid_date'],
			['field' => 'period_id', 'label' => 'Period', 'rules' => 'required|integer'],
			['field' => 'room_id', 'label' => 'Room', 'rules' => 'required|integer'],
		];

		$this->CI->load->library('form_validation');

		foreach ($slots as $json) {

			$data = json_decode($json, TRUE);

			$this->CI->form_validation->set_rules($rules);
			$this->CI->form_validation->set_data($data);

			if ($this->CI->form_validation->run() === FALSE) {
				throw new AgentException(validation_errors());
			}

			$rows[] = [
				'date' => $data['date'],
				'period_id' => $data['period_id'],
				'room_id' => $data['room_id'],
			];
		}

		// Get first date
		$date_ymd = $rows[0]['date'];
		// Get Date info
		$date_info = $this->CI->dates_model->get_by_date($date_ymd);

		// The scenarios below shouldn't really happen:

		if ( ! $date_info || ! $date_info->session_id) {
			throw new AgentException('Selected date does not belong to a session.');
		}

		if ( ! $date_info->week_id) {
			throw new AgentException('Selected date does not belong to a timetable week.');
		}

		// Got data - create multibooking entry.
		$mb_data = [
			'user_id' => $this->user->user_id,
			'session_id' => $date_info->session_id,
			'week_id' => $date_info->week_id,
			'slots' => $rows,
		];

		$mb_id = $this->CI->multi_booking_model->create($mb_data);

		if ( ! $mb_id) {
			throw new AgentException("Could not create multibooking entry.");
		}

		redirect(current_url() . '?' . http_build_query([
			'mb_id' => $mb_id,
			'step' => 'details',
		]));
	}


	/**
	 * Create multiple single bookings.
	 *
	 */
	private function process_create_single()
	{
		$this->CI->load->library('form_validation');

		$rules = [
			['field' => 'mb_id', 'label' => 'ID', 'rules' => 'required|integer'],
			['field' => 'type', 'label' => 'Type', 'rules' => 'required|in_list[single]'],
			['field' => 'slot_single[]', 'label' => 'Notes', 'rules' => 'required'],
		];

		$this->CI->form_validation->set_rules($rules);

		if ($this->CI->form_validation->run() === FALSE) {
			$this->message = 'The form contained some invalid values. Please check and try again.';
			return FALSE;
		}

		$rules = [
			['field' => 'date', 'label' => 'Date', 'rules' => 'required|valid_date'],
			['field' => 'session_id', 'label' => 'Session', 'rules' => 'required|integer'],
			['field' => 'period_id', 'label' => 'Period', 'rules' => 'required|integer'],
			['field' => 'room_id', 'label' => 'Room', 'rules' => 'required|integer'],
			['field' => 'department_id', 'label' => 'Department', 'rules' => 'integer'],
			['field' => 'user_id', 'label' => 'User', 'rules' => 'integer'],
			['field' => 'notes', 'label' => 'Notes', 'rules' => 'max_length[255]'],
		];

		$form_slots = $this->CI->input->post('slot_single');

		$multibooking = $this->view_data['multibooking'];

		foreach ($multibooking->slots as $slot_data) {

			$mbs_id = $slot_data->mbs_id;
			// Not in form
			if ( ! isset($form_slots[$mbs_id])) continue;
			$form_slot = $form_slots[$mbs_id];

			// Not selected for creation
			if ($form_slot['create'] == 0) continue;

			$department_id = NULL;
			if (isset($form_slot['department_id'])) {
				$department_id = $form_slot['department_id'];
			}

			$user_id = NULL;
			if (isset($form_slot['user_id'])) {
				$user_id = $form_slot['user_id'];
			}

			// Force logged-in user details for non-admins
			if ( ! $this->is_admin) {
				$user_id = $this->user->user_id;
				$department_id = $this->user->department_id;
			}

			if (empty($department_id)) {
				$department_id = NULL;
			}

			$booking_data = [
				'date' => $slot_data->date,
				'session_id' => $multibooking->session_id,
				'period_id' => $slot_data->period_id,
				'room_id' => $slot_data->room_id,
				'department_id' => $department_id,
				'user_id' => !empty($user_id) ? $user_id : NULL,
				'notes' => !empty($form_slot['notes']) ? $form_slot['notes'] : NULL,
			];

			$this->CI->form_validation->reset_validation();
			$this->CI->form_validation->set_rules($rules);
			$this->CI->form_validation->set_data($booking_data);

			if ($this->CI->form_validation->run() === FALSE) {
				$this->message = 'One or more of the bookings contained some invalid values. Please check and try again.';
				return FALSE;
			}

			$rows[] = $booking_data;
		}

		$booking_ids = [];

		$this->CI->db->trans_start();

		foreach ($rows as $row) {
			$booking_ids[] = $this->CI->bookings_model->create($row);
		}

		$this->CI->db->trans_complete();

		if (count($booking_ids) === count($rows)) {
			// Clear multibooking entry
			$this->CI->multi_booking_model->delete($multibooking->mb_id);
			// Finish
			$this->success = TRUE;
			$this->message = sprintf('%d bookings have been created.', count($booking_ids));
			return TRUE;
		}

		$err = $this->CI->bookings_model->get_error();

		$this->message = ($err)
			? $err
			: 'Could not create booking.';

		return FALSE;
	}


	private function process_recurring_defaults()
	{
		// Validation rules
		$rules = [
			['field' => 'mb_id', 'label' => 'Multibooking ID', 'rules' => 'required|integer'],
			['field' => 'step', 'label' => 'Step', 'rules' => 'required'],
			['field' => 'department_id', 'label' => 'Department', 'rules' => 'integer'],
			['field' => 'user_id', 'label' => 'User', 'rules' => 'integer'],
			['field' => 'notes', 'label' => 'Notes', 'rules' => 'max_length[255]'],
			['field' => 'recurring_start', 'label' => 'Recurring start', 'rules' => 'required'],
			['field' => 'recurring_end', 'label' => 'Recurring end', 'rules' => 'required'],
		];

		$this->CI->load->library('form_validation');
		$this->CI->form_validation->set_rules($rules);

		if ($this->CI->form_validation->run() === FALSE) {
			$this->message = 'One or more of the bookings contained some invalid values. Please check and try again.';
			return FALSE;
		}

		$session_key = sprintf('mb_%d', $this->CI->input->post('mb_id'));

		$_SESSION[$session_key] = [
			'department_id' => $this->CI->input->post('department_id'),
			'user_id' => $this->CI->input->post('user_id'),
			'notes' => $this->CI->input->post('notes'),
			'recurring_start' => $this->CI->input->post('recurring_start'),
			'recurring_end' => $this->CI->input->post('recurring_end'),
		];

		redirect(current_url() . '?' . http_build_query([
			'mb_id' => $this->CI->input->post('mb_id'),
			'step' => 'recurring_customise',
		]));
	}


	/**
	 * Get all the details for all the slots.
	 *
	 */
	private function process_recurring_customise()
	{
		$this->CI->load->library('form_validation');

		$rules = [
			['field' => 'mb_id', 'label' => 'ID', 'rules' => 'required|integer'],
			['field' => 'step', 'label' => 'Step', 'rules' => 'required|in_list[recurring_customise]'],
			['field' => 'slots[]', 'label' => 'Slot', 'rules' => 'required'],
		];

		$this->CI->form_validation->set_rules($rules);

		if ($this->CI->form_validation->run() === FALSE) {
			$this->message = 'The form contained some invalid values. Please check and try again.';
			return FALSE;
		}

		// Rules for each slot

		$rules = [
			['field' => 'session_id', 'label' => 'Session', 'rules' => 'required|integer'],
			['field' => 'period_id', 'label' => 'Period', 'rules' => 'required|integer'],
			['field' => 'room_id', 'label' => 'Room', 'rules' => 'required|integer'],
			['field' => 'department_id', 'label' => 'Department', 'rules' => 'integer'],
			['field' => 'user_id', 'label' => 'User', 'rules' => 'integer'],
			['field' => 'notes', 'label' => 'Notes', 'rules' => 'max_length[255]'],
			['field' => 'recurring_start', 'label' => 'Recurring start', 'rules' => 'required|valid_date'],
			['field' => 'recurring_end', 'label' => 'Recurring end', 'rules' => 'required|valid_date'],
		];

		$form_slots = $this->CI->input->post('slots');

		$multibooking = $this->view_data['multibooking'];

		$slots = [];

		foreach ($multibooking->slots as $slot) {

			$mbs_id = $slot->mbs_id;
			// Not in form
			if ( ! isset($form_slots[$mbs_id])) continue;
			$form_slot = $form_slots[$mbs_id];
			// Not selected for creation. Not used in this process yet.
			// if ($form_slot['create'] == 0) continue;

			$recurring_start = $form_slot['recurring_start'];
			$recurring_end = $form_slot['recurring_end'];

			if ($recurring_start == 'session') {
				foreach ($slot->recurring_dates as $row) {
					$dt = datetime_from_string($row->date);
					if ($dt >= $this->session->date_start) {
						$recurring_start = $dt->format('Y-m-d');
						break;
					}
				}
			}

			if ($recurring_end == 'session') {
				foreach (array_reverse($slot->recurring_dates) as $row) {
					$dt = datetime_from_string($row->date);
					if ($dt <= $this->session->date_end) {
						$recurring_end = $dt->format('Y-m-d');
						break;
					}
				}
			}

			$booking_data = [
				'session_id' => $multibooking->session_id,
				'period_id' => $slot->period_id,
				'room_id' => $slot->room_id,
				'department_id' => !empty($form_slot['department_id']) ? $form_slot['department_id'] : NULL,
				'user_id' => !empty($form_slot['user_id']) ? $form_slot['user_id'] : NULL,
				'notes' => !empty($form_slot['notes']) ? $form_slot['notes'] : NULL,
				'recurring_start' => $recurring_start,
				'recurring_end' => $recurring_end,
			];

			$this->CI->form_validation->reset_validation();
			$this->CI->form_validation->set_rules($rules);
			$this->CI->form_validation->set_data($booking_data);

			if ($this->CI->form_validation->run() === FALSE) {
				$this->message = 'One or more of the bookings contained some invalid values. Please check and try again.';
				return FALSE;
			}

			// Find recurring start/end dates
			//

			$slots[$mbs_id] = $booking_data;
		}

		$session_key = sprintf('mb_%d_slots', $this->CI->input->post('mb_id'));
		$_SESSION[$session_key] = $slots;

		redirect(current_url() . '?' . http_build_query([
			'mb_id' => $this->CI->input->post('mb_id'),
			'step' => 'recurring_preview',
		]));
	}


	/**
	 * Create all recurring bookings.
	 *
	 */
	private function process_create_recurring()
	{
		$this->CI->load->library('form_validation');

		$rules = [
			['field' => 'mb_id', 'label' => 'ID', 'rules' => 'required|integer'],
			['field' => 'step', 'label' => 'Step', 'rules' => 'required|in_list[recurring_preview]'],
			['field' => 'dates[]', 'label' => 'Dates', 'rules' => 'required'],
		];

		$this->CI->form_validation->set_rules($rules);

		if ($this->CI->form_validation->run() === FALSE) {
			$this->message = 'The form contained some invalid values. Please check and try again.';
			return FALSE;
		}

		// Get values for each series
		$session_key = sprintf('mb_%d_slots', $this->multibooking->mb_id);
		$session_slots = isset($_SESSION[$session_key]) ? $_SESSION[$session_key] : [];

		// Get selected dates
		$dates = $this->CI->input->post('dates');

		$multibooking = $this->view_data['multibooking'];

		$repeat_ids = [];

		$this->CI->db->trans_begin();

		foreach ($multibooking->slots as $slot) {

			$mbs_id = $slot->mbs_id;

			if ( ! isset($session_slots[$mbs_id])) continue;
			$slot_data = $session_slots[$mbs_id];

			if ( ! isset($dates[$mbs_id])) continue;
			$slot_dates = $dates[$mbs_id];
			if (empty($slot_dates)) continue;

			$repeat_data = [
				'session_id' => $multibooking->session_id,
				'period_id' => $slot->period_id,
				'room_id' => $slot->room_id,
				'user_id' => !empty($slot_data['user_id']) ? $slot_data['user_id'] : NULL,
				'department_id' => !empty($slot_data['department_id']) ? $slot_data['department_id'] : NULL,
				'week_id' => $multibooking->week_id,
				'weekday' => $slot->weekday,
				'status' => Bookings_model::STATUS_BOOKED,
				'notes' => !empty($slot_data['notes']) ? $slot_data['notes'] : NULL,
				'dates' => $slot_dates,
			];

			// bookings_repeat_model will also handle creation of individual bookings.
			$repeat_id = $this->CI->bookings_repeat_model->create($repeat_data);

			if ( ! $repeat_id) {
				$this->CI->db->trans_rollback();
				$this->message = 'Could not create one or more recurring bookings.';
				return FALSE;
			}

			$repeat_ids[] = $repeat_id;
		}

		if ($this->CI->db->trans_status() === FALSE) {
			$this->CI->db->trans_rollback();
			$this->message = 'Could not create recurring bookings.';
			return FALSE;
		}

		$this->CI->db->trans_commit();

		$this->success = TRUE;
		$this->message = sprintf('%d recurring bookings have been created successfully.', count($repeat_ids));
		return TRUE;
	}


}
