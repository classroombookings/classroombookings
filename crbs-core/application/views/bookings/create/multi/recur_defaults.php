<?php

// Date format of bookings

use app\components\Calendar;

$fieldset_fmt = <<<EOF
<fieldset style='padding:0;'>
	<legend style='margin:8px;color:inherit'>%s</legend>
	%s
</fieldset>
EOF;


// Generate table of bookings
//
$this->table->set_template([
	'table_open' => '<table
		class="zebra-table form-table multibooking-table"
		style="line-height:1.3;margin:0;border-top:0;border-bottom:0;"
		width="100%"
		cellpadding="8"
		cellspacing="0"
		border="0"
	>',
]);

// Get columns for table
//

$by_room = [];

foreach ($multibooking->slots as $key => $slot) {
	$dates[] = $slot->date;
	$by_room[$slot->room_id]['room'] = $slot->room;
	$by_room[$slot->room_id]['slots'][$key] = $slot;
}

$show_date_col = (count(array_unique($dates)) == 1)
	? false
	: true;

// Configure table headings
//
$cols = [];
$cols[] = ['data' => '', 'width' => 10];
if ($show_date_col) {
	$cols[] = ['data' => lang('app.date'), 'width' => ''];
} else {
	$cols[] = ['data' => lang('period.period'), 'width' => ''];
}
$cols[] = ['data' => lang('department.department'), 'width' => '17%'];
$cols[] = ['data' => lang('user.user'), 'width' => '17%'];
$cols[] = ['data' => lang('booking.notes'), 'width' => '17%'];
$cols[] = ['data' => lang('booking.start'), 'width' => '17%'];
$cols[] = ['data' => lang('booking.end'), 'width' => '17%'];


// Generate rows
//

