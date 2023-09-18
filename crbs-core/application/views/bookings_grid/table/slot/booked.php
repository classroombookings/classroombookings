<?php

$display_user_setting = ($booking->repeat_id)
	? setting('bookings_show_user_recurring')
	: setting('bookings_show_user_single');

$template = "{user}{notes}{actions}";

$vars = [
	'{user}' => '',
	'{notes}' => '',
	'{actions}' => '',
];

$actions = [];


// User info
//
$user_is_admin = $this->userauth->is_level(ADMINISTRATOR);
$user_is_booking_owner = ($booking->user_id && $booking->user_id == $context->user->user_id);

$show_user = ($user_is_admin || $user_is_booking_owner || $display_user_setting);

if ($show_user && ! empty($booking->user)) {

	$user_label = !empty($booking->user->displayname)
		? $booking->user->displayname
		: $booking->user->username;
	if (!empty($user_label)) {
		$vars['{user}'] = '<div class="booking-cell-user">' . html_escape($user_label) . '</div>';
	}
}

// Notes
//
if (!empty($booking->notes)) {
	$notes = html_escape($booking->notes);
	$tooltip = '';
	if (strlen($notes) > 15) {
		$tooltip = 'up-tooltip="' . $notes . '"';
	}
	$vars['{notes}'] .= '<div class="booking-cell-notes" ' . $tooltip . '>'.character_limiter($notes, 15).'</div>';
}

if ( ! empty($actions)) {
	$vars['{actions}'] = '';	//'<div class="booking-cell-actions">' . implode(" ", $actions) . '</div>';
}

// Process template for items
$body = strtr($template, $vars);
// Remove tags that don't have content
$body = str_replace(array_keys($vars), '', $body);


// URL params to pass to /view/ so it can return to source page
$params = ['params' => http_build_query($context->get_query_params()) ];
$uri = sprintf('bookings/view/%d?%s', $booking->booking_id, http_build_query($params));
$url = site_url($uri);

?>

<td class='<?= $class ?>'>
	<a
		class="bookings-grid-button"
		href="<?= $url ?>"
		up-position="right"
		up-target=".bookings-view"
		up-layer="new drawer"
		up-history="false"
		up-preload
	>
		<?= $body ?>
	</a>
</td>
