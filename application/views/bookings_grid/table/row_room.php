<?php
$url = site_url("rooms/info/{$room->room_id}");
$name = html_escape($room->name);
$link = "<a href='{$url}' up-drawer='.room-info' up-history='false' up-tooltip='View room details' up-preload>{$name}</a>";
?>

<th class="bookings-grid-header-cell bookings-grid-header-cell-room">
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
