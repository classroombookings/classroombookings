<?php
use app\components\Calendar;

$hidden = [
	'room_id' => $room->room_id,
	'period_id' => $period->period_id,
	'date' => $date_info->date,
];

echo form_hidden($hidden);

$none = sprintf('(%s)', lang('app.none'));

// Weekday name that the selected date falls on
$day_name = Calendar::get_day_name($date_info->weekday);
$lang_key = sprintf('cal_%s', strtolower((string) $day_name));
$day_name_lang = lang($lang_key);

// List of availbale dates to choose from for recurring dates
//
$recurring_date_options = [];
if ($recurring_dates !== false) {
	foreach ($recurring_dates as $date) {
		$title = date_output_long($date->date);
		$title = trim(str_replace($day_name, '', $title));
		$title = trim(str_replace($day_name_lang, '', $title));
		$title = ltrim($title, '\s,');
		if ($date->date->format('Y-m-d') == $date_info->date) $title = '* ' . $title;
		$recurring_date_options[ $date->date->format('Y-m-d') ] = $title;
	}
}

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
	if ($department) {
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

// Recurring?
//

$field = 'recurring';
$value = set_value($field, '1', FALSE);


// Recurring fields
//
$recurring_fields = [];

// Info
//
$field = 'recurring_info';
$label = form_label(lang('booking.recurs'), 'recurring_info');
$input = sprintf(lang('booking.recurring.repeat_description'), $day_name_lang, html_escape($week->name));
$recurring_fields[] = "<p>{$label}{$input}</p>";

// Starting from
//
$field = 'recurring_start';
$label = form_label(lang('booking.recurring.starting_from'), 'recurring_start');
$value = set_value($field, $date_info->date, FALSE);
$options = [
	'session' => sprintf('(%s)', lang('booking.recurring.start_of_session')),
	lang('booking.recurring.specific_date') => $recurring_date_options,
];
$input = form_dropdown([
	'name' => 'recurring_start',
	'options' => $options,
	'selected' => $value,
]);
$recurring_fields[] = "<p>{$label}{$input}</p>";

// Until
//
$field = 'recurring_end';
$label = form_label(lang('booking.recurring.until'), 'recurring_end');
$value = set_value($field, 'session', FALSE);
$options = [
	'session' => sprintf('(%s)', lang('booking.recurring.end_of_session')),
	lang('booking.recurring.specific_date') => $recurring_date_options,
];
$input = form_dropdown([
	'name' => 'recurring_end',
	'options' => $options,
	'selected' => $value,
]);
$recurring_fields[] = "<p>{$label}{$input}</p>";

echo "<div class='recurring-content'>" . implode("\n", $recurring_fields) . "</div>";

//

echo "</fieldset>";

// Actions
//

$submit = form_button([
	'type' => 'submit',
	'name' => 'action',
	'value' => 'preview_recurring',
	'content' => lang('booking.recurring.preview') . ' &rarr;',
]);

$cancel = anchor($return_uri, lang('app.action.cancel'), ['up-dismiss' => '']);

echo "<div class='submit' style='border-top:0px;'>{$submit} &nbsp; {$cancel}</div>";
