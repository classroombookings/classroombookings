<?php

use app\components\BookingActionChecker;
use app\permissions\BookingPermissions;

defined('BASEPATH') OR exit('No direct script access allowed');



function booking_editable($booking)
{
	if ( ! $booking) return FALSE;

	$CI =& get_instance();

	// Owner check
	//
	$user_id = $booking->user_id;
	if ($user_id == $CI->userauth->user->user_id) {
		return true;
	}

	$permission_name = $booking->repeat_id
		? Permission::BK_RECUR_EDIT_OTHER
		: Permission::BK_SGL_EDIT_OTHER
		;

	return has_permission($permission_name, $booking->room_id);
}


function booking_cancelable($booking)
{
	$permission_name = $booking->repeat_id
		? Permission::BK_RECUR_CANCEL_OTHER
		: Permission::BK_SGL_CANCEL_OTHER
		;

	// Check permission *without* room provided.
	// This allows users that have the permission assigned at the Role (likely admins) to cancel it without any other restrictions
	if (has_permission($permission_name)) {
		return true;
	}

	$today = (new DateTime());

	// Today: Check for time if we have it
	$is_past = ($booking->time_end instanceof \DateTime && $today > $booking->time_end);
	if ($is_past) {
		return false;
	}

	// Check for past date
	$today->setTime(0, 0, 0);
	if ($booking->date < $today) {
		return false;
	}

	$CI =& get_instance();

	// Owner check
	$user_id = $booking->user_id;
	if ($user_id == $CI->userauth->user->user_id) {
		return true;
	}

	return has_permission($permission_name, $booking->room_id);
}


function booking_user_viewable($booking)
{
	$CI =& get_instance();

	// Owner check
	//
	$user_id = $booking->user_id;
	if ($user_id == $CI->userauth->user->user_id) {
		return true;
	}

	$permission_name = $booking->repeat_id
		? Permission::BK_RECUR_VIEW_OTHER_USERS
		: Permission::BK_SGL_VIEW_OTHER_USERS
		;

	return has_permission($permission_name, $booking->room_id);
}


function booking_notes_viewable($booking)
{
	$CI =& get_instance();

	// Owner check
	//
	$user_id = $booking->user_id;
	if ($user_id == $CI->userauth->user->user_id) {
		return true;
	}

	$permission_name = $booking->repeat_id
		? Permission::BK_RECUR_VIEW_OTHER_NOTES
		: Permission::BK_SGL_VIEW_OTHER_NOTES
		;

	return has_permission($permission_name, $booking->room_id);
}


function booking_status_label($booking)
{
	$label = match ($booking->status) {
		Bookings_model::STATUS_BOOKED => lang('booking.status.booked'),
		Bookings_model::STATUS_CANCELLED => lang('booking.status.cancelled'),
		default => $booking->status,
	};

	return $label;
}

function booking_status_icon($booking)
{
	$label = match ($booking->status) {
		Bookings_model::STATUS_BOOKED => 'enabled.png',
		Bookings_model::STATUS_CANCELLED => 'delete.png',
		default => $booking->status,
	};

	return $label;
}
