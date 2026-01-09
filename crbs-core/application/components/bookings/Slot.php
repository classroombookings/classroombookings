<?php

namespace app\components\bookings;

defined('BASEPATH') OR exit('No direct script access allowed');


use \DateTime;

use app\components\Calendar;
use Permission;


/**
 * Represents the status/details for an individual timeslot: date + period + room
 *
 */
class Slot
{


	const STATUS_AVAILABLE = 'available';
	const STATUS_UNAVAILABLE = 'unavailable';
	const STATUS_BOOKED = 'booked';

	const BOOKED_SINGLE = 'single';
	const BOOKED_RECURRING = 'recurring';

	const UNAVAILABLE_HOLIDAY = 'holiday';
	const UNAVAILABLE_DATE_RANGE = 'date_range';
	const UNAVAILABLE_PERIOD = 'period';
	const UNAVAILABLE_LIMIT = 'limit';
	const UNAVAILABLE_UNKNOWN = 'unknown';
	const UNAVAILABLE_PERMISSIONS = 'permissions';
	const UNAVAILABLE_RANGE_MIN = 'range_min';
	const UNAVAILABLE_RANGE_MAX = 'range_max';


	// CI instance
	private $CI;

	// Context instance
	private $context;

	private $date;
	private $period;
	private $room;

	private $key = false;

	// Main status
	private $status;
	// Reason name for unavailability
	private $reason;
	// Label related to reasoning
	private $label;

	private $view_data = [];

	// Booking, if slot booked.
	private ?object $booking;

	// DateTime instance created from the passed date
	private DateTime $datetime;

	// Whether to continue with further status checks
	private bool $continue;



	public function __construct(Context $context, $date, $period, $room)
	{
		$this->CI =& get_instance();

		$this->context = $context;

		$this->date = $date;
		$this->period = $period;
		$this->room = $room;
		$this->booking = null;

		$this->init();
		$this->set_status();
	}


	private function init()
	{
		$this->datetime = datetime_from_string($this->date->date);

		if ($this->date && $this->period && $this->room) {
			$this->key = self::generate_key($this->date->date, $this->period->period_id, $this->room->room_id);
		}

		if ($this->key && array_key_exists($this->key, $this->context->bookings)) {
			$this->booking = $this->context->bookings[ $this->key ];
		}
	}


	/**
	 * Check various pieces of data to determine if this slot is bookable or not.
	 *
	 * Uses various cues, including:
	 * 	- holidays
	 * 	- period time/day
	 * 	- existing bookings
	 * 	- user's permissions / constraints
	 *
	 */
	private function set_status()
	{
		// Default
		// $this->status = self::STATUS_UNAVAILABLE;
		// $this->reason = self::UNAVAILABLE_LIMIT;

		$this->continue = true;
		$this->check_holiday();
		$this->check_valid_period();
		$this->checked_booked();
		$this->check_free_range();
		$this->check_free_constraints();

		if ($this->continue) {
			$this->status = self::STATUS_AVAILABLE;
		}
	}


	private function check_holiday()
	{
		if ($this->continue === false) return;

		if ($this->date->holiday_id) {

			// Date is on a holiday

			$this->continue = false;

			$this->status = self::STATUS_UNAVAILABLE;
			$this->reason = self::UNAVAILABLE_HOLIDAY;

			$holiday_id = $this->date->holiday_id;
			$holiday = $this->context->holidays[$holiday_id] ?? FALSE;

			$date_fmt = 'd/m/Y';

			$this->label = $holiday
				? sprintf("Holiday: %s<br>(%s - %s)",
		          	$holiday->name,
		          	$holiday->date_start->format($date_fmt),
		          	$holiday->date_end->format($date_fmt)
		          	)
				: 'Holiday';

			return;
		}
	}


	/**
	 * Check if period is valid
	 *
	 */
	private function check_valid_period()
	{
		if ($this->continue === false) return;

		// Check if period is valid.
		$period_key = sprintf('day_%d', $this->date->weekday);

		if ($this->period->{$period_key} != 1) {

			// Period is not applicable on this weekday

			$this->continue = false;

			$day_names = Calendar::get_day_names();
			$day_name = $day_names["{$this->date->weekday}"];
			$lang_key = sprintf('cal_%s', strtolower((string) $day_name));
			$day_name_lang = lang($lang_key);

			$this->status = self::STATUS_UNAVAILABLE;
			$this->reason = self::UNAVAILABLE_PERIOD;

			$line = lang('booking.error.period_wrong_day');

			$this->label = sprintf($line, $this->period->name, $day_name_lang);

		}
	}


