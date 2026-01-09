<?php

namespace app\components\bookings\agent;

defined('BASEPATH') OR exit('No direct script access allowed');


use Permission;


/**
 * Booking Agent base class for handling creation and editing of bookings.
 *
 */
abstract class BaseAgent
{

	const AGENT_MODE = null;

	// Booking types
	const BOOK_SINGLE = 'single';
	const BOOK_RECUR = 'recurring';


	// CI instance
	protected $CI;

	// Page title to show
	protected $title = '';

	// Status success/error message
	protected $message = '';

	// Type of booking this agent instance is handling.
	protected $agent_mode = null;

	// Type of booking being made (single or recurring)
	protected ?string $booking_type = null;

	// View name to load
	protected $view = '';

	// Current logged-in user
	protected $user = null;

	// Session for the dates
	protected $session;

	// Marker for completion of process
	protected $success = false;

	// Extra data that can be provided to view (for render())
	protected $view_data = [];

	// Main view with form
	protected string $subview = '';

	// Lists of lookups to select from
	protected array $all_periods = [];
	protected array $all_rooms = [];
	protected array $all_departments = [];
	protected array $all_users = [];


	public function __construct()
	{
		$this->CI =& get_instance();

		$this->CI->load->model([
			'bookings_model',
			'bookings_repeat_model',
			'multi_booking_model',
			'rooms_model',
			'periods_model',
			'dates_model',
			'sessions_model',
			'schedules_model',
			'departments_model',
			'users_model',
			'weeks_model',
		]);

		$this->CI->load->library('table');

		$this->agent_mode = self::AGENT_MODE;

		// Initialise user
		$this->user = $this->CI->userauth->user;
		unset($this->user->password);

		$this->detect_booking_type();
	}


	protected function detect_booking_type()
	{
		$sess_key = sprintf('%s.booking_type', $this->agent_mode);

		if (isset($_SESSION[$sess_key])) {
			$this->booking_type = $_SESSION[$sess_key];
		}
		if ($this->CI->input->post_get('booking_type')) {
			$this->set_booking_type($this->CI->input->post_get('booking_type'));
		}
	}


	protected function set_booking_type(string $type)
	{
		$sess_key = sprintf('%s.booking_type', $this->agent_mode);
		$this->booking_type = $type;
		$_SESSION[$sess_key] = $type;
	}


	/**
	 * Create a new instance of the agent for a given type of booking.
	 *
	 */
	public static function create()
	{
		return new static();
	}


	public function is_success()
	{
		return ($this->success === TRUE);
	}


	protected function populate_departments()
	{
		$departments = $this->CI->departments_model->Get(NULL, NULL, NULL);
		if (is_array($departments)) {
			$this->all_departments = $departments;
		}
	}


	protected function populate_users()
	{
		$users = $this->CI->users_model->Get(NULL, NULL, NULL);
		if (is_array($users)) {
			$this->all_users = $users;
		}
	}


	protected function get_view_data()
	{
		return [];
	}


	/**
	 * Get the vars and load the chosen view ($this->view.
	 *
	 */
	public function render()
	{
		$default_vars = [

			'message' => $this->message,

			'return_uri' => $_SESSION['return_uri'] ?? '',
			'user' => $this->user,
			'booking_type' => $this->booking_type,
			'agent_mode' => $this->agent_mode,
			'subview' => $this->subview,

			'all_periods' => $this->all_periods,
			'all_rooms' => $this->all_rooms,
			'all_departments' => $this->all_departments,
			'all_users' => $this->all_users,

		];

		$vars = array_merge($default_vars, $this->get_view_data(), $this->view_data);

		return $this->CI->load->view($this->view, $vars, TRUE);
	}


	protected function get_actions($booking)
	{
		$actions = [];

		$user_is_owner = ($booking->user_id == $this->user->user_id);

		if ($user_is_owner) {
			$actions['replace'] = lang('booking.action.replace');
			$actions['do_not_book'] = lang('booking.action.keep');
			return $actions;
		}

		$booking_type = empty($booking->repeat_id) ? 'single' : 'recurring';
		$actions['do_not_book'] = lang('booking.action.keep');

		switch ($booking_type) {
			case 'single':
				if (has_permission(Permission::BK_SGL_CANCEL_OTHER, $booking->room_id)) {
					$actions['replace'] = lang('booking.action.replace');
				}
				break;
			case 'recurring':
				if (has_permission(Permission::BK_RECUR_CANCEL_OTHER, $booking->room_id)) {
					$actions['replace'] = lang('booking.action.replace');
				}
				break;
		}

		return $actions;
	}


	/**
	 * Overwrite in subclass to handle requests.
	 *
	 */
	public function process()
	{
		return FALSE;
	}


	public function __get($name)
	{
		return $this->{$name};
	}



}
