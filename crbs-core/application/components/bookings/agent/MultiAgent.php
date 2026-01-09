<?php

namespace app\components\bookings\agent;

defined('BASEPATH') OR exit('No direct script access allowed');


use app\components\bookings\exceptions\AgentException;
use app\components\bookings\Slot;
use \Bookings_model;
use Permission;


/**
 * Agent handles the creation/editing/cancellation of bookings.
 *
 */
class MultiAgent extends BaseAgent
{


	// Agent type
	const AGENT_MODE = 'multi';

	protected $department;

	private $multibooking;


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
		$this->view = 'bookings/create/multi';
		$this->title = lang('booking.create_multiple_bookings');

		$mb_id = (int) $this->CI->input->post_get('mb_id');
		$step = $this->CI->input->post_get('step');
		$this->view_data['step'] = $step;

		$this->view_data['mb_id'] = $mb_id;

		// Set some initial flags.
		// Update these if the MultiBooking data contains the capabilities
		$this->view_data['can_book_single'] = false;
		$this->view_data['can_book_recur'] = false;

		// Load the multibooking data from the DB if ID is provided.
		// If not provided, it will be created on the first step - 'selection'.
		//
		if ($mb_id) {

			$this->view_data['mb_id'] = $mb_id;

			$multibooking = $this->CI->multi_booking_model->get($mb_id, $this->user->user_id);

			if ( ! $multibooking) {
				$line = lang('booking.error.not_found');
				throw new AgentException($line);
			}

			$this->multibooking = $multibooking;
			$this->session = $this->CI->sessions_model->get($multibooking->session_id);
			$this->view_data['multibooking'] = $multibooking;

			// Determine capabilities from the slot data
			//
			$capabilities = array_column($multibooking->slots, 'capabilities');
			$singles = array_column($capabilities, 'single.create');
			$recurs = array_column($capabilities, 'recur.create');
			$can_book_single = $this->view_data['can_book_single'] = false;
			$can_book_recur = $this->view_data['can_book_recur'] = false;
			if (array_sum($singles) > 0) {
				$can_book_single = $this->view_data['can_book_single'] = true;
			}
			if (array_sum($recurs) > 0) {
				$can_book_recur = $this->view_data['can_book_recur'] = true;
			}

			// Determine default booking type
			if (is_null($this->booking_type)) {
				$booking_type = ($can_book_recur && ! $can_book_single)
					? 'recurring'
					: 'single'
					;
				$this->set_booking_type($booking_type);
			}

			$none = sprintf('(%s)', lang('app.none'));

			// Populate these lists in case any slots of capabilites to set them
			$this->populate_departments();
			$this->view_data['department_options'] = results_to_assoc($this->all_departments, 'department_id', 'name', $none);
			$this->populate_users();
			$this->view_data['user_options'] = results_to_assoc($this->all_users, 'user_id', fn($user) => !empty($user->displayname)
					? $user->displayname
					: $user->username, $none);

			// User constraints
			$this->view_data['user_booking_limit'] = null;
			$this->view_data['user_booking_count'] = null;
			$this->view_data['user_permitted_booking_count'] = null;
			$user_constraints = $this->CI->users_model->get_constraints($this->user->user_id);
			$user_limit = $user_constraints['max_active_bookings'];
			if ( ! is_null($user_limit)) {
				$user_booking_count = $this->CI->users_model->get_scheduled_booking_count($this->user->user_id);
				$permitted_booking_count = ($user_limit >= $user_booking_count)
					? $user_limit - $user_booking_count
					: null;
				$this->view_data['user_booking_limit'] = $user_limit;
				$this->view_data['user_booking_count'] = $user_booking_count;
				$this->view_data['user_permitted_booking_count'] = $permitted_booking_count;
			}
		}

		// Detect/set department
		$user_department_id = $this->user->department_id;
		if ($user_department_id) {
			$this->department = $this->CI->departments_model->Get($user_department_id);
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
		$this->title = lang('booking.create_multiple_bookings');

		switch ($this->booking_type) {

			case self::BOOK_SINGLE:
				$this->subview = 'bookings/create/multi/single_details';
				break;

			case self::BOOK_RECUR:
				$this->subview = 'bookings/create/multi/recur_defaults';
				break;

		}

		if ($this->CI->input->post()) {

			switch ($this->booking_type) {

				case self::BOOK_SINGLE:
					$this->process_create_single();
					break;

				case self::BOOK_RECUR:
					$this->process_recurring_defaults();
					break;
			}

		}
	}


