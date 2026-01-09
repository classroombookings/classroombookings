<?php

$hidden = [
	'room_id' => $room->room_id,
	'period_id' => $period->period_id,
	'date' => $date_info->date,
];

echo form_hidden($hidden);

$none = sprintf('(%s)', lang('app.none'));

echo "<fieldset style='border:0'>";

// Date
//
$field = 'date';
$label = form_label(lang('app.date'), 'date');
$input = sprintf('%s (%s)', date_output_long($datetime), html_escape($week->name));
echo "<p>{$label}{$input}</p>";


// Period
//
$field = 'period_id';
$label = form_label(lang('period.period'), $field);
$options = results_to_assoc($all_periods, 'period_id', function($period) {
	$start = date_output_time($period->time_start);
	$end = date_output_time($period->time_end);
	return sprintf('%s (%s - %s)', $period->name, $start, $end);
});
$value = set_value($field, $period->period_id, FALSE);
$input = form_dropdown([
	'name' => $field,
	'id' => $field,
	'options' => $options,
	'selected' => $value,
]);
echo "<p>{$label}{$input}</p>";


// Room
//
$field = 'room_id';
$label = form_label(lang('room.room'), $field);
$input = html_escape($room->name);
echo "<p>{$label}{$input}</p>";


// Department
//
$field = 'department_id';
$label = form_label(lang('department.department'), $field);
$show_department = FALSE;
if ($can_set_department) {
	$show_department = TRUE;
	$options = results_to_assoc($all_departments, 'department_id', 'name', $none);
	$value = set_value($field, $department ? $department->department_id : '', FALSE);
	$input = form_dropdown([
		'name' => $field,
		'id' => $field,
		'options' => html_escape($options),
		'selected' => $value,
	]);
} else {
	if (!empty($department)) {
		$show_department = TRUE;
		$input = html_escape($department->name);
	}
}
echo ($show_department)
	? "<p>{$label}{$input}</p>"
	: '';

// Who
//
$field = 'user_id';
$label = form_label(lang('booking.booked_by'), $field);
if ($can_set_user) {
	$options = results_to_assoc($all_users, 'user_id', fn($user) => !empty($user->displayname)
			? $user->displayname
			: $user->username, $none);
	$value = set_value($field, $user->user_id, FALSE);
	$input = form_dropdown([
		'name' => $field,
		'id' => $field,
		'options' => html_escape($options),
		'selected' => $value,
	]);
} else {
	$input = !empty($user->displayname)
		? $user->displayname
		: $user->username;
	$input = html_escape($input);
}
echo "<p>{$label}{$input}</p>";


// Notes
//
$field = 'notes';
$value = set_value($field, '', FALSE);
$label = form_label(lang('booking.notes'), 'notes');
$input = form_textarea([
	'autofocus' => 'true',
	'name' => $field,
	'id' => $field,
	'rows' => '3',
	'cols' => '50',
	'tabindex' => tab_index(),
	'value' => $value,
]);
echo sprintf("<p>%s%s</p>%s", $label, $input, form_error($field));

echo "</fieldset>";

// Actions
//
$submit = form_button([
	'type' => 'submit',
	'name' => 'action',
	'value' => 'create',
	'content' => '&check; ' . lang('booking.add.single.action'),
]);

$cancel = anchor($return_uri, lang('app.action.cancel'), ['up-dismiss' => '']);

echo "<div class='submit' style='border-top:0px;'>{$submit} &nbsp; {$cancel}</div>";
