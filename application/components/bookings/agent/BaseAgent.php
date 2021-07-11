<?php
defined('BASEPATH') OR exit('No direct script access allowed');

namespace app\components\bookings\agent;

use app\components\bookings\exceptions\AgentException;
use app\components\bookings\Slot;
use \Bookings_model;


/**
 * Booking Agent base class for handling creation of bookings.
 *
 */
abstract class BaseAgent
{

	const TYPE = null;

	// Tpes of bookings
	const TYPE_SINGLE = 'single';
	const TYPE_MULTI = 'multi';


	// CI instance
	protected $CI;

	// Page title to show
	protected $title = '';

	// Status success/error message
	protected $message = '';

	// Type of booking this agent instance is handling.
	protected $type = FALSE;

	// View name to load
	protected $view = '';

	// Current logged-in user
	protected $user = FALSE;

	// Session for the dates
	protected $session;

	// Flag for user admin level
	protected $is_admin = FALSE;

	// Marker for completion of process
	protected $success = FALSE;

	// Extra data that can be provided to view (for render())
	protected $view_data = [];

	// For admin (to select from)
	protected $all_periods;
	protected $all_rooms;
	protected $all_departments;
	protected $all_users;


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
			'departments_model',
			'users_model',
			'weeks_model',
		]);

		$this->CI->load->library('table');

		$this->type = self::TYPE;

		// Initialise user
		$this->user = $this->CI->userauth->user;
		unset($this->user->password);

		$this->is_admin = ($this->CI->userauth->is_level(ADMINISTRATOR));

		$this->init_lists();
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


	/**
	 * Load lists of selectable items so permitted users can select from them.
	 *
	 */
	protected function init_lists()
	{
		if ( ! $this->is_admin) return;

		$this->all_periods = $this->CI->periods_model->GetBookable();
		$this->all_rooms = $this->CI->rooms_model->get_bookable_rooms($this->user->user_id);
		$this->all_departments = $this->CI->departments_model->Get();
		$this->all_users = $this->CI->users_model->Get(NULL, NULL, NULL);
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

			'return_uri' => isset($_SESSION['return_uri']) ? $_SESSION['return_uri'] : '',
			'user' => $this->user,
			'is_admin' => $this->is_admin,

			'all_periods' => $this->all_periods,
			'all_rooms' => $this->all_rooms,
			'all_departments' => $this->all_departments,
			'all_users' => $this->all_users,

		];

		$vars = array_merge($default_vars, $this->get_view_data(), $this->view_data);

		return $this->CI->load->view($this->view, $vars, TRUE);
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