foreach ($by_room as $room_id => $data) {

	$fields = [];

	$room_name_esc = html_escape($data['room']->name);

	if ( ! has_permission(Permission::BK_RECUR_CREATE, $room_id)) {
		$msg = msgbox('notice large', lang('booking.error.no_permission_room'));
		$msg = "<div style='padding:8px'>{$msg}</div>";
		echo sprintf($fieldset_fmt, $room_name_esc, $msg);
		continue;
	}

	$slot_count = count($data['slots']);

	foreach ($data['slots'] as $key => $slot) {

		$capabilities = $slot->capabilities;

		$day_name = Calendar::get_day_name($slot->datetime->format('N'));
		$lang_key = sprintf('cal_%s', strtolower((string) $day_name));
		$day_name_lang = lang($lang_key);

		// List of availbale dates to choose from for recurring dates
		//
		$recurring_date_options = [];
		if (is_array($slot->recurring_dates)) {
			foreach ($slot->recurring_dates as $date) {
				$title = date_output_long($date->date);
				if ($date->date->format('Y-m-d') == $slot->date) $title = '* ' . $title;
				$recurring_date_options[ $date->date->format('Y-m-d') ] = $title;
			}
		}


		// 'Create' checkbox col
		//
		$create_field = sprintf('slots[%d][create]', $slot->mbs_id);
		$create_hidden = form_hidden($create_field, 0);
		$check_props = [
			'id' => $create_field,
			'name' => $create_field,
			'value' => 1,
			'checked' => (set_value($create_field, 1) == 1),
		];
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
			$date_period_col = form_label($date . $period_text, $create_field, ['class' => 'ni']);
		} else {
			$period_text = sprintf('<div>%s</div><small class="hint">(%s - %s)</small>',
				html_escape($slot->period->name),
				date_output_time($slot->period->time_start),
				date_output_time($slot->period->time_end)
			);
			$date_period_col = form_label($period_text, $create_field, ['class' => 'ni']);
		}

		// Department column
		//
		if ($capabilities['recur.set_department']) {
			$copy_group = sprintf('r%d-department', $room_id);
			$department_field = sprintf('slots[%d][department_id]', $slot->mbs_id);
			$value = set_value($department_field, $department ? $department->department_id : '', FALSE);
			$input = form_dropdown([
				'name' => $department_field,
				'id' => $department_field,
				'options' => html_escape($department_options),
				'selected' => $value,
				'up-copy-group' => $copy_group,
			]);
			$input_block = "<div class='block b-90'>{$input}</div>";
			$append_block = '';
			if ( ! isset($fields['department']) && $slot_count > 1) {
				$fields['department'] = true;
				$append_block = "<div class='block b-10'><button type='button' class='btn-block' up-copy-to='{$copy_group}'>&darr;</button></div>";
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
		if ($capabilities['recur.set_user']) {
			$copy_group = sprintf('r%d-user', $room_id);
			$user_field = sprintf('slots[%d][user_id]', $slot->mbs_id);
			$value = set_value($user_field, $user->user_id, FALSE);
			$input = form_dropdown([
				'name' => $user_field,
				'id' => $user_field,
				'options' => $user_options,
				'selected' => $value,
				'up-copy-group' => $copy_group,
			]);
			$input_block = "<div class='block b-90'>{$input}</div>";
			$append_block = '';
			if ( ! isset($fields['user']) && $slot_count > 1) {
				$fields['user'] = true;
				$append_block = "<div class='block b-10'><button type='button' class='btn-block' up-copy-to='{$copy_group}'>&darr;</button></div>";
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
		$copy_group = sprintf('r%d-notes', $room_id);
		$notes_field = sprintf('slots[%d][notes]', $slot->mbs_id);
		$value = set_value($notes_field, '', FALSE);
		$input = form_input([
			'name' => $notes_field,
			'id' => $notes_field,
			'size' => 30,
			'value' => $value,
			'up-copy-group' => $copy_group,
		]);
		$input_block = "<div class='block b-90'>{$input}</div>";
		$append_block = '';
		if ( ! isset($fields['notes']) && $slot_count > 1) {
			$fields['notes'] = true;
			$append_block = "<div class='block b-10'><button type='button' class='btn-block' up-copy-to='{$copy_group}'>&darr;</button></div>";
		}
		$notes_col = "<div class='block-group'>{$input_block}{$append_block}</div>";


		// Recurring start from
		//
		$copy_group = sprintf('r%d-start-%s', $room_id, $day_name);
		$start_field = sprintf('slots[%d][recurring_start]', $slot->mbs_id);
		$options = [
			'session' => lang('booking.recurring.start_of_session'),
			lang('booking.recurring.specific_date') => $recurring_date_options,
		];
		$value = set_value($start_field, $slot->date, FALSE);
		$input = form_dropdown([
			'name' => $start_field,
			'id' => $start_field,
			'options' => $options,
			'selected' => $value,
			'up-copy-group' => $copy_group,
		]);
		$input_block = "<div class='block b-90'>{$input}</div>";
		$append_block = '';
		if (!isset($fields[$copy_group]) && $slot_count > 1) {
			$fields[$copy_group] = true;
			$append_block = "<div class='block b-10'><button type='button' class='btn-block' up-copy-to='{$copy_group}'>&darr;</button></div>";
		}
		$start_col = "<div class='block-group'>{$input_block}{$append_block}</div>";

		// Recurring end date
		//
		$copy_group = sprintf('r%d-end-%s', $room_id, $day_name);
		$end_field = sprintf('slots[%d][recurring_end]', $slot->mbs_id);
		$options = [
			'session' => lang('booking.recurring.end_of_session'),
			lang('booking.recurring.specific_date') => $recurring_date_options,
		];
		$value = set_value($end_field, 'session', FALSE);
		$input = form_dropdown([
			'name' => $end_field,
			'id' => $end_field,
			'options' => $options,
			'selected' => $value,
			'up-copy-group' => $copy_group,
		]);
		$input_block = "<div class='block b-90'>{$input}</div>";
		$append_block = '';
		if (!isset($fields[$copy_group]) && $slot_count > 1) {
			$fields[$copy_group] = true;
			$append_block = "<div class='block b-10'><button type='button' class='btn-block' up-copy-to='{$copy_group}'>&darr;</button></div>";
		}
		$end_col = "<div class='block-group'>{$input_block}{$append_block}</div>";


		// Add row
		//
		$row = [];
		$row[] = $check_col;
		$row[] = $date_period_col;
		$row[] = $department_col;
		$row[] = $user_col;
		$row[] = $notes_col;
		$row[] = $start_col;
		$row[] = $end_col;
		$this->table->add_row($row);
	}

	$this->table->set_heading($cols);

	echo sprintf($fieldset_fmt, $room_name_esc, $this->table->generate());

}

if ($can_book_recur) {

	// Actions
	//
	$submit = form_button([
		'type' => 'submit',
		'name' => 'action',
		'value' => 'create',
		'content' => lang('app.action.continue') . ' &rarr;',
	]);

	$cancel = anchor($return_uri, lang('app.action.cancel'), ['up-dismiss' => '']);

	echo "<div class='booking-type-content' style='border-top:0px;'>{$submit} &nbsp; {$cancel}</div>";

}
