<?php

use app\components\Calendar;

$this->table->set_template([
	'table_open' => '<table class="zebra-table" width="100%" cellpadding="6" cellspacing="0" border="0">',
]);

$links = [];
$info = [];


// Edit/cancel links
//

// Params for main booking page
$params = $this->input->get('params');

if ($booking->repeat_id) {
	$uri = sprintf('bookings/view_series/%d?%s', $booking->booking_id, http_build_query(['params' => $params]));
	$links[] = [
		'link' => $uri,
		'name' => lang('app.action.view_all'),
		'icon' => 'calendar_view_month.png',
		'attrs' => [
			'up-target' => '.bookings-view',
			'up-preload' => '',
		],
	];
}


if (booking_editable($booking)) {

	if ($booking->repeat_id) {
		$edit_choices = $this->load->view('bookings/edit_choice', ['booking' => $booking, 'params' => $params], TRUE);
		$links[] = [
			'link' => '#',
			'name' => lang('app.action.update'),
			'icon' => 'edit.png',
			'attrs' => [
				'up-layer' => 'new popup',
				'up-align' => 'right',
				'up-size' => 'medium',
				'up-content' => html_escape($edit_choices),
				'up-history' => 'false',
			],
		];
	} else {
		$uri = sprintf('bookings/edit/%d?%s', $booking->booking_id, http_build_query(['params' => $params]));
		$links[] = [
			'link' => $uri,
			'name' => lang('app.action.update'),
			'icon' => 'edit.png',
			'attrs' => [
				'up-layer' => 'new modal',
				'up-target' => '.bookings-edit',
				'up-preload' => '',
			]
		];
	}

}

if (booking_cancelable($booking)) {
	$cancel_choices = $this->load->view('bookings/cancel_choice', ['booking' => $booking, 'params' => $params], TRUE);
	$links[] = [
		'link' => '#',
		'name' => lang('booking.action.cancel_booking'),
		'icon' => 'delete.png',
		'attrs' => [
			'up-layer' => 'new popup',
			'up-align' => 'right',
			'up-size' => 'medium',
			'up-content' => html_escape($cancel_choices),
			'up-class' => 'booking-choices-cancel',
		]
	];
}

$links_html = empty($links)
	? ''
	: iconbar($links);


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
	$this->table->add_row($row['label'], $row['value']);
}

$info_html = $this->table->generate();

//


$messages = $this->session->flashdata('saved');
echo "<div class='messages'>{$messages}</div>";

// Booking
//

echo "<h3>" . lang('booking.booking') . "</h3>";
echo $links_html;
echo "<div class='bookings-edit-choice'></div>";
echo "<div class='bookings-cancel'></div>";
echo $info_html;

// Room
//

echo '<h3>' . html_escape($booking->room->name) . '</h3>';

$photo_html = '';
$fields_html = '';

$this->table->clear();

foreach ($booking->room->info as $row) {
	$this->table->add_row($row['label'], $row['value']);
}

$fields_html = $this->table->generate();

if ($photo_url = image_url($booking->room->photo)) {
	$img = img($photo_url);
	$photo_html = "<br><div class='room-photo'>{$img}</div>";
}

echo $fields_html;
echo $photo_html;
