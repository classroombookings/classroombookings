<?php

if ( ! isset($active)) $active = NULL;

$items = [];

if (!isset($session)) {
	$items[] = ['sessions', 'Sessions', 'calendar_view_month.png'];
} else {
	$items[] = ['sessions', 'All sessions', 'arrow_turn_left.png'];
	$items[] = ['sessions/view/' . $session->session_id, 'Weeks', 'calendar_view_day.png'];
	$items[] = ['sessions/edit/' . $session->session_id, 'Edit', 'edit.png'];
	$items[] = ['room_schedules/session/' . $session->session_id, 'Schedules', 'school_manage_times.png'];
	$items[] = ['holidays/session/' . $session->session_id, 'Holidays', 'school_manage_holidays.png'];
}

echo iconbar($items, $active);
