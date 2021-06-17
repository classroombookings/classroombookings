<?php
defined('BASEPATH') OR exit('No direct script access allowed');

namespace app\components\bookings;

// use app\components\Calendar;
use app\components\bookings\exceptions\AgentException;
use app\components\bookings\Slot;
use Bookings_model;


/**
 * Agent handles the creation/editing/cancellation of bookings.
 *
 */
class Agent
{


	// Tpes of bookings
	const TYPE_SINGLE = 'single';
	const TYPE_MULTI = 'multi';

	// CI instance
	private $CI;

	// Status success/error message
	private $message = '';

	// Type of booking this agent instance is handling.
	private $type = FALSE;

	// View name to load
	private $view = '';

	// Current logged-in user
	private $user = FALSE;

	// Return URI after success or cancel
	private $return_uri = FALSE;

	// Session for the dates
	private $session;

	// Flag for user admin level
	private $is_admin = FALSE;

	// Extra data that can be provided to view (for render())
	private $data = [];


	// For single
	private $period;
	private $room;
	private $department;
	private $date_info;
	private $datetime;
	private $week;
	private $recurring_dates;

	// For multi
	private $periods;
	private $rooms;
	private $dates;

	// For admin
	private $all_periods;
	private $all_rooms;
	private $all_departments;
	private $all_users;



	public function __construct($type = '')
	{
		$this->CI =& get_instance();

		$this->CI->load->model([
			'bookings_model',
			'bookings_repeat_model',
			'rooms_model',
			'periods_model',
			'dates_model',
			'sessions_model',
			'departments_model',
			'users_model',
			'weeks_model',
		]);

		$valid_types = [
			self::TYPE_SINGLE,
			self::TYPE_MULTI,
			// self::TYPE_RECURRING,
		];

		$type = strtolower($type);

		if ( ! in_array($type, $valid_types)) {
			throw AgentException::forInvalidType($valid_types);
		}

		$this->type = $type;

		$this->user = $this->CI->userauth->user;
		unset($this->user->password);

		$this->is_admin = ($this->CI->userauth->is_level(ADMINISTRATOR));

		if ($this->is_admin) {
			$this->init_lists();
		}
	}


	/**
	 * Create a new instance of the agent for a given type of booking.
	 *
	 */
	public static function create($type)
	{
		return new self($type);
	}


	/**
	 * Load lists of selectable items so permitted users can select from them.
	 *
	 */
	private function init_lists()
	{
		$this->all_periods = $this->CI->periods_model->GetBookable();
		$this->all_rooms = $this->CI->rooms_model->get_bookable_rooms($this->user->user_id);
		$this->all_departments = $this->CI->departments_model->Get();
		$this->all_users = $this->CI->users_model->Get(NULL, NULL, NULL);
	}


	/**
	 * Initialise the Agent with some values.
	 *
	 * Depending on the type of booking, these will be retrieved from different places.
	 *
	 */
	public function load()
	{
		switch ($this->type) {

			case self::TYPE_SINGLE:

				$this->view = 'bookings/create/single';

				$period_id = $this->CI->input->post_get('period_id');
				if (strlen($period_id)) $this->period = $this->CI->periods_model->Get($period_id);
				if ( ! $this->period) throw AgentException::forNoPeriod();

				$room_id = $this->CI->input->post_get('room_id');
				if (strlen($room_id)) $this->room = $this->CI->rooms_model->get_bookable_rooms($this->user->user_id, $room_id);
				if ( ! $this->room) throw AgentException::forNoRoom();

				$date = $this->CI->input->post_get('date');
				$this->date_info = $this->CI->dates_model->get_by_date($date);
				if ( ! $this->date_info) throw AgentException::forInvalidDate();

				$this->week = $this->CI->weeks_model->get($this->date_info->week_id);
				if ( ! $this->week) throw AgentException::forNoWeek();

				$this->datetime = datetime_from_string($this->date_info->date);

				$department_id = $this->user->department_id;
				if ($this->is_admin && $this->CI->input->post('department_id')) {
					$department_id = $this->CI->input->post('department_id');
				}
				if (strlen($department_id)) $this->department = $this->CI->departments_model->Get($department_id);

				$this->session = $this->CI->sessions_model->get_by_date($this->datetime);
				if ( ! $this->session) throw AgentException::forNoSession();

				// List of dates that a recurring booking can begin/end on.
				$this->recurring_dates = $this->CI->dates_model->get_recurring_dates($this->session->session_id, $this->date_info->week_id, $this->date_info->weekday);

				break;

			case self::TYPE_MULTI:

				$room_ids = $this->CI->input->post('rooms');
				$period_ids = $this->CI->input->post('periods');
				$dates = $this->CI->input->post('dates');

				break;

		}

	}


