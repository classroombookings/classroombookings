<?php

use app\components\Calendar;

// Weekday name that the selected date falls on
$day_name = Calendar::get_day_name($date_info->weekday);

// List of availbale dates to choose from for recurring dates
//
$recurring_date_options = [];
if ($is_admin) {
	foreach ($recurring_dates as $date) {
		$title = $date->date->format(setting('date_format_long'));
		$title = trim(str_replace($day_name, '', $title));
		if ($date->date->format('Y-m-d') == $date_info->date) $title = '* ' . $title;
		$recurring_date_options[ $date->date->format('Y-m-d') ] = $title;
	}
}

// Form
//
$attrs = [
	'id' => 'bookings_create_single',
	'class' => 'cssform',
	'up-accept-location' => 'bookings',
	'up-layer' => 'any',
	'up-target' => '.bookings-create',
];

$hidden = [
	'room_id' => $room->room_id,
	'period_id' => $period->period_id,
	'date' => $date_info->date,
	// 'return_uri' => $return_uri,
];

if ($message) {
	echo msgbox('error', $message);
}

echo form_open(current_url(), $attrs, $hidden);

echo "<fieldset style='border:0'>";


// Date
//
$field = 'date';
$label = form_label('Date', 'date');
$input = sprintf('%s (%s)', $datetime->format(setting('date_format_long')), html_escape($week->name));
echo "<p>{$label}{$input}</p>";


// Period
//
$field = 'period_id';
$label = form_label('Period', $field);

$time_fmt = setting('time_format_period');

if ($is_admin) {
	$options = results_to_assoc($all_periods, 'period_id', function($period) use ($time_fmt) {
		$start = date($time_fmt, strtotime($period->time_start));
		$end = date($time_fmt, strtotime($period->time_end));
		return sprintf('%s (%s - %s)', $period->name, $start, $end);
	});
	$value = set_value($field, $period->period_id, FALSE);
	$input = form_dropdown([
		'name' => $field,
		'id' => $field,
		'options' => $options,
		'selected' => $value,
	]);
} else {
	$input = html_escape($period->name);
	if (!empty($time_fmt)) {
		$start = date($time_fmt, strtotime($period->time_start));
		$end = date($time_fmt, strtotime($period->time_end));
		$input .= sprintf(' <span style="font-size:90%%;color:#aaa;background:transparent">(%s - %s)</span>', $start, $end);
	}
}
echo "<p>{$label}{$input}</p>";


// Room
//
$field = 'room_id';
$label = form_label('Room', $field);
if ($is_admin) {
	$options = results_to_assoc($all_rooms, 'room_id', 'name');
	$value = set_value($field, $room->room_id, FALSE);
	$input = form_dropdown([
		'name' => $field,
		'id' => $field,
		'options' => $options,
		'selected' => $value,
	]);
} else {
	$input = html_escape($room->name);
}
echo "<p>{$label}{$input}</p>";


// Department
//
$field = 'department_id';
$label = form_label('Department', $field);
$show_department = FALSE;
if ($is_admin) {
	$show_department = TRUE;
	$options = results_to_assoc($all_departments, 'department_id', 'name', '(None)');
	$value = set_value($field, $department ? $department->department_id : '', FALSE);
	$input = form_dropdown([
		'name' => $field,
		'id' => $field,
		'options' => $options,
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
$label = form_label('Who', $field);
if ($is_admin) {
	$options = results_to_assoc($all_users, 'user_id', function($user) {
		return !empty($user->displayname)
			? $user->displayname
			: $user->username;
	}, '(None)');
	$value = set_value($field, $user->user_id, FALSE);
	$input = form_dropdown([
		'name' => $field,
		'id' => $field,
		'options' => $options,
		'selected' => $value,
	]);
} else {
	$input = !empty($user->displayname)
		? $user->displayname
		: $user->username;
}
echo "<p>{$label}{$input}</p>";


// Notes
//
$field = 'notes';
$value = set_value($field, '', FALSE);
$label = form_label('Notes', 'notes');
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
if ($is_admin) {

	$field = 'recurring';
	$value = set_value($field, '0', FALSE);

	$label = form_label('Recurring?', 'recurring');

	$hidden = form_hidden($field, '0');
	$input = form_checkbox([
		'name' => $field,
		'id' => $field,
		'value' => '1',
		'tabindex' => tab_index(),
		'checked' => ($value == '1'),
		'up-switch' => '.recurring-content',
	]);
	$input_label = form_label($input . 'Yes', 'recurring', ['class' => 'ni']);
	$error = form_error($field);

	echo "<p>{$label}{$hidden}{$input_label}</p>{$error}";

	// Recurring fields
	//
	$recurring_fields = [];

	// Info
	//
	$field = 'recurring_info';
	$label = form_label('Recurs', 'recurring_info');
	$input = sprintf('Every %s on %s', $day_name, html_escape($week->name));
	$recurring_fields[] = "<p>{$label}{$input}</p>";

	// Starting from
	//
	$field = 'recurring_start';
	$label = form_label('Starting from...', 'recurring_start');
	$value = set_value($field, $date_info->date, FALSE);
	$options = ['session' => '(Start of session)', 'Specific date...' => $recurring_date_options];
	$input = form_dropdown([
		'name' => 'recurring_start',
		'options' => $options,
		'selected' => $value,
	]);
	$recurring_fields[] = "<p>{$label}{$input}</p>";

	// Until
	//
	$field = 'recurring_end';
	$label = form_label('Until...', 'recurring_end');
	$value = set_value($field, 'session', FALSE);
	$options = ['session' => '(End of session)', 'Specific date...' => $recurring_date_options];
	$input = form_dropdown([
		'name' => 'recurring_end',
		'options' => $options,
		'selected' => $value,
	]);
	$recurring_fields[] = "<p>{$label}{$input}</p>";

	echo "<div class='recurring-content'>" . implode("\n", $recurring_fields) . "</div>";

}

echo "</fieldset>";

// Actions
//
$submit_single = form_button([
	'type' => 'submit',
	'name' => 'action',
	'value' => 'create',
	'content' => 'Create booking',
]);

$submit_recurring = form_button([
	'type' => 'submit',
	'name' => 'action',
	'value' => 'preview_recurring',
	'content' => 'Preview recurring bookings',
]);

$cancel = anchor($return_uri, 'Cancel', ['up-dismiss' => '']);

if ($is_admin) {
	echo "<div class='submit recurring-content' style='border-top:0px;' up-show-for=':unchecked'>{$submit_single} &nbsp; {$cancel}</div>";
	echo "<div class='submit recurring-content' style='border-top:0px;' up-show-for=':checked'>{$submit_recurring} &nbsp; {$cancel}</div>";
} else {
	echo "<div class='submit' style='border-top:0px;'>{$submit_single} &nbsp; {$cancel}</div>";
}



// End
echo form_close();
