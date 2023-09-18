<?php
$rooms_grouped = [];
foreach ($rooms as $room) {
	$group_id = $room->room_group_id ?? 'ungrouped';
	$rooms_grouped[ $group_id ][ $room->room_id ] = $room;
}

$rooms_html = '';

$base_uri = site_url('bookings');

foreach ($room_groups as $group) {

	$rooms_html .= "<div class='block b-25'>";

	$rooms_html .= "<h5 style='margin:0'>" . html_escape($group->name) . "</h5>";

	if ( ! empty($group->description)) {
		$desc = html_escape($group->description);
		$rooms_html .= "<p style='margin:4px 0 0 0'><small class='hint'>{$desc}</small></p>";
	}

	$rooms_html .= "<ul class='plain-list' style='margin-top:16px'>";

	$items = $rooms_grouped[$group->room_group_id] ?? [];
	foreach ($items as $room) {
		$query = [
			'room' => $room->room_id,
			'date' => $current_date,
		];
		$url = $base_uri . '?' . http_build_query($query);
		$room_name = html_escape($room->name);
		if ($room->room_id == $current_room) {
			$room_name = "<strong>{$room_name}</strong>";
		}
		$link = anchor($url, $room_name, [
			'attrs' => 'up-follow up-preload',
		]);
		$rooms_html .= "<li>{$link}</li>";

	}

	$rooms_html .= "</ul>";

	$rooms_html .= "</div>";

}


?>

<div class='block-group room-groups'>
	<?= $rooms_html ?>
</div>

<style>
.room-groups .block {
	padding-right: 20px;
}
.room-groups .block:nth-child(4n+1) {
    clear: left;
}
</style>