	/*
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
	*/


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
			$line = lang('booking.error.no_slots_selected');
			throw new AgentException($line);
		}

		// Rows of data for multibooking.
		$rows = [];

		// Validation rules
		$rules = [
			['field' => 'date', 'label' => 'lang:app.date', 'rules' => 'required|valid_date'],
			['field' => 'period_id', 'label' => 'lang:period.period', 'rules' => 'required|integer'],
			['field' => 'room_id', 'label' => 'lang:room.room', 'rules' => 'required|integer'],
		];

		$this->CI->load->library('form_validation');

		foreach ($slots as $json) {

			$data = json_decode((string) $json, TRUE);

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
			throw AgentException::forNoSession();
		}

		if ( ! $date_info->week_id) {
			throw AgentException::forNoWeek();
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
			$line = lang('booking.error.multibooking_create_error');
			throw new AgentException($line);
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
			['field' => 'mb_id', 'label' => 'lang:app.id', 'rules' => 'required|integer'],
			['field' => 'slot_single[]', 'label' => 'lang:booking.notes', 'rules' => 'required'],
		];

		$this->CI->form_validation->set_rules($rules);

		if ($this->CI->form_validation->run() === FALSE) {
			$this->message = lang('app.form_error');
			return FALSE;
		}

		$rules = [
			['field' => 'date', 'label' => 'lang:app.date', 'rules' => 'required|valid_date'],
			['field' => 'session_id', 'label' => 'lang:session.session', 'rules' => 'required|integer'],
			['field' => 'period_id', 'label' => 'lang:period.period', 'rules' => 'required|integer'],
			['field' => 'room_id', 'label' => 'lang:room.room', 'rules' => 'required|integer'],
			['field' => 'department_id', 'label' => 'lang:department.department', 'rules' => 'integer'],
			['field' => 'user_id', 'label' => 'lang:user.user', 'rules' => 'integer'],
			['field' => 'notes', 'label' => 'lang:booking.notes', 'rules' => 'max_length[255]'],
		];

		$multibooking = $this->view_data['multibooking'];
		$form_slots = $this->CI->input->post('slot_single');
		$rows = [];

		foreach ($multibooking->slots as $slot_data) {

			$mbs_id = $slot_data->mbs_id;
			// Not in form
			if ( ! isset($form_slots[$mbs_id])) continue;
			$form_slot = $form_slots[$mbs_id];

			// Not selected for creation
			if ($form_slot['create'] == 0) continue;

			$room_id = $slot_data->room_id;

			if ( ! has_permission(Permission::BK_SGL_CREATE, $room_id)) continue;

			$department_id = null;
			if (has_permission(Permission::BK_SGL_SET_DEPT, $room_id)) {
				if (isset($form_slot['department_id'])) {
					$department_id = $form_slot['department_id'];
				}
			} else {
				if ( ! empty($this->user->department_id)) {
					$department_id = $this->user->department_id;
				}
			}

			$user_id = null;
			if (has_permission(Permission::BK_SGL_SET_USER, $room_id)) {
				if (isset($form_slot['user_id'])) {
					$user_id = $form_slot['user_id'];
				}
			} else {
				$user_id = $this->user->user_id;
			}

			$booking_data = [
				'date' => $slot_data->date,
				'session_id' => $multibooking->session_id,
				'period_id' => $slot_data->period_id,
				'room_id' => $slot_data->room_id,
				'department_id' => !empty($department_id) ? $department_id : null,
				'user_id' => !empty($user_id) ? $user_id : null,
				'notes' => !empty($form_slot['notes']) ? $form_slot['notes'] : null,
			];

			$this->CI->form_validation->reset_validation();
			$this->CI->form_validation->set_rules($rules);
			$this->CI->form_validation->set_data($booking_data);

			if ($this->CI->form_validation->run() === FALSE) {
				$line = lang('booking.error.some_invalid_values');
				$this->message = $line;
				return FALSE;
			}

			$rows[] = $booking_data;
		}

		if (empty($rows)) {
			$this->success = false;
			$this->message = lang('booking.error.none_created');
			return false;
		}

		// Check row count is within user limit
		if (isset($this->view_data['user_permitted_booking_count'])) {
			$user_permitted_booking_count = $this->view_data['user_permitted_booking_count'];
			if (count($rows) > $user_permitted_booking_count) {
				$this->success = false;
				$this->message = lang('booking.error.must_select_fewer');
				return false;
			}
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
			$this->success = true;
			$this->message = sprintf(lang('booking.success.some_created'), count($booking_ids));
			return true;
		}

		$err = $this->CI->bookings_model->get_error();

		$this->message = $err ?: lang('booking.error.generic')
			;

		return false;
	}


	private function process_recurring_defaults()
	{
		// Validate
		//
		$this->CI->load->library('form_validation');

		$rules = [
			['field' => 'mb_id', 'label' => 'lang:app.id', 'rules' => 'required|integer'],
			['field' => 'slots[]', 'label' => 'lang:booking.slot', 'rules' => 'required'],
		];

		$this->CI->form_validation->set_rules($rules);

		if ($this->CI->form_validation->run() === FALSE) {
			$this->message = lang('app.form_error');
			return FALSE;
		}

		// Rules for each slot

		$rules = [
			['field' => 'session_id', 'label' => 'lang:session.session', 'rules' => 'required|integer'],
			['field' => 'period_id', 'label' => 'lang:period.period', 'rules' => 'required|integer'],
			['field' => 'room_id', 'label' => 'lang:room.room', 'rules' => 'required|integer'],
			['field' => 'department_id', 'label' => 'lang:department.department', 'rules' => 'integer'],
			['field' => 'user_id', 'label' => 'lang:user.user', 'rules' => 'integer'],
			['field' => 'notes', 'label' => 'lang:booking.notes', 'rules' => 'max_length[255]'],
			['field' => 'recurring_start', 'label' => 'lang:booking.recur_start', 'rules' => 'required|valid_date'],
			['field' => 'recurring_end', 'label' => 'lang:booking.recur_end', 'rules' => 'required|valid_date'],
		];

		$form_slots = $this->CI->input->post('slots');

		$multibooking = $this->view_data['multibooking'];

		$slots = [];

		foreach ($multibooking->slots as $slot) {

			$mbs_id = $slot->mbs_id;
			// Not in form
			if ( ! isset($form_slots[$mbs_id])) continue;
			$form_slot = $form_slots[$mbs_id];
			// Skip if not selected for creation
			if ($form_slot['create'] == 0) continue;

			$recurring_start = $form_slot['recurring_start'];
			$recurring_end = $form_slot['recurring_end'];

			if ($recurring_start == 'session') {
				if (is_array($slot->recurring_dates)) {
					foreach ($slot->recurring_dates as $row) {
						$dt = datetime_from_string($row->date);
						if ($dt >= $this->session->date_start) {
							$recurring_start = $dt->format('Y-m-d');
							break;
						}
					}
				}
			}

			if ($recurring_end == 'session') {
				if (is_array($slot->recurring_dates)) {
					foreach (array_reverse($slot->recurring_dates) as $row) {
						$dt = datetime_from_string($row->date);
						if ($dt <= $this->session->date_end) {
							$recurring_end = $dt->format('Y-m-d');
							break;
						}
					}
				}
			}

			$room_id = $slot->room_id;

			$department_id = null;
			if (has_permission(Permission::BK_RECUR_CREATE, $room_id)) {
				if (isset($form_slot['department_id'])) {
					$department_id = $form_slot['department_id'];
				}
			} else {
				if ( ! empty($this->user->department_id)) {
					$department_id = $this->user->department_id;
				}
			}

			$user_id = null;
			if (has_permission(Permission::BK_RECUR_SET_USER, $room_id)) {
				if (isset($form_slot['user_id'])) {
					$user_id = $form_slot['user_id'];
				}
			} else {
				$user_id = $this->user->user_id;
			}

			$booking_data = [
				'session_id' => $multibooking->session_id,
				'period_id' => $slot->period_id,
				'room_id' => $slot->room_id,
				'department_id' => $department_id,
				'user_id' => $user_id,
				'notes' => !empty($form_slot['notes']) ? $form_slot['notes'] : NULL,
				'recurring_start' => $recurring_start,
				'recurring_end' => $recurring_end,
			];

			$this->CI->form_validation->reset_validation();
			$this->CI->form_validation->set_rules($rules);
			$this->CI->form_validation->set_data($booking_data);

			if ($this->CI->form_validation->run() === false) {
				$this->message = lang('booking.error.some_invalid_values');
				return false;
			}

			// Find recurring start/end dates
			//
			$slots[$mbs_id] = $booking_data;
		}

		// Store the booking data in the session for each slot
		//
		$session_key = sprintf('mb_%d_slots', $this->CI->input->post('mb_id'));
		$_SESSION[$session_key] = $slots;

		redirect(current_url().'?'.http_build_query([
			'mb_id' => $this->CI->input->post('mb_id'),
			'step' => 'recurring_preview',
		]));
	}


	private function handle_recurring_preview()
	{
		$this->subview = 'bookings/create/multi/recur_preview';

		$session_key = sprintf('mb_%d_slots', $this->view_data['mb_id']);

		$slot_data = $_SESSION[$session_key] ?? [];

		$user_constraints = $this->CI->users_model->get_constraints($this->user->user_id);
		$max_instances = $user_constraints['recur_max_instances'];

		// Get existing bookings for conflicts
		$conflict_count = 0;

		// Loop through all slots in this multibooking
		foreach ($this->multibooking->slots as &$slot) {

			$mbs_id = $slot->mbs_id;

			$slot->conflict_count = 0;
			$slot->ignore = false;

			// Get booking data values from previous step
			$data = $slot_data[$mbs_id] ?? FALSE;
			if ( ! $data) {
				$slot->ignore = true;
				continue;
			}

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

			$num_bookable = 0;

			// Update 'instances' data with the options for each one
			foreach ($instances as $key => $instance) {

				$actions = [];

				if (array_key_exists($key, $existing_bookings)) {
					$actions = $this->get_actions($existing_bookings[$key]);
					// $actions['do_not_book'] = 'Keep existing booking';
					// $actions['replace'] = 'Replace existing booking';
					$instances[$key]['booking'] = $existing_bookings[$key];
					$slot->conflict_count++;
					$conflict_count++;
				} else {
					$actions['book'] = lang('booking.book');
					$actions['do_not_book'] = lang('booking.do_not_book');
					$num_bookable++;
				}

				$instances[$key]['actions'] = $actions;
			}

			if ( ! is_null($max_instances) && $num_bookable > $max_instances) {
				$diff = $num_bookable - $max_instances;
				$line = lang('booking.error.too_many_instances');
				$msg = sprintf($line, $max_instances, $diff);
				$slot->message = $msg;
				$slot->conflict_count++;
				$conflict_count++;
			}

			$slot->existing_bookings = $existing_bookings;
			$slot->instances = $instances;
		}

		$this->view_data['slot_data'] = $slot_data;
		$this->view_data['conflict_count'] = $conflict_count;

		if ($this->CI->input->post()) {
			$this->process_create_recurring();
		}
	}


	/**
	 * Create all recurring bookings.
	 *
	 */
	private function process_create_recurring()
	{
		$this->CI->load->library('form_validation');

		$rules = [
			['field' => 'mb_id', 'label' => 'lang:app.id', 'rules' => 'required|integer'],
			['field' => 'step', 'label' => 'lang:app.step', 'rules' => 'required|in_list[recurring_preview]'],
			['field' => 'dates[]', 'label' => 'lang:app.dates', 'rules' => 'required'],
		];

		$this->CI->form_validation->set_rules($rules);

		if ($this->CI->form_validation->run() === FALSE) {
			$this->message = lang('app.form_error');
			return FALSE;
		}

		// Get values for each series
		$session_key = sprintf('mb_%d_slots', $this->multibooking->mb_id);
		$session_slots = $_SESSION[$session_key] ?? [];

		// Get selected dates
		$dates = $this->CI->input->post('dates');

		$multibooking = $this->view_data['multibooking'];

		$repeat_ids = [];

		$user_constraints = $this->CI->users_model->get_constraints($this->user->user_id);
		$max_instances = $user_constraints['recur_max_instances'];

		$this->CI->db->trans_begin();

		foreach ($multibooking->slots as $slot) {

			$mbs_id = $slot->mbs_id;

			if ( ! isset($session_slots[$mbs_id])) continue;
			$slot_data = $session_slots[$mbs_id];

			if ( ! isset($dates[$mbs_id])) continue;
			$slot_dates = $dates[$mbs_id];
			if (empty($slot_dates)) continue;

			if ( ! is_null($max_instances)) {
				$i = 0;
				foreach ($slot_dates as $date => &$info) {
					if ($info['action'] !== 'book') continue;
					$i++;
					if ($i > $max_instances) {
						// Ensure we do not book once we have reached the constraint's max instances
						$info['action'] = 'do_not_book';
					}
				}
			}

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
				$this->message = lang('booking.error.generic');
				return FALSE;
			}

			$repeat_ids[] = $repeat_id;
		}

		if ($this->CI->db->trans_status() === FALSE) {
			$this->CI->db->trans_rollback();
			$this->message = lang('booking.error.generic');
			return FALSE;
		}

		$this->CI->db->trans_commit();

		$this->success = TRUE;
		$line = lang('booking.success.recurring.some_created');
		$this->message = sprintf($line, count($repeat_ids));
		return TRUE;
	}


}
