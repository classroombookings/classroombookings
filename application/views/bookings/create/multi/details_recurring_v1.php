<?php

use app\components\Calendar;

// Date format of bookings
$date_format = setting('date_format_long', 'crbs');

// For period display
$time_fmt = setting('time_format_period');


// Configure table
//
$table_template = [
	'table_open' => '<table class="multibooking-table form-table" style="line-height:1.3;" width="100%" cellpadding="8" cellspacing="0" border="0">',
];

$table_heading = [
	['data' => 'Starting from', 'width' => '20%'],
	['data' => 'Ending on', 'width' => '20%'],
	['data' => 'Department', 'width' => '20%'],
	['data' => 'User', 'width' => '20%'],
	['data' => 'Notes', 'width' => '20%'],
];

$is_first = TRUE;

foreach ($multibooking->slots as $key => $slot) {

	$day_name = Calendar::get_day_name($slot->datetime->format('N'));

	$recurring_date_options = [];

	foreach ($slot->recurring_dates as $date) {
		$title = $date->date->format(setting('date_format_long'));
		$title = trim(str_replace($day_name, '', $title));
		if ($date->date->format('Y-m-d') == $slot->date) $title = '* ' . $title;
		$recurring_date_options[ $date->date->format('Y-m-d') ] = $title;
	}

	$switch_id = sprintf('recurring-slot-%d-fields', $slot->mbs_id);

	// Table caption info
	//

	// 'Create' checkbox
	$create_field = sprintf('slot_recurring[%d][create]', $slot->mbs_id);
	$create_hidden = form_hidden($create_field, 0);
	$create_check = form_checkbox([
		'id' => $create_field,
		'name' => $create_field,
		'value' => 1,
		'checked' => (set_value($create_field, 1) == 1),
		'up-switch' => '.' . $switch_id,
	]);
	$create_block = "<div class='block b-5'>{$create_hidden}{$create_check}</div>";

	$day_block = "<div class='block b-20' for='{$create_field}'>{$day_name}</div>";

	$period = $slot->period->name;
	$period_block = "<div class='block b-20'>{$period}</div>";

	$room = $slot->room->name;
	$room_block = "<div class='block b-20'>{$room}</div>";

	$caption = "<label class='block-group'>{$create_block}{$day_block}{$period_block}{$room_block}</label>";
	$this->table->set_caption($caption);


	// Table rows for setting values
	//

	// Starting from date
	//
	$recurring_start_field = sprintf('slot_recurring[%d][recurring_start]', $slot->mbs_id);
	$value = set_value($recurring_start_field, $slot->date, FALSE);
	$options = ['session' => '(Start of session)', 'Specific date...' => $recurring_date_options];
	$input = form_dropdown([
		'name' => $recurring_start_field,
		'options' => $options,
		'selected' => $value,
		'up-copy-group' => 'recurring_start',
	]);
	$input_block = "<div class='block b-90'>{$input}</div>";
	$append_block = '';
	if ($is_first) {
		$append_block = "<div class='block b-10'><button type='button' class='btn-block' up-copy-to='recurring_start'>&darr;</button></div>";
	}
	$recurring_start_col = "<div class='block-group'>{$input_block}{$append_block}</div>";

	// Ending on date
	//
	$recurring_end_field = sprintf('slot_recurring[%d][recurring_end]', $slot->mbs_id);
	$value = set_value($recurring_end_field, 'session', FALSE);
	$options = ['session' => '(End of session)', 'Specific date...' => $recurring_date_options];
	$input = form_dropdown([
		'name' => $recurring_end_field,
		'options' => $options,
		'selected' => $value,
		'up-copy-group' => 'recurring_end',
	]);
	$input_block = "<div class='block b-90'>{$input}</div>";
	$append_block = '';
	if ($is_first) {
		$append_block = "<div class='block b-10'><button type='button' class='btn-block' up-copy-to='recurring_end'>&darr;</button></div>";
	}
	$recurring_end_col = "<div class='block-group'>{$input_block}{$append_block}</div>";

	// Department column
	//
	$department_field = sprintf('slot_recurring[%d][department_id]', $slot->mbs_id);
	$options = results_to_assoc($all_departments, 'department_id', 'name', '(None)');
	$value = set_value($department_field, $department ? $department->department_id : '', FALSE);
	$input = form_dropdown([
		'name' => $department_field,
		'id' => $department_field,
		'options' => $options,
		'selected' => $value,
		'up-copy-group' => 'recurring_department_id',
	]);
	$input_block = "<div class='block b-90'>{$input}</div>";
	$append_block = '';
	if ($is_first) {
		$append_block = "<div class='block b-10'><button type='button' class='btn-block' up-copy-to='recurring_department_id'>&darr;</button></div>";
	}
	$department_col = "<div class='block-group'>{$input_block}{$append_block}</div>";

	// User column
	//
	$user_field = sprintf('slot_recurring[%d][user_id]', $slot->mbs_id);
	$options = results_to_assoc($all_users, 'user_id', function($user) {
		return strlen($user->displayname)
			? $user->displayname
			: $user->username;
	}, '(None)');
	$value = set_value($user_field, $user->user_id, FALSE);
	$input = form_dropdown([
		'name' => $user_field,
		'id' => $user_field,
		'options' => $options,
		'selected' => $value,
		'up-copy-group' => 'recurring_user_id',
	]);
	$input_block = "<div class='block b-90'>{$input}</div>";
	$append_block = '';
	if ($is_first) {
		$append_block = "<div class='block b-10'><button type='button' class='btn-block' up-copy-to='recurring_user_id'>&darr;</button></div>";
	}
	$user_col = "<div class='block-group'>{$input_block}{$append_block}</div>";

	// Notes
	//
	$notes_field = sprintf('slot_recurring[%d][notes]', $slot->mbs_id);
	$value = set_value($notes_field, '', FALSE);
	// $label = form_label('Notes', 'notes');
	$input = form_input([
		'name' => $notes_field,
		'id' => $notes_field,
		'size' => 30,
		'value' => $value,
		'up-copy-group' => 'recurring_notes',
	]);
	$input_block = "<div class='block b-90'>{$input}</div>";
	$append_block = '';
	if ($is_first) {
		$append_block = "<div class='block b-10'><button type='button' class='btn-block' up-copy-to='recurring_notes'>&darr;</button></div>";
	}
	$notes_col = "<div class='block-group'>{$input_block}{$append_block}</div>";

	$this->table->clear();

	$switch_id = sprintf('recurring-slot-%d-fields', $slot->mbs_id);

	$tpl = array_merge($table_template, [
		'thead_open' => "<thead class='{$switch_id}' up-show-for=':checked'>",
		'tbody_open' => "<tbody class='{$switch_id}' up-show-for=':checked'>",
	]);
	$this->table->set_template($tpl);
	$this->table->set_heading($table_heading);

	$this->table->add_row([
		$recurring_start_col,
		$recurring_end_col,
		$department_col,
		$user_col,
		$notes_col,
	]);

	echo "<fieldset style='border:0; padding:0; margin-bottom:16px;'>";
	echo $this->table->generate();
	echo "</fieldset>";

	if ($is_first) {
		$is_first = FALSE;
	}
}

echo "<br><br>";
