<?php

$template = "{user}{notes}{actions}";

$vars = [
	'{user}' => '',
	'{notes}' => '',
	'{actions}' => '',
];

$actions = [];

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
if ($show_notes && !empty($booking->notes)) {
	$notes = html_escape($booking->notes);
	$tooltip = '';
	if (strlen($notes) > 15) {
		$tooltip = 'up-tooltip="' . $notes . '"';
	}
	$vars['{notes}'] .= '<div class="booking-cell-notes" ' . $tooltip . '>'.character_limiter($notes, 15).'</div>';
	$vars['{notes}'] .= '<div class="booking-cell-notes-full">'.nl2br($notes).'</div>';
}

if ( ! empty($actions)) {
	$vars['{actions}'] = '';	//'<div class="booking-cell-actions">' . implode(" ", $actions) . '</div>';
}

// Process template for items
$body = strtr($template, $vars);
// Remove tags that don't have content
$body = str_replace(array_keys($vars), '', $body);

if (empty(trim($body))) {
	$body = '&mdash;';
}


// URL params to pass to /view/ so it can return to source page
$params = ['params' => http_build_query($context->get_query_params()) ];
$uri = sprintf('bookings/view/%d?%s', $booking->booking_id, http_build_query($params));
$url = site_url($uri);

// For checkbox
//
$input_name = sprintf('bookings[]');
$input_id = sprintf('booking_%d', $booking->booking_id);
$input_value = $booking->booking_id;

// Deletable
//
$is_deletable = booking_cancelable($booking);

?>

<td class='<?= $class ?>'>

	<?php if ($is_deletable): ?>
	<?php
	echo form_checkbox([
		'form' => 'form_cancel_multi',
		'name' => $input_name,
		'id' => $input_id,
		'value' => $input_value,
		'class' => 'bookings-grid-booked-check multi-select-content',
		'data-multi' => 'true',
		'style' => 'display:none; position: absolute; bottom: 2px; right: 2px;',
	]);
	?>
	<label
		style="display: none"
		class="bookings-grid-button multi-select-content"
		data-multi="true"
		for="<?= $input_id ?>"
	>
		<?php
		echo $body;
		?>
	</label>
	<?php endif; ?>

	<?php

	$cls = 'bookings-grid-button';

	if ($is_deletable) {
		$cls .= ' multi-select-content';
	}

	$link_attrs = [
		'class' => $cls,
		'up-position' => "right",
		'up-target' => ".bookings-view",
		'up-layer' => "new drawer",
		'up-history' => "false",
		'up-preload' => '',
	];

	if ($is_deletable) {
		$link_attrs['data-multi'] = "false";
	}

	echo anchor($url, $body, $link_attrs);

	?>

</td>
