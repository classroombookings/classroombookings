<?php

use app\components\Calendar;
use app\components\TabPane;

// Date format of bookings
$date_format = setting('date_format_long', 'crbs');

// For period display
$time_fmt = setting('time_format_period');


echo "<fieldset>";

echo "<div style='margin-bottom:16px'>Enter the default values for each recurring booking. You can change these for each booking on the next step.</div>";

// Department
//
$field = 'department_id';
$label = form_label('Department', $field);
$options = results_to_assoc($all_departments, 'department_id', 'name', '(None)');
$value = set_value($field, $department ? $department->department_id : '', FALSE);
$input = form_dropdown([
	'name' => $field,
	'id' => $field,
	'options' => $options,
	'selected' => $value,
]);
echo sprintf("<p>%s%s</p>%s", $label, $input, form_error($field));


// Who
//
$field = 'user_id';
$label = form_label('Who', $field);
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
echo sprintf("<p>%s%s</p>%s", $label, $input, form_error($field));


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

// Recurring start from
//
$field = 'recurring_start';
$value = set_value($field, 'date', FALSE);
$label = form_label('Starting from...', 'recurring_start');
$options = ['session' => 'Start of session', 'date' => 'Selected date(s)'];
$input = form_dropdown([
	'name' => 'recurring_start',
	'options' => $options,
	'selected' => $value,
]);
echo sprintf("<p>%s%s</p>%s", $label, $input, form_error($field));

// Recurring end date
//
$field = 'recurring_end';
$label = form_label('Until...', 'recurring_end');
$value = set_value($field, 'session', FALSE);
$options = ['session' => 'End of session', 'date' => 'Selected date(s)'];
$input = form_dropdown([
	'name' => 'recurring_end',
	'options' => $options,
	'selected' => $value,
]);
echo sprintf("<p>%s%s</p>%s", $label, $input, form_error($field));

echo "</fieldset>";
