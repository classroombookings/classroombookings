<?php

use app\components\bookings\Slot;


class MY_Form_validation extends CI_Form_validation
{

	public $CI;

	public function __construct($rules = array())
	{
		parent::__construct($rules);

		$this->CI =& get_instance();
	}


	public function valid_date($value)
	{
		$dt = datetime_from_string($value);

		if ( ! $dt) {
			$this->set_message('valid_date', '{field} must be a valid date.');
			return FALSE;
		}

		return TRUE;
	}


	public function valid_time($value)
	{
		$am = strtotime('00:00');
		$pm = strtotime('23:59');
		$ts = strtotime($value);

		$has_ts = !empty($ts);
		$is_after = $ts >= $am;
		$is_before = $ts <= $pm;

		if ( ! $has_ts) {
			$this->set_message('valid_time', 'Time must be provided.');
			return FALSE;
		}

		if ( ! $is_after) {
			$this->set_message('valid_time', 'Time must be after 00:00');
			return FALSE;
		}

		if ( ! $is_before) {
			$this->set_message('valid_time', 'Time must be before 23:59');
			return FALSE;
		}

		return TRUE;
	}


	public function time_is_after($value, $earlier_field)
	{
		$earlier_value = $this->_field_data[$earlier_field]['postdata'];
		if (empty($earlier_value)) return TRUE;

		$earlier_ts = strtotime($earlier_value);
		$ts = strtotime($value);

		if ($ts < $earlier_ts) {
			$this->set_message('time_is_after', 'Time must be after the earlier time.');
			return FALSE;
		}

		return TRUE;
	}


	/**
	 * Should be applied to the 'date' field for a booking.
	 *
	 */
	public function no_conflict($value, $booking_id)
	{
		$date = datetime_from_string($value);
		$period_id = $this->_field_data['period_id']['postdata'];
		$room_id = $this->_field_data['room_id']['postdata'];

		// No valid date
		if ( ! $date) return FALSE;

		$date_ymd = $date->format('Y-m-d');

		$this->CI->load->model('bookings_model');
		$conflicts = $this->CI->bookings_model->find_conflicts([$date_ymd], $period_id, $room_id);

		// No conflicts
		if (empty($conflicts)) return TRUE;

		$conflict = current($conflicts);

		// Conflict is ourself: booking has not moved.
		if ($conflict->booking_id == $booking_id) return TRUE;

		// Include details in error message.
		$booking_card_uri = site_url('bookings/card/' . $conflict->booking_id);
		$msg = sprintf('Another booking already exists. <a href="%s" up-target=".bookings-card" up-layer="new popup" up-size="medium">View details</a>.',
			$booking_card_uri
		);

		$this->set_message('no_conflict', $msg);
		return FALSE;
	}


}
