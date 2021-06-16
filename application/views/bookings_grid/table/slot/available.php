<?php

/* @var $slot app\components\bookings\Slot */

$params = [
	'date' => $slot->date->date,
	'period_id' => $slot->period->period_id,
	'room_id' => $slot->room->room_id,
	'params' => http_build_query($context->get_query_params()),
];

$uri = 'bookings/create/single?' . http_build_query($params);
$url = site_url($uri);

?>

<td class='<?= $class ?>'>

	<button
		class="bookings-grid-button"
		up-href="<?= $url ?>"
		up-modal=".bookings-create"
		up-history="false"
		up-position="right"
		up-preload
	>
	</button>

</td>
