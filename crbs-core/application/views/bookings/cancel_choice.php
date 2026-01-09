<?php

if ($booking->user_id && $current_user->user_id != $booking->user_id) {
	echo msgbox('exclamation', lang('booking.warning.not_own'));
	echo "<br>";
}


$cls = '';

if ($booking->repeat_id) {

	$heading = '<strong>' . lang('booking.cancel.recurring.title') . ':</strong><br><br>';

	$cls = 'is-repeat';

	$buttons = [];

	$buttons[] = form_button([
		'type' => 'submit',
		'name' => 'cancel',
		'value' => '1',
		'content' => lang('booking.selection.this_only'),
	]);

	$buttons[] = form_button([
		'type' => 'submit',
		'name' => 'cancel',
		'value' => 'future',
		'content' => lang('booking.selection.future'),
	]);

	$buttons[] = form_button([
		'type' => 'submit',
		'name' => 'cancel',
		'value' => 'all',
		'content' => lang('booking.selection.all'),
	]);

	$cancel = "<br><br><a href='#' up-dismiss>" . lang('booking.cancel.abort') . "</a>";

	$content = implode("\n", $buttons) . $cancel;

} else {

	$heading = '<strong>' . lang('booking.cancel.single.title') . '</strong><br><br>';

	$submit = form_button([
		'type' => 'submit',
		'name' => 'cancel',
		'value' => '1',
		'content' => lang('booking.cancel.single.action'),
		'autofocus' => true,
	]);

	$cancel = "<a href='#' up-dismiss>" . lang('booking.cancel.abort') . "</a>";

	$content = "{$submit} &nbsp; {$cancel}";
}


$uri = sprintf('bookings/cancel/%d?%s', $booking->booking_id, http_build_query(['params' => $params]));
echo form_open($uri, ['class' => 'booking-choices']);
echo $heading;
echo "<div class='submit' style='border-top:0px;'>{$content}</div>";
echo form_close();