	public function render()
	{
		$vars = [

			'message' => $this->message,

			'return_uri' => isset($_SESSION['return_uri']) ? $_SESSION['return_uri'] : '',
			'user' => $this->user,
			'is_admin' => $this->CI->userauth->is_level(ADMINISTRATOR),

			'recurring_dates' => $this->recurring_dates,

			'room' => $this->room,
			'period' => $this->period,
			'department' => $this->department,
			'date_info' => $this->date_info,
			'datetime' => $this->datetime,
			'week' => $this->week,

			'rooms' => $this->rooms,
			'periods' => $this->periods,
			'dates' => $this->dates,

			'all_periods' => $this->all_periods,
			'all_rooms' => $this->all_rooms,
			'all_departments' => $this->all_departments,
			'all_users' => $this->all_users,

		];

		$vars = array_merge($vars, $this->data);

		return $this->CI->load->view($this->view, $vars, TRUE);
	}


	/**
	 * Process the input and state.
	 *
	 * This should return bool TRUE for success of whole process.
	 * Returning FALSE keeps the process active.
	 *
	 */
	public function process()
	{
		// Validate data to create booking
		switch ($this->type) {
			case self::TYPE_SINGLE:
				return $this->process_single();
				break;
			case self::TYPE_MULTI:
				return $this->process_multi();
				break;
		}
	}


	/**
	 * Process form for a single booking.
	 *
	 * @return  bool TRUE on final success (to redirect) or FALSE on error or other steps.
	 *
	 */
	private function process_single()
	{
		$action = $this->CI->input->post('action');

		switch ($action) {

			// Single booking: validate / create.
			case 'create':
				return $this->create_single_booking();
				break;

			case 'preview_recurring':
				return $this->preview_single_recurring();
				break;

			case 'create_recurring':
				return $this->create_single_recurring();
				break;
		}

		// return TRUE;
	}


	/**
	 * Create a single booking.
	 *
	 */
	private function create_single_booking()
	{
		$rules = [
			['field' => 'date', 'label' => 'Date', 'rules' => 'required|valid_date'],
			['field' => 'period_id', 'label' => 'Period', 'rules' => 'required|integer'],
			['field' => 'room_id', 'label' => 'Room', 'rules' => 'required|integer'],
			['field' => 'notes', 'label' => 'Notes', 'rules' => 'max_length[255]'],
		];

		$this->CI->load->library('form_validation');
		$this->CI->form_validation->set_rules($rules);

		if ($this->CI->form_validation->run() == FALSE) {
			$this->message = 'The form contained some invalid values. Please check and try again.';
			return FALSE;
		}

		$booking_data = [
			'date' => $this->datetime->format('Y-m-d'),
			'notes' => $this->CI->input->post('notes'),
			'period_id' => $this->period->period_id,
			'room_id' => $this->room->room_id,
			'department_id' => $this->department ? $this->department->department_id : NULL,
			'session_id' => $this->session->session_id,
			'user_id' => $this->user->user_id,
		];

		if ($this->is_admin) {
			$booking_data['user_id'] = $this->CI->input->post('user_id');
		}

		$booking_id = $this->CI->bookings_model->create($booking_data);

		if ($booking_id) {
			$this->message = 'The booking has been created successfully.';
			return TRUE;
		}

		$err = $this->CI->bookings_model->get_error();

		$this->message = ($err)
			? $err
			: 'Could not create booking.';

		return FALSE;
	}


