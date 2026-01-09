<?php

use app\components\Calendar;

$this->table->set_template([
	'table_open' => '<table class="border-table" width="100%" cellpadding="6" cellspacing="0" border="0">',
]);


// Date
//
$info[] = [
	'name' => 'date',
	'label' => lang('app.date'),
	'value' => date_output_long($booking->date),
];

// Week
//
$info[] = [
	'name' => 'week',
	'label' => lang('week.week'),
	'value' => week_dot($booking->week, 'sm') . ' ' . html_escape($booking->week->name),
];


if ($booking->repeat_id) {
	$weekday = Calendar::get_day_name($booking->repeat->weekday);
	$lang_key = sprintf('cal_%s', strtolower((string) $weekday));
	$weekday_lang = lang($lang_key);
	$info[] = [
		'name' => 'occurs',
		'label' => lang('booking.occurs'),
		'value' => sprintf("%s, %s %s", $booking->week->name, strtolower(lang('app.every')), $weekday_lang),
	];
} else {
	$info[] = [
		'name' => 'occurs',
		'label' => lang('booking.occurs'),
		'value' => lang('booking.occurs.once'),
	];
}

// Period
//
$start = date_output_time($booking->period->time_start);
$end = date_output_time($booking->period->time_end);
$time = sprintf(' (%s - %s)', $start, $end);
$info[] = [
	'name' => 'period',
	'label' => lang('period.period'),
	'value' => html_escape($booking->period->name . $time),
];

// User
//
$user_value = '<em>' . lang('app.not_available') . '</em>';
if (booking_user_viewable($booking)) {
	$user_value = '<em>' . lang('app.not_set') . '</em>';
	if ($booking->user) {
		$user_label = !empty($booking->user->displayname)
			? $booking->user->displayname
			: $booking->user->username;
		$user_value = html_escape($user_label);
	}
}

$info[] = [
	'name' => 'user',
	'label' => lang('booking.booked_by'),
	'value' => $user_value,
];

// Department
//
$department = $booking->department ?: ($booking->user ? $booking->user->department : false);
if ($department) {
	$info[] = [
		'name' => 'department',
		'label' => lang('department.department'),
		'value' => html_escape($department->name),
	];
}

// Notes
//
if (!empty($booking->notes)) {

	$notes_value = '<em>' . lang('app.not_available') . '</em>';

	if (booking_notes_viewable($booking)) {
		$notes_value = html_escape($booking->notes);
	}

	$info[] = [
		'name' => 'notes',
		'label' => lang('booking.notes'),
		'value' => $notes_value,
	];

}


foreach ($info as $row) {
	$label = ['data' => "<strong>{$row['label']}</strong>", 'width' => '40%'];
	$value = ['data' => $row['value'], 'width' => '60%'];
	$this->table->add_row($label, $value);
}

echo $this->table->generate();
