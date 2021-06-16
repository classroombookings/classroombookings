<?php

$display_user_setting = ($booking->repeat_id)
	? setting('bookings_show_user_recurring')
	: setting('bookings_show_user_single');


$vars = [
	'user' => false,
	'notes' => false,
];


// User info
//
$user_is_admin = $this->userauth->is_level(ADMINISTRATOR);
$user_is_booking_owner = ($booking->user_id && $booking->user_id == $context->user->user_id);

$show_user = ($user_is_admin || $user_is_booking_owner || $display_user_setting);

if ($show_user && ! empty($booking->user)) {

	$user_label = strlen($booking->user->displayname)
			? $booking->user->displayname
			: $booking->user->username;

	$vars['user'] = '<div class="booking-cell-user">' . html_escape($user_label) . '</div>';
}

// Notes
//
if ($booking->notes) {
	$notes = html_escape($booking->notes);
	$tooltip = '';
	if (strlen($notes) > 15) {
		$tooltip = 'up-tooltip="' . $notes . '"';
	}
	$vars['notes'] .= '<div class="booking-cell-notes" ' . $tooltip . '>'.character_limiter($notes, 15).'</div>';
}

$body = strlen($vars['notes'])
	? $vars['notes']
	: $vars['user'];

$icon = '';

if ($booking->repeat_id) {
	$icon = img([
		'src' => base_url('assets/images/refresh.svg'),
		'class' => 'booking-icon',
		'alt' => 'Repeat icon',
		'title' => 'Recurring',
	]);
}

?>

<td class='<?= $class ?>'>
	<button
		class="bookings-grid-button"
		up-href="<?= site_url('bookings/view/' . $booking->booking_id) ?>"
		up-history="false"
		up-position="right"
		up-drawer=".bookings-view"
		up-preload
	>
		<?= $body ?>
		<?= $icon ?>
	</button>
</td>
