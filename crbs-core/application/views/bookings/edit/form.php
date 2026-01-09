<?php

use app\components\bookings\agent\UpdateAgent;


// Form
//
$attrs = [
	'id' => 'bookings_edit',
	'class' => 'cssform',
	'up-layer' => 'current root',
	'up-target' => '.bookings-edit',
];

$hidden = [
	'booking_id' => $booking->booking_id,
	'edit' => $edit_mode,
];

if ($message) {
	echo msgbox('error', $message);
}

echo validation_errors();

echo form_open(current_url(), $attrs, $hidden);

if ($booking->repeat_id) {

	$msg = '';

	switch ($edit_mode) {
		case UpdateAgent::EDIT_ONE:
			$msg = lang('booking.edit.single.hint');
			break;
		case UpdateAgent::EDIT_FUTURE:
			$msg = lang('booking.edit.future.hint');
			break;
		case UpdateAgent::EDIT_ALL:
			$msg = lang('booking.edit.all.hint');
			break;
	}

	echo "<div style='margin-bottom:16px'>{$msg}</div>";
}

echo "<fieldset style='border:0'>";

$datetime = datetime_from_string($booking->date);
$none = sprintf('(%s)', lang('app.none'));

// Date
//
$field = 'booking_date';
$label = form_label(lang('app.date'), $field);
if ($features[UpdateAgent::FEATURE_DATE]) {
	$input = form_input(array(
		'name' => $field,
		'id' => $field,
		'size' => '10',
		'maxlength' => '10',
		'tabindex' => tab_index(),
		'value' => set_value($field, $datetime ? $datetime->format('d/m/Y') : '', FALSE),
	));
	$input .= date_picker_img($field);
} else {
	$input = sprintf('%s (%s)', date_output_long($datetime), html_escape($booking->week->name));
	if ($edit_mode != UpdateAgent::EDIT_ONE) {
		$input .= ' (' . lang('booking.and_others'). ')';
	}
}
echo "<p>{$label}{$input}</p>";


// Period
//
$field = 'period_id';
$label = form_label(lang('period.period'), $field);

if ($features[UpdateAgent::FEATURE_PERIOD]) {
	$options = results_to_assoc($all_periods, 'period_id', function($period) {
		$start = date_output_time($period->time_start);
		$end = date_output_time($period->time_end);
		return sprintf('%s (%s - %s)', $period->name, $start, $end);
	});
	$value = set_value($field, $booking->period_id, FALSE);
	$input = form_dropdown([
		'name' => $field,
		'id' => $field,
		'options' => $options,
		'selected' => $value,
	]);
} else {
	$input = html_escape($booking->period->name);
	$start = date_output_time($booking->period->time_start);
	$end = date_output_time($booking->period->time_end);
	$input .= sprintf(' <span style="font-size:90%%;color:#aaa;background:transparent">(%s - %s)</span>', $start, $end);
}
echo "<p>{$label}{$input}</p>";


// Room
//
$field = 'room_id';
$label = form_label(lang('room.room'), $field);
if ($features[UpdateAgent::FEATURE_ROOM]) {
	$options = results_to_assoc($all_rooms, 'room_id', 'name');
	$value = set_value($field, $booking->room_id, FALSE);
	$input = form_dropdown([
		'name' => $field,
		'id' => $field,
		'options' => $options,
		'selected' => $value,
	]);
} else {
	$input = html_escape($booking->room->name);
}
echo "<p>{$label}{$input}</p>";


// Department
//
$field = 'department_id';
$label = form_label(lang('department.department'), $field);
$show_department = FALSE;
if ($features[UpdateAgent::FEATURE_DEPARTMENT]) {
	$show_department = TRUE;
	$options = results_to_assoc($all_departments, 'department_id', 'name', $none);
	$value = set_value($field, $booking->department_id, FALSE);
	$input = form_dropdown([
		'name' => $field,
		'id' => $field,
		'options' => $options,
		'selected' => $value,
	]);
} else {
	if ($booking->department_id) {
		$show_department = TRUE;
		$input = html_escape($booking->department->name);
	}
}
echo ($show_department)
	? "<p>{$label}{$input}</p>"
	: '';

// Who
//
$field = 'user_id';
$label = form_label(lang('booking.booked_by'), $field);
if ($features[UpdateAgent::FEATURE_EDIT_USER]) {
	$options = results_to_assoc($all_users, 'user_id', fn($user) => !empty($user->displayname)
			? $user->displayname
			: $user->username, $none);
	$value = set_value($field, $booking->user_id, FALSE);
	$input = form_dropdown([
		'name' => $field,
		'id' => $field,
		'options' => $options,
		'selected' => $value,
	]);
} elseif ($features[UpdateAgent::FEATURE_VIEW_USER] && !empty($booking->user)) {
	$input = !empty($booking->user->displayname)
		? $booking->user->displayname
		: $booking->user->username;
} else {
	$input = '<em>' . lang('app.not_available') . '</em>';
}
echo "<p>{$label}{$input}</p>";


// Notes
//
$field = 'notes';
$value = set_value($field, $booking->notes, FALSE);
$label = form_label(lang('booking.notes'), 'notes');
if ($features[UpdateAgent::FEATURE_EDIT_NOTES]) {
	$input = form_textarea([
		'name' => $field,
		'id' => $field,
		'rows' => '3',
		'cols' => '50',
		'tabindex' => tab_index(),
		'value' => $value,
	]);
} elseif ($features[UpdateAgent::FEATURE_VIEW_NOTES]) {
	$input = '<span>' . html_escape($booking->notes) . '</span>';
} else {
	$input = '<em>' . lang('app.not_available') . '</em>';
}
echo sprintf("<p>%s%s</p>%s", $label, $input, form_error($field));



echo "</fieldset>";

// Actions
//
$submit = form_button([
	'type' => 'submit',
	'name' => 'action',
	'value' => 'update',
	'content' => lang('booking.edit.action'),
]);

$cancel = anchor($return_uri, lang('app.action.cancel'), ['up-dismiss' => '']);

echo "<div class='submit' style='border-top:0px;'>{$submit} &nbsp; {$cancel}</div>";


// End
echo form_close();