	/**
	 * Single recurring booking marked as recurring: show preview of generated bookings.
	 *
	 */
	private function preview_single_recurring()
	{
		$rules = [];

		if ($this->CI->input->post('recurring_start') == 'session') {

			$rules[] = ['field' => 'recurring_start', 'label' => 'Start date', 'rules' => 'required'];
			$recurring_start = false;

			foreach ($this->recurring_dates as $row) {
				$dt = datetime_from_string($row->date);
				if ($dt >= $this->session->date_start) {
					$recurring_start = clone $dt;
					break;
				}
			}

		} else {
			$rules[] = ['field' => 'recurring_start', 'label' => 'Start date', 'rules' => 'required|valid_date'];
			$recurring_start = $this->CI->input->post('recurring_start');
		}

		if ($this->CI->input->post('recurring_end') == 'session') {

			$rules[] = ['field' => 'recurring_end', 'label' => 'End date', 'rules' => 'required'];
			$recurring_end = false;

			foreach (array_reverse($this->recurring_dates) as $row) {
				$dt = datetime_from_string($row->date);
				if ($dt <= $this->session->date_end) {
					$recurring_end = clone $dt;
					break;
				}
			}

		} else {
			$rules[] = ['field' => 'recurring_end', 'label' => 'End date', 'rules' => 'required|valid_date'];
			$recurring_end = $this->CI->input->post('recurring_end');
		}

		$this->data['recurring_start'] = $recurring_start;
		$this->data['recurring_end'] = $recurring_end;

		$this->CI->load->library('form_validation');
		$this->CI->form_validation->set_rules($rules);

		if ($this->CI->form_validation->run() == FALSE) {
			$this->message = 'The form contained some invalid values. Please check and try again.';
			return FALSE;
		}

		$recurring_start = datetime_from_string($recurring_start);
		$recurring_end = datetime_from_string($recurring_end);


		$dates = [];
		$slots = [];

		// Generate a list of all recurring dates that the user
		// has requested to be filled, starting with the list of *all* possible
		// recurring dates.
		//
		foreach ($this->recurring_dates as $row) {
			if ($row->date < $recurring_start) continue;
			if ($row->date > $recurring_end) continue;
			$date_ymd = $row->date->format('Y-m-d');
			// List of dates for booking checking
			$dates[] = $date_ymd;
			// Slot for references
			$key = Slot::generate_key($date_ymd, $this->period->period_id, $this->room->room_id);
			$slots[$key]['datetime'] = $row->date;
		}

		// Get list of recurring bookings for these dates
		$existing_bookings = $this->CI->bookings_model->find_conflicts($dates, $this->period->period_id, $this->room->room_id);

		foreach ($slots as $key => $slot) {

			// List of possible actions for each slot
			$actions = [];

			if (array_key_exists($key, $existing_bookings)) {
				$actions['do_not_book'] = 'Keep existing booking';
				$actions['replace'] = 'Replace existing booking';
				$slots[$key]['booking'] = $existing_bookings[$key];
			} else {
				$actions['book'] = 'Book';
				$actions['do_not_book'] = 'Do not book';
			}

			$slots[$key]['actions'] = $actions;
		}

		$this->data['slots'] = $slots;

		$this->view = 'bookings/create/single_recurring_preview';

		return FALSE;
	}


	/**
	 * Create a single recurring booking.
	 *
	 */
	private function create_single_recurring()
	{
		$dates = $this->CI->input->post('dates');

		if (empty($dates)) {
			$this->message = 'No dates selected.';
			return FALSE;
		}

		$repeat_data = [
			'session_id' => $this->session->session_id,
			'period_id' => $this->period->period_id,
			'room_id' => $this->room->room_id,
			'user_id' => $this->CI->input->post('user_id'),
			'department_id' => $this->department ? $this->department->department_id : NULL,
			'week_id' => $this->date_info->week_id,
			'weekday' => $this->date_info->weekday,
			'status' => Bookings_model::STATUS_BOOKED,
			'notes' => $this->CI->input->post('notes'),
			'dates' => $dates,
		];

		$repeat_id = $this->CI->bookings_repeat_model->create($repeat_data);

		if ( ! $repeat_id) {
			$this->message = 'Could not create recurring booking.';
			return FALSE;
		}

		$this->message = 'The bookings have been created successfully.';
		return TRUE;

		// $instance_count = $this->CI->bookings_repeat_model->create_instances($repeat_id, $dates);
		// if ($instance_count > 0) {
		// 	$this->message = sprintf('The recurring booking with %d instances has been created.');
		// 	return TRUE;
		// }

		// $this->message = sprintf('The recurring booking could not be created.');
		// return FALSE;
	}


	public function set_return_uri($uri)
	{
		$this->return_uri = $uri;
		return $this;
	}


	public function __get($name)
	{
		return $this->{$name};
	}



}
