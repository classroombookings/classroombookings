<?php

if ($booking->user_id && $current_user->user_id != $booking->user_id) {
	echo msgbox('exclamation', 'This is not your own.');
	echo "<br>";
}


$cls = '';

if ($booking->repeat_id) {

	$heading = '<strong>Cancel recurring booking:</strong><br><br>';

	$cls = 'is-repeat';

	$buttons = [];

	$buttons[] = form_button([
		'type' => 'submit',
		'name' => 'cancel',
		'value' => '1',
		'content' => 'This booking only',
	]);

	$buttons[] = form_button([
		'type' => 'submit',
		'name' => 'cancel',
		'value' => 'future',
		'content' => 'This and future bookings in series',
	]);

	$buttons[] = form_button([
		'type' => 'submit',
		'name' => 'cancel',
		'value' => 'all',
		'content' => 'All bookings in series',
	]);

	$cancel = "<a href='#' up-dismiss>No, keep it</a>";

	$content = implode("\n", $buttons) . $cancel;

} else {

	$heading = '<strong>Cancel this booking?</strong><br><br>';

	$submit = form_button([
		'type' => 'submit',
		'name' => 'cancel',
		'value' => '1',
		'content' => 'Yes, cancel booking',
		'autofocus' => true,
	]);

	$cancel = "<a href='#' up-dismiss>No, keep it</a>";

	$content = "{$submit} &nbsp; {$cancel}";
}


$uri = sprintf('bookings/cancel/%d?%s', $booking->booking_id, http_build_query(['params' => $params]));
echo form_open($uri, ['class' => 'booking-choices']);
echo $heading;
echo "<div class='submit' style='border-top:0px;'>{$content}</div>";
echo form_close();
