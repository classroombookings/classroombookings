<?php

echo $this->session->flashdata('saved');

echo iconbar([
	['rooms/add', 'Add Room', 'add.png'],
]);

echo "<div style='position:relative' id='sort_msg' up-hungry>";
$msg = $message ?? '<br>';
if ( ! empty($msg)) {
	echo "<div style='float:right;position:absolute;top:-48px;right:0'>{$msg}</div>";
}
echo "</div>";


//
$open_group_id = $open_group_id ?? null;
$items = [];
foreach ($groups as $group) {
	$is_open =
	$items[] = [
		'url' => site_url('room_groups/edit/' . $group->room_group_id),
		'title' => sprintf('%s <span>(%d)</span>', $group->name, $group->room_count),
		'active' => $group->room_group_id == $open_group_id,
	];
}


foreach ($groups as $group) {

	$this->load->view('rooms/index_groups/group', [
		'group' => $group,
		'rooms' => $rooms[$group->room_group_id] ?? [],
	]);

}

$this->load->view('rooms/index_groups/group', [
	'group' => null,
	'rooms' => $rooms['ungrouped'] ?? [],
]);
