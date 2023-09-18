<?php


echo $this->session->flashdata('saved');

echo iconbar([
	['room_groups/add', 'Add Group', 'add.png'],
]);

//

$this->table->set_template([
	'table_open' => '<table
		class="border-table"
		data-sortable="group_sort_form"
		style="line-height:1.3;margin-top:16px;margin-bottom:16px"
		width="100%"
		cellspacing="2"
		border="0"
		>',
	'heading_row_start' => '<tr class="heading">',
]);

$this->table->set_heading([
	['data' => '', 'width' => '5%'],
	['data' => 'Name', 'width' => '25%'],
	['data' => 'Rooms', 'width' => '10%'],
	['data' => 'Description', 'width' => '45%'],
	['data' => 'Actions', 'width' => '10%'],
]);

foreach ($groups as $idx => $group) {

	$sort_img = img('assets/images/ui/arrow_ns.png', FALSE, "alt='sort'");
	$sort_btn = "<div role='button' class='handle' style='cursor:grab;display:inline-block'>{$sort_img}</div>";
	$sort_input = form_hidden('groups[]', $group->room_group_id);
	$sort_html = $sort_input . $sort_btn;

	$name = html_escape($group->name);
	$name_html = anchor('room_groups/edit/' . $group->room_group_id, $name);

	$room_count = sprintf('%d', $group->room_count);
	$rooms_html = anchor('rooms?group=' . $group->room_group_id, $room_count);

	$description_html = (empty($group->description))
		? ''
		: word_limiter(html_escape($group->description), 8)
		;

	$actions = [
		'edit' => 'room_groups/edit/' . $group->room_group_id,
		'delete' => 'room_groups/delete/' . $group->room_group_id,
	];
	$actions_html = $this->load->view('partials/editdelete', $actions, TRUE);

	$this->table->add_row([
		$sort_html,
		$name_html,
		$rooms_html,
		$description_html,
		$actions_html,
	]);
}

if (empty($groups)) {

	echo msgbox('info', 'No room groups added yet.');

} else {

	$form_attrs = [
		'id' => 'group_sort_form',
		'up-target' => '.content_area',
		'up-submit' => '',
		'up-navigate' => 'false',
	];
	echo form_open('room_groups/save_pos', $form_attrs);

	echo "<div style='position:relative'>";

	$msg = $message ?? '<br>';
	if ( ! empty($msg)) {
		echo "<div style='float:right;position:absolute;top:-48px;right:0'>{$msg}</div>";
	}
	echo $this->table->generate();
	echo "</div>";

	echo form_close();

}
