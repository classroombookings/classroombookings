<?php

// Date format of bookings
$date_format = setting('date_format_long', 'crbs');

// For period display
$time_fmt = setting('time_format_period');


// Generate table of bookings
//
$this->table->set_template([
	'table_open' => '<table class="zebra-table form-table multibooking-table" style="line-height:1.3;margin-bottom:16px" width="100%" cellpadding="8" cellspacing="0" border="0">',
]);


$is_first = TRUE;

// Get columns for table
//

foreach ($multibooking->slots as $key => $slot) {
	$dates[] = $slot->date;
	$rooms[] = $slot->room_id;
}

$show_date_col = (count(array_unique($dates)) == 1)
	? FALSE
	: TRUE;

$show_room_col = (count(array_unique($rooms)) == 1)
	? FALSE
	: TRUE;

$cols = [];
$cols[] = ['data' => '', 'width' => 10];

if ($show_date_col) {
	$cols[] = ['data' => 'Date'];
} else {
	$cols[] = ['data' => 'Period'];
}

if ($show_room_col) {
	$cols[] = ['data' => 'Room'];
}

if ($is_admin) {
	$cols[] = ['data' => 'Department'];
	$cols[] = ['data' => 'User'];
}

$cols[] = ['data' => 'Notes'];

$this->table->set_heading($cols);

// Generate rows
//

foreach ($multibooking->slots as $key => $slot) {

	// 'Create' checkbox col
	//
	$create_field = sprintf('slot_single[%d][create]', $slot->mbs_id);
	$create_hidden = form_hidden($create_field, 0);
	$create_check = form_checkbox([
		'id' => $create_field,
		'name' => $create_field,
		'value' => 1,
		'checked' => (set_value($create_field, 1) == 1),
	]);
	$check_col = $create_hidden . $create_check;

	// Date column
	//
	if ($show_date_col) {
		$date = "<div>" . $slot->datetime->format($date_format) . "</div>";
		$period = '<small class="hint">' . $slot->period->name . '</span>';
		$date_col = form_label($date . $period, $create_field, ['class' => 'ni']);
	} else {
		$date_col = form_label($slot->period->name, $create_field, ['class' => 'ni']);
	}

	// Department column
	//
	$department_field = sprintf('slot_single[%d][department_id]', $slot->mbs_id);
	$options = results_to_assoc($all_departments, 'department_id', 'name', '(None)');
	$value = set_value($department_field, $department ? $department->department_id : '', FALSE);
	$input = form_dropdown([
		'name' => $department_field,
		'id' => $department_field,
		'options' => $options,
		'selected' => $value,
		'up-copy-group' => 'department_id',
	]);
	$input_block = "<div class='block b-90'>{$input}</div>";
	$append_block = '';
	if ($is_first) {
		$append_block = "<div class='block b-10'><button type='button' class='btn-block' up-copy-to='department_id'>&darr;</button></div>";
	}
	$department_col = "<div class='block-group'>{$input_block}{$append_block}</div>";


	// User column
	//
	$user_field = sprintf('slot_single[%d][user_id]', $slot->mbs_id);
	$options = results_to_assoc($all_users, 'user_id', function($user) {
		return !empty($user->displayname)
			? $user->displayname
			: $user->username;
	}, '(None)');
	$value = set_value($user_field, $user->user_id, FALSE);
	$input = form_dropdown([
		'name' => $user_field,
		'id' => $user_field,
		'options' => $options,
		'selected' => $value,
		'up-copy-group' => 'user_id',
	]);
	$input_block = "<div class='block b-90'>{$input}</div>";
	$append_block = '';
	if ($is_first) {
		$append_block = "<div class='block b-10'><button type='button' class='btn-block' up-copy-to='user_id'>&darr;</button></div>";
	}
	$user_col = "<div class='block-group'>{$input_block}{$append_block}</div>";

	// Notes
	//
	$notes_field = sprintf('slot_single[%d][notes]', $slot->mbs_id);
	$value = set_value($notes_field, '', FALSE);
	// $label = form_label('Notes', 'notes');
	$input = form_input([
		'name' => $notes_field,
		'id' => $notes_field,
		'size' => 30,
		'value' => $value,
		'up-copy-group' => 'notes',
	]);
	$input_block = "<div class='block b-90'>{$input}</div>";
	$append_block = '';
	if ($is_first) {
		$append_block = "<div class='block b-10'><button type='button' class='btn-block' up-copy-to='notes'>&darr;</button></div>";
	}
	$notes_col = "<div class='block-group'>{$input_block}{$append_block}</div>";

	// Add row
	//
	$row = [];
	$row[] = $check_col;
	$row[] = $date_col;
	if ($show_room_col) $row[] = $slot->room->name;
	if ($is_admin) {
		$row[] = $department_col;
		$row[] = $user_col;
	}
	$row[] = $notes_col;

	$this->table->add_row($row);

	if ($is_first) {
		$is_first = FALSE;
	}
}

if ( ! $show_room_col || ! $show_date_col) {

	echo "<fieldset style='padding-top:0'>";
	if ( ! $show_date_col) {
		$date_str = $slot->datetime->format($date_format);
		echo "<p><label>Date</label>{$date_str}</p>";
	}
	if ( ! $show_room_col) {
		$room_str = html_escape($slot->room->name);
		echo "<p><label>Room</label>{$room_str}</p>";
	}
	echo "</fieldset>";
}

echo "<fieldset style='border:0; padding:0;'>";
echo $this->table->generate();
echo "</fieldset>";
