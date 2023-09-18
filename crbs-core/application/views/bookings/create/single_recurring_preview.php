<?php

use app\components\Calendar;

// Weekday name that the selected date falls on
$day_name = Calendar::get_day_name($date_info->weekday);

// Date format of bookings
$date_format = setting('date_format_long', 'crbs');

// For period display
$time_fmt = setting('time_format_period');

// Number of conflicts found
$conflict_count = 0;


// Generate table of bookings
//
$this->table->set_template([
	'table_open' => '<table class="zebra-table" style="line-height:1.3;margin-top:16px;margin-bottom:16px" width="100%" cellpadding="10" cellspacing="0" border="0">',
]);
$this->table->set_heading([
	['data' => 'Date', 'width' => 250],
	['data' => 'Action', 'width' => 250],
	['data' => 'Existing booking', 'width' => 300],
]);


// Table rows for every slot.
//
foreach ($slots as $key => $slot) {

	$date = $slot['datetime']->format($date_format);

	$existing_html = '';

	if (isset($slot['booking'])) {

		$conflict_count++;

		$user_label = !empty($slot['booking']->user->displayname)
			? $slot['booking']->user->displayname
			: $slot['booking']->user->username;

		$notes_html = '';
		if (!empty($slot['booking']->notes)) {
			$notes = html_escape($slot['booking']->notes);
			$tooltip = '';
			if (strlen($notes) > 15) {
				$tooltip = 'up-tooltip="' . $notes . '"';
			}
			$notes_html = '<div ' . $tooltip . '>'.character_limiter($notes, 15).'</div>';
		}

		$existing_html = $user_label . $notes_html;
	}

	$date_ymd = $slot['datetime']->format('Y-m-d');
	$field_name = sprintf('dates[%s][action]', $date_ymd);

	// Default value
	$hidden_html = form_hidden($field_name, 'do_not_book');

	if (isset($slot['booking'])) {

		$hidden = form_hidden(sprintf('dates[%s][replace_booking_id]', $date_ymd), $slot['booking']->booking_id);

		$options_value = set_value($field_name);

		$actions_list = form_dropdown([
			'name' => $field_name,
			'options' => $slot['actions'],
			'selected' => $options_value,
		]);

		$actions_html = $hidden . $actions_list;

	} else {

		$field_id = sprintf('date_%s', $date_ymd);
		$field_value = set_value($field_name, 'book', FALSE);


		$input = form_checkbox([
			'name' => $field_name,
			'id' => $field_id,
			'value' => 'book',
			'style' => 'vertical-align:middle',
			'checked' => ($field_value == 'book'),
		]);

		$input_label = form_label('Create booking', $field_id, ['class' => 'ni']);

		$actions_html = $input . $input_label;
	}

	$this->table->add_row($date, $hidden_html . $actions_html, $existing_html);
}

// Info
//

$info = [];


$recurring_html = sprintf('Every %s on %s', $day_name, html_escape($week->name));
$info[] = "<p><strong>Recurs: </strong> {$recurring_html}</p>";

$period_html = html_escape($period->name);
if (!empty($time_fmt)) {
	$start = date($time_fmt, strtotime($period->time_start));
	$end = date($time_fmt, strtotime($period->time_end));
	$period_html .= sprintf(' <span style="font-size:90%%;color:#aaa;background:transparent">(%s - %s)</span>', $start, $end);
}
$info[] = "<p><strong>Period: </strong> {$period_html}</p>";

$room_html = html_escape($room->name);
$info[] = "<p><strong>Room: </strong> {$room_html}</p>";

if ($department) {
	$department_html = html_escape($department->name);
	$info[] = "<p><strong>Department: </strong> {$department_html}</p>";
}

$user_html = !empty($user->displayname)
	? $user->displayname
	: $user->username;
$info[] = "<p><strong>User: </strong> {$user_html}</p>";

$notes_html = set_value('notes');
if (!empty($notes_html)) {
	$info[] = "<p><strong>Notes: </strong> {$notes_html}</p>";
}

echo "<fieldset style='padding-top:0;padding-bottom:0'>" . implode("\n", $info) . "</fieldset>";

$str = sprintf('This recurring booking would result in %d instances.', count($slots));
echo $str;

if ($conflict_count > 0) {
	$str = ($conflict_count === 1)
		? 'There is <strong>one</strong> booking conflict to review.'
		: sprintf('There are <strong>%d</strong> booking conflicts to review.', $conflict_count);
	echo msgbox('exclamation', $str, FALSE);
}


// Form
//
$attrs = [
	'id' => 'bookings_create_single',
	'class' => '',
	'up-accept-location' => 'bookings/*',
	'up-target' => '.bookings-create',
	'up-layer' => 'any',
];

$hidden = [
	'room_id' => $room->room_id,
	'period_id' => $period->period_id,
	'department_id' => set_value('department_id'),
	'user_id' => set_value('user_id'),
	'notes' => set_value('notes'),
	'date' => set_value('date'),
];

echo form_open(current_url(), $attrs, $hidden);

echo "<fieldset style='border:0; padding:0;'>";
echo $this->table->generate();
echo "</fieldset>";

// echo '<pre>' . json_encode($_POST, JSON_PRETTY_PRINT) . '</pre>';


// Actions
//
$submit = form_button([
	'type' => 'submit',
	'name' => 'action',
	'value' => 'create_recurring',
	'content' => 'Create bookings',
]);

$cancel = anchor($return_uri, 'Cancel', ['up-dismiss' => '']);

echo "<div class='' style='border-top:0px;'>{$submit} &nbsp; {$cancel}</div>";

echo form_close();
