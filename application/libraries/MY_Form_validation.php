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
