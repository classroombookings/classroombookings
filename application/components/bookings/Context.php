<?php
defined('BASEPATH') OR exit('No direct script access allowed');

namespace app\components\bookings;

use \DateTime;
use \DateInterval;
use \DatePeriod;

use app\components\Calendar;
use app\components\bookings\exceptions\DateException;


class Context
{


	// CI instance
	private $CI;

	/**
	 * Params for configuration
	 *
	 */
	private $display_type = FALSE;
	private $columns = FALSE;
	private $user_id = FALSE;
	private $room_id = FALSE;
	private $session_id = FALSE;
	private $date_string = FALSE;
	private $direction = FALSE;


	/**
	 * Session row object
	 *
	 */
	private $session = FALSE;


	/**
	 * User row object for current user
	 *
	 */
	private $user = FALSE;


	/**
	 * Room row object for selected room
	 *
	 */
	private $room = FALSE;


	/**
	 * Period row objects.
	 * If $columns == day, then this is filtered to only show periods for the selected $date's weekday.
	 *
	 */
	private $periods = FALSE;


	/**
	 * Date period for the whole week of the selected date.
	 *
	 */
	private $date_period = FALSE;


	/**
	 * DateTime object representing the selected date. Populated from date_string.
	 *
	 */
	private $datetime = FALSE;


	/**
	 * Date info rows (from Dates model).
	 * Array keyed by Y-m-d.
	 *
	 */
	private $dates = FALSE;


	/**
	 * Timetable Week row for the selected date
	 *
	 */
	private $timetable_week = FALSE;


	/**
	 * Available rooms to choose from or display in the grid.
	 *
	 */
	private $rooms = FALSE;


	public function __construct()
	{
		$this->CI =& get_instance();

		$this->CI->load->model([
			'sessions_model',
			'dates_model',
			'users_model',
			'rooms_model',
			'access_control_model',
			'periods_model',
			'weeks_model',
		]);
	}


	/**
	 * Generate a default configuration from various envrionmental params.
	 *
	 */
	public function autofill()
	{
		$config = [
			'display_type' => $this->CI->settings_model->get('displaytype'),
			'columns' => $this->CI->settings_model->get('d_columns'),
			'user_id' => $this->CI->userauth->user->user_id,
			'room_id' => $this->CI->input->get('room_id'),
			'session_id' => isset($_SESSION['working_session_id'])
				? $_SESSION['working_session_id']
				: NULL,
			'date_string' => $this->CI->input->get('date')
				? $this->CI->input->get('date')
				: date('Y-m-d'),
			'direction' => $this->CI->input->get('direction'),
		];

		$config = array_filter($config, 'strlen');

		$this->init($config);
	}


	public function init($config = [])
	{
		$default = [
			'display_type' => NULL,
			'columns' => NULL,
			'user_id' => NULL,
			'room_id' => NULL,
			'session_id' => NULL,
			'date_string' => NULL,
			'direction' => NULL,
		];

		$data = array_merge($default, $config);
		// $data = array_filter($data, 'strlen');

		foreach ($data as $key => $val) {
			if (isset($this->$key)) {
				$this->$key = $val;
			}
		}

		$this->load_data();
	}


	private function load_data()
	{
		if ($this->session_id) {
			$this->session = $this->CI->sessions_model->get($this->session_id);
		} else {
			$this->session = $this->CI->sessions_model->get_current();
		}

		// Get and parse the requested date
		if ($this->date_string) {
			$this->datetime = datetime_from_string($this->date_string);
		}

		if ($this->datetime) {

			// Get week boundaries
			$week_starts_day_name = Calendar::get_day_names()[ Calendar::get_first_day_of_week() ];
			$week_start = clone $this->datetime;
			$week_start->modify('+1 day');
			$week_start->modify("last {$week_starts_day_name}");
			$week_end = clone $week_start;
			$week_end->modify('+6 days');

			// Get the Dates data for the week
			$this->dates = $this->CI->dates_model->with_period_count()->get_by_range($week_start, $week_end);

			// Get DatePeriod for week
			// @TODO might not be necessary if we can just iterate over $this->dates ?
			$interval = new DateInterval('P1D');
			$week_end->modify('+1 day');
			$this->date_period = new DatePeriod($week_start, $interval, $week_end);

			// Get Timetable Week
			$date_key = $this->datetime->format('Y-m-d');
			$date_info = isset($this->dates[$date_key]) ? $this->dates[$date_key] : FALSE;
			if ($date_info && $date_info->week_id) {
				$this->timetable_week = $this->CI->weeks_model->get($date_info->week_id);
			}

			// Remove entries with no periods
			// @TODO might not need to do this if we can access period_count var when looping?
			foreach ($this->dates as $date => $item) {
				if ($item->period_count == 0) {
					unset($this->dates[$date]);
				}
			}

		}

		if ($this->user_id) {
			$this->user = $this->CI->users_model->get_by_id($this->user_id);
		}

		if ($this->room_id) {
			$this->room = $this->CI->rooms_model->Get($room_id);
		}

		$this->rooms = $this->CI->rooms_model->get_bookable_rooms($this->user->user_id);
	}


	public function validate()
	{
		// if ( ! $this->datetime) {
		// 	throw DateException::invalidDate($this->date_string);
		// }

		// if ($this->datetime < $this->session->date_start || $this->datetime > $this->session->date_end) {
		// 	throw DateException::forSessionRange($this->datetime);
		// }
	}


	public function toArray()
	{
		$vars = get_object_vars($this);
		unset($vars['CI']);
		return $vars;
	}



}
