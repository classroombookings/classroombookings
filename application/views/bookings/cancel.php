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
		'content' => 'This and following bookings in series',
	]);

	$buttons[] = form_button([
		'type' => 'submit',
		'name' => 'cancel',
		'value' => 'all',
		'content' => 'All bookings in series',
	]);

	$cancel = anchor('bookings/noop', 'No, keep it', ['up-target' => '.bookings-cancel', 'up-history' => 'false']);

	$content = implode("\n", $buttons) . $cancel;

} else {

	$heading = '<strong>Cancel this booking?</strong><br><br>';

	$submit = form_button([
		'type' => 'submit',
		'name' => 'cancel',
		'value' => '1',
		'content' => 'Yes, cancel booking',
	]);

	$cancel = anchor('bookings/noop', 'No, keep it', ['up-target' => '.bookings-cancel', 'up-history' => 'false']);

	$content = "{$submit} &nbsp; {$cancel}";
}


echo form_open(current_url());
echo $heading;
echo "<div class='submit' style='border-top:0px;'>{$content}</div>";
echo form_close();
