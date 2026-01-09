<?php

if ( ! isset($active)) $active = NULL;

$items = [];

if (!isset($session)) {
	$items[] = ['sessions', lang('session.sessions'), 'calendar_view_month.png'];
} else {
	$items[] = ['sessions', lang('session.all_sessions'), 'arrow_turn_left.png'];
	$items[] = ['sessions/view/' . $session->session_id, lang('session.weeks'), 'calendar_view_day.png'];
	$items[] = ['sessions/edit/' . $session->session_id, lang('session.update'), 'edit.png'];
	$items[] = ['room_schedules/session/' . $session->session_id, lang('session.schedules'), 'school_manage_times.png'];
	$items[] = ['holidays/session/' . $session->session_id, lang('session.holidays'), 'school_manage_holidays.png'];
}

echo iconbar($items, $active);
