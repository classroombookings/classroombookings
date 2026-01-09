<?php

echo $this->session->flashdata('saved');

$form_id = (isset($group))
	? sprintf('room_sort_form_%d', $group->room_group_id)
	: 'room_sort_form_ungrouped';

$room_count = isset($rooms) && is_array($rooms) ? count($rooms) : 0;

$sortable_hs = <<<EOS
init
	tell <tbody/> in me
		make a Sortable from yourself, {handle: '.handle', animation: 150, swapThreshold: 0.65}
	end

on end from <tbody/> in me
	set frm to the closest <form/>
	call frm.requestSubmit()
EOS;

$table_attrs = ($room_count > 1)
	? "data-script=\"{$sortable_hs}\""
	: '';

if ($room_count > 0) {

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
		['data' => lang('room.field.name'), 'width' => '25%'],
		['data' => lang('room.field.location'), 'width' => '25%'],
		['data' => lang('room.field.user_id'), 'width' => '20%'],
		['data' => lang('room.field.photo'), 'width' => '10%'],
		['data' => lang('app.actions'), 'width' => '15%'],
	]);

	foreach ($rooms as $room) {

		$sort_img = img(asset_url('assets/images/ui/arrow_ns.png'), FALSE, "alt='sort'");
		$sort_btn = "<div role='button' class='handle' style='cursor:grab;display:inline-block'>{$sort_img}</div>";
		$sort_input = form_hidden('rooms[]', $room->room_id);
		$sort_html = $sort_input . $sort_btn;

		$name = html_escape($room->name);
		$name_html = anchor('setup/rooms/rooms/edit/' . $room->room_id, $name);
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
			$icon_src = asset_url('assets/images/ui/picture.png');
			$icon_el = "<img src='{$icon_src}' width='16' height='16' alt='".lang('room.field.photo')."'>";
			$photo_html = "<a href='{$url}' up-history='false' up-layer='new drawer' up-target='.room-photo' title='".lang('room.field.photo')."'>{$icon_el}</a>";
		}

		$actions_html = iconbar([
		[
			'link' => 'setup/rooms/rooms/edit/'.$room->room_id,
			'name' => '',
			'title' => lang('app.action.update'),
			'icon' => 'edit.png',
		],
		[
			'link' => 'setup/rooms/acl/room/'.$room->room_id,
			'name' => '',
			'title' => lang('acl.access_control'),
			'icon' => 'lock.png',
		],
		[
			'link' => 'setup/rooms/rooms/delete/'.$room->room_id,
			'name' => '',
			'title' => lang('app.action.delete'),
			'icon' => 'delete.png',
		],
	]);

		$this->table->add_row([
			($room_count > 1) ? $sort_html : null,
			$name_html,
			$location_html,
			$owner_html,
			$photo_html,
			$actions_html,
		]);
	}
}

echo iconbar([
	array('setup/rooms/rooms/add/' . $group->room_group_id, lang('room.add.action'), 'add.png'),
]);

if (empty($rooms)) {

	echo msgbox('info', lang('room.no_items'));

} else {

	$form_uri = site_url('setup/rooms/rooms/save_pos');
	$form_attrs = [
		'id' => $form_id,
		'hx-post' => $form_uri,
		'hx-target' => 'this',
		'hx-swap' => 'none',
	];

	echo form_open($form_uri, $form_attrs, [
		'group' => (isset($group)) ? $group->room_group_id : null,
	]);

	echo $this->table->generate();

	echo form_close();

}
