<?php

use app\components\Calendar;

$this->table->set_template([
	'table_open' => '<table class="zebra-table" width="100%" cellpadding="6" cellspacing="0" border="0">',
]);

$date_format = setting('date_format_long', 'crbs');

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
		'name' => 'View all',
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
			'name' => 'Edit',
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
			'name' => 'Edit',
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
		'name' => 'Cancel booking',
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
	'label' => 'Date',
	'value' => $booking->date->format($date_format),
];

// Week
//
$info[] = [
	'name' => 'week',
	'label' => 'Week',
	'value' => week_dot($booking->week, 'sm') . ' ' . html_escape($booking->week->name),
];


if ($booking->repeat_id) {
	$weekday = Calendar::get_day_name($booking->repeat->weekday);
	$info[] = [
		'name' => 'occurs',
		'label' => 'Occurs',
		'value' => sprintf("%s, every %s", $booking->week->name, $weekday),
	];
} else {
	$info[] = [
		'name' => 'occurs',
		'label' => 'Occurs',
		'value' => 'Once',
	];
}

// Period
//
$time_fmt = setting('time_format_period');
$time = '';
if (!empty($time_fmt)) {
	$start = date($time_fmt, strtotime($booking->period->time_start));
	$end = date($time_fmt, strtotime($booking->period->time_end));
	$time = sprintf(' (%s - %s)', $start, $end);
}
$info[] = [
	'name' => 'period',
	'label' => 'Period',
	'value' => html_escape($booking->period->name . $time),
];

// User
//
$user_is_admin = $this->userauth->is_level(ADMINISTRATOR);
$user_is_booking_owner = ($booking->user_id && $booking->user_id == $current_user->user_id);

$display_user_setting = ($booking->repeat_id)
	? setting('bookings_show_user_recurring')
	: setting('bookings_show_user_single');

$show_user = ($user_is_admin || $user_is_booking_owner || $display_user_setting);

$user_label = '';
if ($booking->user) {
	$user_label = !empty($booking->user->displayname)
		? $booking->user->displayname
		: $booking->user->username;
}

$user_value = ($show_user && ! empty($booking->user))
	? html_escape($user_label)
	: '<em>Not available</em>';

$info[] = [
	'name' => 'user',
	'label' => 'Booked by',
	'value' => $user_value,
];

// Department
//
$department = ($booking->department)
	? $booking->department
	: ($booking->user ? $booking->user->department : false);
if ($department) {
	$info[] = [
		'name' => 'department',
		'label' => 'Department',
		'value' => html_escape($department->name),
	];
}

// Notes
//
if (!empty($booking->notes)) {
	$info[] = [
		'name' => 'notes',
		'label' => 'Notes',
		'value' => html_escape($booking->notes),
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

echo "<h3>Booking</h3>";
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
