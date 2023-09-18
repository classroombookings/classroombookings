<?php

$form_id = (isset($group))
	? sprintf('room_sort_form_%d', $group->room_group_id)
	: 'room_sort_form_ungrouped';

$room_count = count($rooms);

$table_attrs = ($room_count > 1)
	? "data-sortable='{$form_id}'"
	: '';

$this->table->set_template([
	'table_open' => '<table
		class="border-table"
		' . $table_attrs . '
		style="line-height:1.3;margin-top:16px;margin-bottom:16px"
		width="100%"
		cellpadding="0"
		cellspacing="2"
		border="0"
		>',
	'heading_row_start' => '<tr class="heading">',
]);

$this->table->set_heading([
	['data' => '', 'width' => '5%'],
	['data' => 'Name', 'width' => '25%'],
	['data' => 'Location', 'width' => '25%'],
	['data' => 'Owner', 'width' => '25%'],
	['data' => 'Photo', 'width' => '10%'],
	['data' => 'Actions', 'width' => '10%'],
]);

foreach ($rooms as $room) {

	$sort_img = img('assets/images/ui/arrow_ns.png', FALSE, "alt='sort'");
	$sort_btn = "<div role='button' class='handle' style='cursor:grab;display:inline-block'>{$sort_img}</div>";
	$sort_input = form_hidden('rooms[]', $room->room_id);
	$sort_html = $sort_input . $sort_btn;

	$name = html_escape($room->name);
	$name_html = anchor('rooms/edit/' . $room->room_id, $name);
	$location_html = html_escape($room->location);

	$owner_html = '';
	if ( ! empty($room->user_id)) {
		$owner = empty($room->owner->displayname)
			? $room->owner->username
			: $room->owner->displayname;
		$owner_html = html_escape($owner);
	}

	$photo_html = '';
	if (!empty($room->photo) && $image_url = image_url($room->photo)) {
		$url = site_url("rooms/photo/{$room->room_id}");
		$icon_src = base_url('assets/images/ui/picture.png');
		$icon_el = "<img src='{$icon_src}' width='16' height='16' alt='View Photo'>";
		$photo_html = "<a href='{$url}' up-history='false' up-layer='new drawer' up-target='.room-photo' title='View Photo'>{$icon_el}</a>";
	}

	$actions = [
		'edit' => 'rooms/edit/'.$room->room_id,
		'delete' => 'rooms/delete/'.$room->room_id,
	];
	$actions_html = $this->load->view('partials/editdelete', $actions, TRUE);

	$this->table->add_row([
		($room_count > 1) ? $sort_html : null,
		$name_html,
		$location_html,
		$owner_html,
		$photo_html,
		$actions_html,
	]);
}

if (empty($rooms)) {

	echo msgbox('info', 'No rooms in this group.');

} else {

	$form_attrs = [
		'id' => $form_id,
		'up-target' => "#{$form_id}",
		'up-submit' => '',
		'up-navigate' => 'false',
	];
	echo form_open('rooms/save_pos', $form_attrs, [
		'group' => (isset($group)) ? $group->room_group_id : null,
	]);

	echo $this->table->generate();

	echo form_close();

}
