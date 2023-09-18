<?php

defined('BASEPATH') OR exit('No direct script access allowed');



function booking_editable($booking)
{
	$CI =& get_instance();

	if ( ! $booking) return FALSE;

	$is_admin = $CI->userauth->is_level(ADMINISTRATOR);
	if ($is_admin) return TRUE;

	$is_booking_owner = ($CI->userauth->user->user_id == $booking->user_id);
	if ($is_booking_owner) return TRUE;

	return FALSE;
}


function booking_cancelable($booking)
{
	$CI =& get_instance();

	$is_admin = $CI->userauth->is_level(ADMINISTRATOR);
	if ($is_admin) return TRUE;

	$today = (new DateTime());

	// Check for time if we have it
	$is_past = ($booking->time_end instanceof \DateTime && $today > $booking->time_end);
	if ($is_past) return FALSE;

	// Check for past date
	$today->setTime(0, 0, 0);
	if ($booking->date < $today) return FALSE;

	$is_booking_owner = ($CI->userauth->user->user_id == $booking->user_id);
	$is_room_owner = ($CI->userauth->user->user_id == $booking->room->user_id && ! $booking->repeat_id);

	if ($is_booking_owner || $is_room_owner) return TRUE;

	return FALSE;
}


function booking_status_label($booking)
{
	switch ($booking->status) {
		case Bookings_model::STATUS_BOOKED:
			$label = 'Booked';
			break;
		case Bookings_model::STATUS_CANCELLED:
			$label = 'Cancelled';
			break;
	}

	return $label;
}

function booking_status_icon($booking)
{
	switch ($booking->status) {
		case Bookings_model::STATUS_BOOKED:
			$label = 'enabled.png';
			break;
		case Bookings_model::STATUS_CANCELLED:
			$label = 'delete.png';
			break;
	}

	return $label;
}
