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

	$user_label = strlen($booking->user->displayname)
			? $booking->user->displayname
			: $booking->user->username;

	$vars['{user}'] = '<div class="booking-cell-user">' . html_escape($user_label) . '</div>';
}

// Notes
//
if ($booking->notes) {
	$notes = html_escape($booking->notes);
	$tooltip = '';
	if (strlen($notes) > 15) {
		$tooltip = 'up-tooltip="' . $notes . '"';
	}
	$vars['{notes}'] .= '<div class="booking-cell-notes" ' . $tooltip . '>'.character_limiter($notes, 15).'</div>';
}

// // Edit if admin?
// //
// if ($slot->editable()) {
// 	$edit_url = site_url('bookings/edit/' . $booking->booking_id);
// 	$actions[] = "<a class='booking-action' href='{$edit_url}' title='Edit this booking'>edit</a>";
// }

// // 'Cancel' action if user is an Admin, Room owner, or Booking owner
// //
// if ($slot->cancelable()) {
// 	$cancel_msg = 'Are you sure you want to cancel this booking?';
// 	if ($context->user->user_id != $booking->user->user_id){
// 		$cancel_msg = 'Are you sure you want to cancel this booking?\n\n(**) Please take caution, it is not your own.';
// 	}
// 	$cancel_url = site_url('bookings/cancel/'.$booking->booking_id);

// 	$actions[] = "<button
// 		class='button-empty booking-action'
// 		type='submit'
// 		name='cancel'
// 		value='{$booking->booking_id}'
// 		onclick='if(!confirm(\"{$cancel_msg}\")) return false'
// 	>cancel</button>";
// }

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
	<button
		class="bookings-grid-button"
		up-href="<?= $url ?>"
		up-history="false"
		up-position="right"
		up-drawer=".bookings-view"
		up-preload
	>
		<?= $body ?>
	</button>
</td>
