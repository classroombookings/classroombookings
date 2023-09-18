<?php
$url = "rooms/info/{$room->room_id}";
$name = html_escape($room->name);
$link = anchor($url, $name, [
	'up-layer' => 'new drawer',
	'up-position' => 'left',
	'up-target' => '.room-info',
	'up-preload' => '',
]);
?>

<th class="bookings-grid-header-cell bookings-grid-header-cell-room" width="<?= $width ?>">
	<strong><?= $link ?></strong><br />
	<?php
	$owner = '&nbsp;';
	if ($room->owner) {
		$owner = $room->owner->displayname
			? $room->owner->displayname
			: $room->owner->username;
		$owner = html_escape($owner);
		echo "<span style='font-size: 90%'>{$owner}</span>";
	}
	?>
</th>
