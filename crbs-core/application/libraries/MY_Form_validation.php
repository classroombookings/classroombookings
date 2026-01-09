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
			$this->set_message('valid_date', lang('validation.valid_date.error'));
			return FALSE;
		}

		return TRUE;
	}


	public function valid_time($value)
	{
		$am = strtotime('00:00');
		$pm = strtotime('23:59');
		$ts = strtotime((string) $value);

		$has_ts = !empty($ts);
		$is_after = $ts >= $am;
		$is_before = $ts <= $pm;

		if ( ! $has_ts) {
			$this->set_message('valid_time', lang('validation.valid_time.not_provided'));
			return FALSE;
		}

		if ( ! $is_after) {
			$this->set_message('valid_time', lang('validation.valid_time.not_after'));
			return FALSE;
		}

		if ( ! $is_before) {
			$this->set_message('valid_time', lang('validation.valid_time.not_before'));
			return FALSE;
		}

		return TRUE;
	}


	public function time_is_after($value, $earlier_field)
	{
		$earlier_value = $this->_field_data[$earlier_field]['postdata'];
		if (empty($earlier_value)) return TRUE;

		$earlier_ts = strtotime((string) $earlier_value);
		$ts = strtotime((string) $value);

		if ($ts < $earlier_ts) {
			$this->set_message('time_is_after', lang('validation.time_is_after.error'));
			return FALSE;
		}

		return TRUE;
	}


	public function date_is_after($value, $earlier_field)
	{
		$earlier_value = $this->_field_data[$earlier_field]['postdata'];

		if (empty($earlier_value)) return true;

		$earlier = datetime_from_string($earlier_value);
		$later = datetime_from_string($value);

		if ($later > $earlier) return true;

		$this->set_message('date_is_after', lang('validation.date_is_after.error'));
		return FALSE;
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
		$booking_card_url = site_url('bookings/card/' . $conflict->booking_id);

		$msg = lang('validation.no_conflict.error');
		$view = lang('app.action.view_details');
		$link = sprintf('%s <a href="%s" up-target=".bookings-card" up-layer="new popup" up-size="medium">%s</a>', $msg, $booking_card_url, $view);

		$this->set_message('no_conflict', $link);
		return FALSE;
	}


	public function is_not_current_password($value, $user_id)
	{
		$this->CI->load->model('users_model');
		$user = $this->CI->users_model->get_by_id($user_id);
		$current_hash = $user->password;
		$is_current = password_verify((string) $value, (string) $current_hash);
		if ($is_current) {
			$this->set_message('is_not_current_password', lang('validation.is_not_current_password.error'));
			return false;
		}

		return true;
	}


}
