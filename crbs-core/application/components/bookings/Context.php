<?php

namespace app\components\bookings;

defined('BASEPATH') OR exit('No direct script access allowed');


use \DateTime;
use \DateInterval;
use \DatePeriod;

use app\components\Calendar;
use app\components\bookings\exceptions\DateException;
use app\components\bookings\exceptions\SessionException;
use app\components\bookings\exceptions\SettingsException;
use app\components\bookings\exceptions\AvailabilityException;


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
	private $rows = FALSE;
	private $user_id = FALSE;
	private $room_id = FALSE;
	private $room_group_id = FALSE;
	private $session_id = FALSE;
	private $base_uri = FALSE;
	private $date_string = FALSE;
	private $direction = FALSE;

	/**
	 * Date for today.
	 *
	 */
	private $today = FALSE;

	/**
	 * Session row object
	 *
	 */
	private $session = FALSE;

	/**
	 * Lists of sessions
	 *
	 */
	private $available_sessions = FALSE;
	private $past_sessions = FALSE;
	private $active_sessions = FALSE;

	/**
	 * Holidays for the session, indexed by ID.
	 *
	 */
	private $holidays = FALSE;

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
	 * Room Group row object for selected room group
	 *
	 */
	private $room_group = FALSE;

	/**
	 * Schedule row object
	 *
	 */
	private $schedule = FALSE;

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
	 * List of all Timetable Weeks
	 *
	 */
	private $weeks = FALSE;

	/**
	 * Date info rows (from Dates model).
	 * Array keyed by Y-m-d.
	 *
	 */
	private $dates = FALSE;

	/**
	 * Info about the selected date.
	 *
	 */
	private $date_info = FALSE;

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

	/**
	 * List of room groups
	 *
	 */
	private $room_groups = FALSE;

	/**
	 * Exception thrown during validation of context data.
	 *
	 */
	private $exception = FALSE;

	/**
	 * Start date of week
	 *
	 */
	private $week_start = FALSE;

	/**
	 * End date of week
	 *
	 */
	private $week_end = FALSE;

	/**
	 * Navigation: Previous date
	 *
	 */
	private $prev_date = FALSE;

	/**
	 * Navigation: Next date
	 *
	 */
	private $next_date = FALSE;

	/**
	 * The slots for the context's range.
	 *
	 */
	private $slots;

	/**
	 * Bookings for the context's range, indeed by their corresponding slot key.
	 *
	 */
	private $bookings = FALSE;


	public function __construct()
	{
		$this->CI =& get_instance();

		$this->CI->load->model([
			'bookings_model',
			'sessions_model',
			'holidays_model',
			'dates_model',
			'users_model',
			'rooms_model',
			'room_groups_model',
			'access_control_model',
			'schedules_model',
			'periods_model',
			'weeks_model',
		]);
	}


	/**
	 * Generate a default configuration from various envrionmental params.
	 *
	 */
	public function autofill($params = [])
	{
		$config = [
			'display_type' => $this->CI->settings_model->get('displaytype'),
			'columns' => $this->CI->settings_model->get('d_columns'),
			'user_id' => $this->CI->userauth->user->user_id,
			'room_id' => $this->CI->input->get('room'),
			'room_group_id' => $this->CI->input->get('room_group'),
			'session_id' => isset($_SESSION['current_session_id'])
				? $_SESSION['current_session_id']
				: NULL,
			'date_string' => $this->CI->input->get('date')
				? $this->CI->input->get('date')
				: date('Y-m-d'),
			'direction' => $this->CI->input->get('dir'),
			'base_uri' => 'bookings',
		];

		$config = array_filter($config, function($var) { return !empty($var); });
		$params = array_filter($params, function($var) { return !empty($var); });

		$config = array_merge($config, $params);

		$this->init($config);
	}


	public function init($config = [])
	{
		$default = [
			'display_type' => NULL,
			'columns' => NULL,
			'user_id' => NULL,
			'room_id' => NULL,
			'room_group_id' => NULL,
			'session_id' => NULL,
			'date_string' => NULL,
			'direction' => NULL,
		];

		$data = array_merge($default, $config);

		// Set object properties from config array
		foreach ($data as $key => $val) {
			if (isset($this->$key)) {
				$this->$key = $val;
			}
		}

		// Determine the rows on the grid, based on configured display_type and columns.
		//

		$rows = [
			'room.periods' => 'days',
			'room.days' => 'periods',
			'day.periods' => 'rooms',
			'day.rooms' => 'periods',
		];

		$key = sprintf('%s.%s', $this->display_type, $this->columns);

		$this->rows = (array_key_exists($key, $rows))
			? $rows[$key]
			: FALSE;

		$this->today = new DateTime();

		// Initialise other section
		//
		$this->init_user();
		$this->init_session();
		$this->init_weeks();
		$this->init_date_time();
		$this->init_room_groups();
		$this->init_rooms();
		$this->init_schedule();
		$this->init_periods();
		$this->init_navigation();
		$this->find_bookings();
		$this->init_slots();


		// Validate the loaded data and set exceptions if found.
		//
		try {
			$this->validate();
		} catch (\Exception $e) {
			$this->exception = $e;
		}
	}


	/**
	 * Load the requested session.
	 *
	 */
	private function init_session()
	{
		$allow_any = FALSE;

		// Load list of selectable sessions
		$this->available_sessions = $this->CI->sessions_model->get_selectable();


		if ($this->user && $this->user->authlevel == ADMINISTRATOR) {
			$this->past_sessions = $this->CI->sessions_model->get_all_past();
			$this->active_sessions = $this->CI->sessions_model->get_all_active();
			$allow_any = TRUE;
		}

		switch (true) {

			case ($allow_any && !empty($this->session_id)):
				$this->session = $this->CI->sessions_model->get($this->session_id);
				break;

			case ($allow_any && is_array($this->active_sessions) && ! empty($this->active_sessions)):
				$this->session = $this->active_sessions[0];
				break;

			case ( ! $allow_any && !empty($this->session_id)):
				$this->session = $this->CI->sessions_model->get_available_session($this->session_id);
				break;

			default:
				$this->session = $this->CI->sessions_model->get_current();

		}

		if ( ! $this->session) return;

		// Selected session is not the current one.
		// Need to check if requested date is within it or not.
		if ($this->session->is_current == 0) {

			$first_bookable_date = $this->CI->dates_model->first_bookable_date($this->session->session_id);

			if ( ! $first_bookable_date) {
				// Can't do anything else here.
				// Likely that no weeks are set up in the session
				return;
			}

			// No date at all? Get first bookable date in session
			if ( ! $this->date_string) {
				$this->date_string = $first_bookable_date;
			}

			// Check the date we have is within range of the session.
			$datetime = datetime_from_string($this->date_string);
			if ($datetime < $this->session->date_start || $datetime > $this->session->date_end) {
				$this->date_string = $first_bookable_date;
			}
		}

		$holidays = $this->CI->holidays_model->get_by_session($this->session->session_id);

		$this->holidays = [];

		if (empty($holidays)) return;

		foreach ($holidays as $holiday) {
			$this->holidays[ $holiday->holiday_id ] = $holiday;
		}
	}


	/**
	 * Load all weeks, so we can generate the required CSS for calendar views
	 *
	 */
	private function init_weeks()
	{
		$weeks = $this->CI->weeks_model->get_all();
		$this->weeks = [];
		if (empty($weeks)) return;
		foreach ($weeks as $week) {
			$this->weeks[ $week->week_id ] = $week;
		}
	}


	/**
	 * Get and parse the requested date.
	 *
	 * Load things that depend on the datetime, if we have it.
	 *
	 */
	private function init_date_time()
	{
		if ( ! $this->date_string) return;

		$this->datetime = datetime_from_string($this->date_string);

		if ( ! $this->datetime) return;

		// Get week boundaries
		$week_starts_day_name = Calendar::get_day_names()[ Calendar::get_first_day_of_week() ];
		$week_start = clone $this->datetime;
		$week_start->modify('+1 day');
		$week_start->modify("last {$week_starts_day_name}");

		// If the start of this week is before the session, move it.
		if ($this->session && $week_start < $this->session->date_start) {
			$week_start = clone $this->session->date_start;
		}

		$week_end = clone $week_start;
		while ($week_end->format('N') < 7) {
			$week_end->modify('+1 days');
		}

		$this->week_start = $week_start;
		$this->week_end = $week_end;

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
		if ($date_info && $date_info->week_id && isset($this->weeks[$date_info->week_id])) {
			$this->timetable_week = $this->weeks[$date_info->week_id];
		}

		$this->date_info = $date_info;

		// Remove entries with no periods
		// @TODO might not need to do this if we can access period_count var when looping?
		foreach ($this->dates as $date => $item) {
			if ($item->period_count == 0) {
				unset($this->dates[$date]);
			}
		}
	}


	/**
	 * Load the schedule (for periods) based on session and room group.
	 *
	 */
	private function init_schedule()
	{
		if ( ! $this->session) return;
		if ( ! $this->room_group) return;
		$session_id = $this->session->session_id;
		$room_group_id = $this->room_group->room_group_id;
		$this->schedule = $this->CI->schedules_model->get_applied_schedule($session_id, $room_group_id);
	}


	/**
	 * Populate periods
	 *
	 */
	private function init_periods()
	{
		if ( ! $this->datetime) return;
		if ( ! $this->schedule) return;

		switch ($this->display_type) {

			// Get periods for the current date only
			case 'day':
				$this->periods = $this->CI->periods_model->filtered([
					'schedule_id' => $this->schedule->schedule_id,
					'day' => $this->datetime->format('N'),
					'bookable' => 1,
				]);
				break;

			// Get all periods
			case 'room':
				$this->periods = $this->CI->periods_model->filtered([
					'schedule_id' => $this->schedule->schedule_id,
					'bookable' => 1,
				]);
				break;

			default:
				// None
		}
	}


	/**
	 * Populate user if we have an ID (should be logged-in user)
	 *
	 */
	private function init_user()
	{
		if ($this->user_id) {
			$this->user = $this->CI->users_model->get_by_id($this->user_id);
		}
	}


	private function init_room_groups()
	{
		$user_id = $this->user
			? $this->user->user_id
			: NULL;

		$this->room_groups = $this->CI->room_groups_model->get_bookable($user_id);

		while ($this->display_type == 'day' && $this->room_group === FALSE && ! empty($this->room_groups)) {

			if ($this->room_group_id && isset($this->room_groups[ $this->room_group_id ])) {
				$this->room_group = $this->room_groups[ $this->room_group_id ];
				break;
			}

			$this->room_group = current($this->room_groups);
			break;
		}
	}


	/**
	 * Load rooms that the user can access as well as the requested room.
	 *
	 */
	private function init_rooms()
	{
		$user_id = $this->user
			? $this->user->user_id
			: NULL;

		$this->rooms = $this->CI->rooms_model->get_bookable_rooms([
			'user_id' => $user_id,
			'room_group_id' => ($this->room_group === FALSE)
				? NULL
				: $this->room_group->room_group_id,
		]);


		// Load the requested room if required
		//

		while ($this->display_type == 'room' && $this->room === FALSE) {

			// Get it from the passed-in ID
			if ($this->room_id && isset($this->rooms[$this->room_id])) {
				$this->room = $this->rooms[$this->room_id];
				break;
			}

			// Get it from one belonging to user
			if ($this->user) {
				$room = $this->CI->rooms_model->GetByUser($this->user->user_id);
				if ($room && $room->room_id && isset($this->rooms[$room->room_id])) {
					$this->room = $room;
					break;
				}
			}

			// Get it from first entry of $this->rooms
			if (count($this->rooms) > 0) {
				$this->room = current($this->rooms);
				break;
			}

			break;
		}

		// Load the room group
		if ($this->room && ! empty($this->room->room_group_id)) {
			$this->room_group = $this->CI->room_groups_model->get($this->room->room_group_id);
		}
	}



	/**
	 * Find the previous and next dates for navigation.
	 *
	 */
	private function init_navigation()
	{
		$dates = [];

		switch ($this->display_type) {

			case 'day':
				$dates = $this->CI->dates_model->get_prev_next($this->datetime, 'day');
				break;

			case 'room':
				$dates = $this->CI->dates_model->get_prev_next($this->datetime, 'week');
				break;

			default:
				// None
		}

		$this->prev_date = isset($dates['prev'])
			? datetime_from_string($dates['prev']->date)
			: FALSE;

		$this->next_date = isset($dates['next'])
			? datetime_from_string($dates['next']->date)
			: FALSE;

		/*
		if ($this->display_type == 'room') {

			// To get the prev/next dates for week navigation,
			// modify the week boundary we already know about by 1 day.
			// That new date will be within the boundary of the prev/next week.

			$this->prev_date = clone $this->week_start;
			$this->prev_date->modify('-1 day');

			if ($this->prev_date < $this->session->date_start) {
				$this->prev_date = FALSE;
			}

			$this->next_date = clone $this->week_end;
			$this->next_date->modify('+1 day');

			if ($this->next_date > $this->session->date_end) {
				$this->next_date = FALSE;
			}

			return;
		}*/
	}


	/**
	 * Get all slots for the current view
	 *
	 */
	private function init_slots()
	{
		if ( ! $this->session) return;
		if ( ! $this->datetime) return;

		$slots = [];

		$rooms = ($this->display_type == 'room')
			? [ $this->room ]
			: $this->rooms;

		$date_key = $this->datetime->format('Y-m-d');

		$dates = ($this->display_type == 'room')
			? $this->dates
			: [ $date_key => $this->date_info ];

		$periods = (is_array($this->periods))
			? $this->periods
			: [];

		foreach ($dates as $date_info) {

			if ($date_info === FALSE) continue;

			foreach ($periods as $period) {

				foreach ($rooms as $room) {

					$slot = new Slot($this, $date_info, $period, $room);
					$slots[ $slot->key ] = $slot;

				}

			}

		}

		$this->slots = $slots;
	}


	/**
	 * Find the bookings that are relevant to the current view
	 *
	 */
	private function find_bookings()
	{
		if ( ! $this->datetime) return;

		if ($this->session) {
			$this->bookings = $this->CI->bookings_model->find_for_context($this);
		}
	}

	/**
	 * Run some checks against the loaded context.
	 *
	 */
	private function validate()
	{
		if ( ! $this->datetime) {
			throw DateException::invalidDate($this->date_string);
		}

		if ( ! $this->session) {
			throw SessionException::notSelected();
		}

		if ($this->datetime < $this->session->date_start || $this->datetime > $this->session->date_end) {
			throw DateException::forSessionRange($this->datetime);
		}

		if ( ! in_array($this->display_type, ['day', 'room'])) {
			throw SettingsException::forDisplayType();
		}

		if ( ! in_array($this->columns, ['periods', 'rooms', 'days'])) {
			throw SettingsException::forColumns();
		}

		if (empty($this->rooms)) {
			throw SettingsException::forNoRooms();
		}

		if ( ! $this->timetable_week) {
			throw AvailabilityException::forNoWeek();
		}

		if ( ! $this->schedule) {
			throw SettingsException::forNoSchedule();
		}

		if ($this->display_type == 'day' && $this->date_info) {

			if ($this->date_info->period_count == 0) {
				throw AvailabilityException::forNoPeriods();
			}

			if ($this->date_info->holiday_id) {
				$holiday = $this->CI->holidays_model->get($this->date_info->holiday_id);
				throw AvailabilityException::forHoliday($holiday);
			}

		}
	}


	/**
	 * Return the essential query params that will be used when navigating or loading data.
	 *
	 */
	public function get_query_params()
	{
		$vars = [
			'date' => $this->datetime ? $this->datetime->format('Y-m-d') : NULL,
			'dir' => $this->direction,
			'room' => $this->room ? $this->room->room_id : NULL,
			'room_group' => $this->room_group ? $this->room_group->room_group_id : NULL,
		];

		return array_filter($vars, function($var) { return !empty($var); });
	}


	public function toArray()
	{
		$vars = get_object_vars($this);
		unset($vars['CI']);
		return $vars;
	}


	public function __get($name)
	{
		return $this->{$name};
	}



}
