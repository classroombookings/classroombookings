<?php

use app\components\Calendar;

$this->table->set_template([
	'table_open' => '<table class="zebra-table" style="line-height:1.3" width="100%" cellpadding="10" cellspacing="0" border="0">',
]);

$this->table->set_heading(lang('app.status'), lang('booking.booking'));

$links = [];
$info = [];


// Links
//

// Params for main booking page
$params = $this->input->get('params');

if ($booking->repeat_id) {
	$uri = sprintf('bookings/view/%d?%s', $booking->booking_id, http_build_query(['params' => $params]));

	$links[] = [
		'link' => $uri,
		'name' => lang('booking.series.go_back'),
		'icon' => 'arrow_turn_left.png',
		'attrs' => [
			'up-target' => '.bookings-view',
			'up-history' => 'false',
		],
	];
}

$links_html = empty($links)
	? ''
	: iconbar($links);


// Generate table of all bookings
//

foreach ($all_bookings as $repeat_booking) {

	$status_label = booking_status_label($repeat_booking);
	$status_icon = booking_status_icon($repeat_booking);
	$status = img([
		'src' => asset_url('assets/images/ui/' . $status_icon),
		'title' => $status_label,
		'alt' => $status_label,
	]);
	$date = "<strong>" . date_output_long($repeat_booking->date) . "</strong>";
	$notes = '';
	if (booking_notes_viewable($repeat_booking)) {
		$notes = !empty($repeat_booking->notes)
			? '<br>' . html_escape($repeat_booking->notes)
			: '';
	}

	if ($repeat_booking->date == $booking->date) {
		$date = "<span class='highlight'>{$date}</span>";
		$notes = "<span class='highlight'>{$notes}</span>";
	}

	$this->table->add_row($status, $date . $notes);
}

$info_html = $this->table->generate();


$messages = $this->session->flashdata('saved');
echo "<div class='messages'>{$messages}</div>";

// Booking
//

echo "<h3>" . lang('booking.bookings_in_series') . "</h3>";
echo $links_html;
echo $info_html;