	private function checked_booked()
	{
		if ($this->continue === false) return;

		if ( ! is_null($this->booking)) {

			// Existing booking

			$this->continue = false;

			$this->status = self::STATUS_BOOKED;
			$this->reason = ($this->booking->repeat_id)
				? self::BOOKED_RECURRING
				: self::BOOKED_SINGLE;

			$this->view_data = [
				'booking' => $this->booking,
			];

			// Set some more permission-based flags for the view

			$is_recurring = !empty($this->booking->repeat_id);
			$room_id = $this->booking->room_id;

			if ($this->booking->user_id == $this->context->user->user_id) {
				$this->view_data['show_user'] = true;
				$this->view_data['show_notes'] = true;
			} else {
				$this->view_data['show_user'] = ($is_recurring)
					? has_permission(Permission::BK_RECUR_VIEW_OTHER_USERS, $room_id)
					: has_permission(Permission::BK_SGL_VIEW_OTHER_USERS, $room_id)
					;
				$this->view_data['show_notes'] = ($is_recurring)
					? has_permission(Permission::BK_RECUR_VIEW_OTHER_NOTES, $room_id)
					: has_permission(Permission::BK_SGL_VIEW_OTHER_NOTES, $room_id)
					;
			}
		}
	}


	private function check_free_range()
	{
		if ($this->continue === false) return;

		$create_single = has_permission(Permission::BK_SGL_CREATE);
		$create_recur = has_permission(Permission::BK_RECUR_CREATE);

		// Users with these permission everywhere can create bookings at any point in the session
		if ($create_single || $create_recur) {
			$start_date = $this->context->session->date_start;
		} else {
			$start_date = ($this->context->session->is_current == '1')
				? $this->context->today
				: $this->context->session->date_start
				;
		}

		$end_date = $this->context->session->date_end;

		$is_in_range = ($this->datetime >= $start_date && $this->datetime <= $end_date);

		if ( ! $is_in_range) {
			// Date is not in future
			$this->continue = false;
			$this->status = self::STATUS_UNAVAILABLE;
			$this->reason = self::UNAVAILABLE_DATE_RANGE;
			$this->label = lang('booking.error.date_not_in_range');
			return;
		}
	}


	private function check_free_constraints()
	{
		if ($this->continue === false) return;

		$user_constraints = $this->CI->users_model->get_constraints($this->context->user->user_id);
		$user_limit = $user_constraints['max_active_bookings'];
		// Check for a limit
		if ( ! is_null($user_limit)) {
			$booking_count = $this->CI->users_model->get_scheduled_booking_count($this->context->user->user_id);
			if ($booking_count >= $user_limit) {
				$this->continue = false;
				$this->status = self::STATUS_UNAVAILABLE;
				$this->reason = self::UNAVAILABLE_LIMIT;
				$line = lang('booking.error.constraint.max_reached');
				$this->label = sprintf($line, $booking_count);
				return;
			}
		}

		// Check range
		//

		$create_single = has_permission(Permission::BK_SGL_CREATE, $this->room->room_id);
		$create_recur = has_permission(Permission::BK_RECUR_CREATE, $this->room->room_id);

		$this->view_data['allow_single'] = $create_single;
		$this->view_data['allow_recur'] = $create_recur;

		if ($create_single === false && $create_recur === false) {
			// Quick exit here because they have no permissions
			$this->continue = false;
			$this->status = self::STATUS_UNAVAILABLE;
			$this->reason = self::UNAVAILABLE_PERMISSIONS;
			$this->label = sprintf(lang('booking.error.no_permission_room'));
			return;
		}

		if ($create_single && $create_recur) {
			// If a user can create single AND recurring, we should keep this slot available.
			// Further checks to be done durinbg creation process.
			return;
		}

		if ($create_recur && ! $create_single) {
			// If a user can create recurring bookings but NOT singles, we should keep this slot as available.
			// Further checks should be done duriung creation process.
			return;
		}

		if ($create_single && ! $create_recur) {

			// Checks for singlt-booking-only permissions.
			//

			// Check for range if they can create single bookings
			$min_days = $user_constraints['range_min'];
			$max_days = $user_constraints['range_max'];

			$min_date = clone $this->context->today;
			if (!is_null($min_days)) {
				$min_date = $min_date->modify("+{$min_days} days");
			}

			if ($this->datetime < $min_date) {
				$this->continue = false;
				$this->status = self::STATUS_UNAVAILABLE;
				$this->reason = self::UNAVAILABLE_RANGE_MIN;
				$this->label = sprintf(lang('booking.error.constraint.range_min_only'), $user_constraints['range_min']);
				return;
			}

			$max_date = clone $this->context->session->date_end;
			if (!is_null($max_days)) {
				$max_date = clone $this->context->today;
				$max_date->modify("+{$max_days} days");
			}

			if ($this->datetime > $max_date) {
				$this->continue = false;
				$this->status = self::STATUS_UNAVAILABLE;
				$this->reason = self::UNAVAILABLE_RANGE_MAX;
				$this->label = sprintf(lang('booking.error.constraint.range_max_only'), $user_constraints['range_max']);
				return;
			}
		}
	}


	/**
	 * Generate the unique key for this slot.
	 *
	 */
	public static function generate_key($date_ymd, $period_id, $room_id)
	{
		return sprintf('%s.P%d.R%d', $date_ymd, $period_id, $room_id);
	}


	public function __get($name)
	{
		return $this->{$name};
	}



}
