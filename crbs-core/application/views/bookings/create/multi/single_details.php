<?php

$is_first = true;
$is_first_department = true;
$is_first_user = true;

// Generate table of bookings
//
$this->table->set_template([
	'table_open' => '<table
		class="zebra-table form-table multibooking-table"
		style="line-height:1.3;margin-bottom:16px"
		width="100%"
		cellpadding="8"
		cellspacing="0"
		border="0"
	>',
]);

// Get columns for table
//
$dates = [];
$rooms = [];
if (is_array($multibooking->slots)) {
	foreach ($multibooking->slots as $key => $slot) {
		$dates[] = $slot->date;
		$rooms[] = $slot->room_id;
	}
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
	$cols[] = ['data' => lang('app.date')];
} else {
	$cols[] = ['data' => lang('period.period')];
}
if ($show_room_col) {
	$cols[] = ['data' => lang('room.room')];
}
$cols[] = ['data' => lang('department.department')];
$cols[] = ['data' => lang('user.user')];
$cols[] = ['data' => lang('booking.notes')];

$this->table->set_heading($cols);

// Generate rows
//

$allowed_booking_count = 0;

if (is_array($multibooking->slots)) {

	foreach ($multibooking->slots as $key => $slot) {

		$capabilities = $slot->capabilities;

		// 'Create' checkbox col
		//
		$create_field = sprintf('slot_single[%d][create]', $slot->mbs_id);
		$create_hidden = form_hidden($create_field, 0);
		$check_props = [
			'id' => $create_field,
			'name' => $create_field,
			'value' => 1,
			'checked' => (set_value($create_field, 1) == 1),
		];
		if (!$capabilities['single.create']) {
			$check_props['disabled'] = '';
			$check_props['checked'] = false;
		} else {
			$allowed_booking_count++;
		}
		$create_check = form_checkbox($check_props);
		$check_col = $create_hidden . $create_check;

		// Date column
		//
		if ($show_date_col) {
			$date = "<div>" . date_output_long($slot->datetime) . "</div>";
			$period_text = sprintf('<small class="hint">%s (%s - %s)</small>',
				html_escape($slot->period->name),
				date_output_time($slot->period->time_start),
				date_output_time($slot->period->time_end)
			);
			$date_col = form_label($date . $period_text, $create_field, ['class' => 'ni']);
		} else {
			$period_text = sprintf('<div>%s</div><small class="hint">(%s - %s)</small>',
				html_escape($slot->period->name),
				date_output_time($slot->period->time_start),
				date_output_time($slot->period->time_end)
			);
			$date_col = form_label($period_text, $create_field, ['class' => 'ni']);
		}

		// Department column
		//
		if ($capabilities['single.set_department']) {
			$department_field = sprintf('slot_single[%d][department_id]', $slot->mbs_id);
			$value = set_value($department_field, $department ? $department->department_id : '', FALSE);
			$input = form_dropdown([
				'name' => $department_field,
				'id' => $department_field,
				'options' => html_escape($department_options),
				'selected' => $value,
				'up-copy-group' => 'department_id',
			]);
			$input_block = "<div class='block b-90'>{$input}</div>";
			$append_block = '';
			if ($is_first_department) {
				$append_block = "<div class='block b-10'><button type='button' class='btn-block' up-copy-to='department_id'>&darr;</button></div>";
			}
			$department_col = "<div class='block-group'>{$input_block}{$append_block}</div>";
		} else {
			$department_label = sprintf('(%s)', lang('app.none'));
			if (isset($department) && ! empty($department)) {
				$department_label = html_escape($department->name);
			}
			$department_col = "<div class='block-group'>{$department_label}</div>";
		}


		// User column
		//
		if ($capabilities['single.set_user']) {
			$user_field = sprintf('slot_single[%d][user_id]', $slot->mbs_id);
			$value = set_value($user_field, $user->user_id, FALSE);
			$input = form_dropdown([
				'name' => $user_field,
				'id' => $user_field,
				'options' => $user_options,
				'selected' => $value,
				'up-copy-group' => 'user_id',
			]);
			$input_block = "<div class='block b-90'>{$input}</div>";
			$append_block = '';
			if ($is_first_user) {
				$append_block = "<div class='block b-10'><button type='button' class='btn-block' up-copy-to='user_id'>&darr;</button></div>";
			}
			$user_col = "<div class='block-group'>{$input_block}{$append_block}</div>";
		} else {
			$user_label = sprintf('(%s)', lang('app.none'));
			if (isset($user) && ! empty($user)) {
				$user_label = !empty($user->displayname)
					? $user->displayname
					: $user->username
					;
				$user_label = html_escape($user_label);
			}
			$user_col = "<div class='block-group'>{$user_label}</div>";
		}

		// Notes
		//
		if ($capabilities['single.create']) {
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
		} else {
			$notes_col = msgbox('notice is-solo large', lang('booking.error.no_permission_room_date'));
		}

		// Room
		//
		$room_col = html_escape($slot->room->name);

		// Add row
		//
		$row = [];
		$row[] = $check_col;
		$row[] = $date_col;
		if ($show_room_col) {
			// $row[] = html_escape($slot->room->name);
			$row[] = $room_col;
		}
		$row[] = $department_col;
		$row[] = $user_col;
		$row[] = $notes_col;

		$this->table->add_row($row);

		if ($is_first) $is_first = false;
		if ($is_first_department) $is_first_department = false;
		if ($is_first_user) $is_first_user = false;
	}
}


// Notice
//
if ( ! is_null($user_permitted_booking_count)) {
	if ($allowed_booking_count > $user_permitted_booking_count) {
		if ($user_booking_count == 0) {
			$line = lang('booking.warning.permitted_limit');
			$msg = sprintf($line, $user_permitted_booking_count);
		} else {
			$line = lang('booking.warning.permitted_limit_with_active');
			$msg = sprintf($line,
				$user_permitted_booking_count,
				$user_booking_limit,
				$user_booking_count
			);
		}
		echo msgbox('notice large', $msg);
	}
}

// Info secton
//
if ( ! $show_room_col || ! $show_date_col) {

	$info = [];
	if ( ! $show_date_col) {
		$info['app.date'] = date_output_long($slot->datetime);
	}

	if ( ! $show_room_col) {
		$info['room.room'] = html_escape($slot->room->name);
	}
	$info_fmt = '<div><dt>%s:</dt><dd>%s</dd></div>';
	$info_html = '';
	foreach ($info as $key => $value) {
		$label = lang($key);
		$info_html .= sprintf($info_fmt, $label, $value);
	}

	echo "<fieldset style='padding-top:0;padding-bottom:0'><dl class='info'>{$info_html}</dl></fieldset>";
}

echo "<fieldset style='border:0; padding:0;'>";
echo $this->table->generate();
echo "</fieldset>";

if ($can_book_single) {
	// Actions
	//
	$submit = form_button([
		'type' => 'submit',
		'name' => 'action',
		'value' => 'create',
		'content' => '&check; ' . lang('booking.add.multi.single.action'),
	]);

	$cancel = anchor($return_uri, lang('app.action.cancel'), ['up-dismiss' => '']);

	echo "<div class='booking-type-content' style='border-top:0px;'>{$submit} &nbsp; {$cancel}</div>";
}
