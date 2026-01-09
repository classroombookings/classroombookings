<?php


echo $this->session->flashdata('saved');

echo iconbar([
	['setup/rooms/groups/add', lang('room_group.add.action'), 'add.png'],
]);

//

$this->table->set_template([
	'table_open' => '<table
		class="border-table"
		data-script="
			init
				tell <tbody/> in me
					make a Sortable from yourself, {handle: \'.handle\', animation: 150, swapThreshold: 0.65}
				end

			on end from <tbody/> in me
				set frm to the closest <form/>
				call frm.requestSubmit()

		"
		style="line-height:1.3;margin-top:16px;margin-bottom:16px"
		width="100%"
		cellspacing="2"
		border="0"
		>',
	'heading_row_start' => '<tr class="heading">',
]);

$this->table->set_heading([
	['data' => '', 'width' => '5%'],
	['data' => lang('room_group.field.name'), 'width' => '25%'],
	['data' => lang('room.rooms'), 'width' => '10%'],
	['data' => lang('room_group.field.description'), 'width' => '45%'],
	['data' => lang('app.actions'), 'width' => '15%'],
]);

if (is_array($groups)) {

	foreach ($groups as $idx => $group) {

		$sort_img = img(asset_url('assets/images/ui/arrow_ns.png'), FALSE, "alt='sort'");
		$sort_btn = "<div role='button' class='handle' style='cursor:grab;display:inline-block'>{$sort_img}</div>";
		$sort_input = form_hidden('groups[]', $group->room_group_id);
		$sort_html = $sort_input . $sort_btn;

		$name = html_escape($group->name);
		$name_html = anchor('setup/rooms/groups/view/' . $group->room_group_id, $name);

		$room_count = sprintf('%d', $group->room_count);
		$rooms_html = $room_count;

		$description_html = (empty($group->description))
			? ''
			: word_limiter(html_escape($group->description), 8)
			;

		$actions_html = iconbar([
			[
				'link' => 'setup/rooms/groups/edit/'.$group->room_group_id,
				'name' => '',
				'title' => lang('app.action.update'),
				'icon' => 'edit.png',
			],
			[
				'link' => 'setup/rooms/acl/room_group/'.$group->room_group_id,
				'name' => '',
				'title' => lang('acl.access_control'),
				'icon' => 'lock.png',
			],
			[
				'link' => 'setup/rooms/groups/delete/'.$group->room_group_id,
				'name' => '',
				'title' => lang('app.action.delete'),
				'icon' => 'delete.png',
			],
		]);

		$this->table->add_row([
			$sort_html,
			$name_html,
			$rooms_html,
			$description_html,
			$actions_html,
		]);

	}

}

if (empty($groups)) {

	echo msgbox('info', lang('room_group.no_items'));

} else {

	$form_uri = site_url('setup/rooms/groups/save_pos');
	$form_attrs = [
		'id' => 'group_sort_form',
		'hx-post' => $form_uri,
		'hx-target' => 'this',
		'hx-swap' => 'none',
	];
	echo form_open($form_uri, $form_attrs);

	echo $this->table->generate();

	echo form_close();

}
