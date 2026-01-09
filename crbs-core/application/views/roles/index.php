<?php

echo $this->session->flashdata('saved');

echo iconbar([
	['roles/add', lang('role.add.action'), 'add.png'],
]);

//

$this->table->set_template([
	'table_open' => '<table
		class="border-table has-icons"
		style="line-height:1.3;margin-top:16px;margin-bottom:16px"
		width="100%"
		cellspacing="2"
		border="0"
		>',
	'heading_row_start' => '<tr class="heading">',
]);

$this->table->set_heading([
	['data' => lang('role.field.name'), 'width' => '25%'],
	['data' => lang('role.field.description'), 'width' => '25%'],
	['data' => lang('role.field.user_count'), 'width' => '10%'],
	['data' => lang('constraint.max_active_bookings.short'), 'width' => '20%'],
	['data' => lang('app.actions'), 'width' => '10%'],
]);

foreach ($roles as $idx => $role) {

	$name = html_escape($role->name);
	$name_html = anchor('roles/edit/' . $role->role_id, $name);

	$user_count = sprintf('%d', $role->user_count);

	$max_active_bookings = ($role->max_active_bookings == null)
		? sprintf('<em>%s</em>', lang('app.unlimited'))
		: sprintf('%d', $role->max_active_bookings)
		;

	$description_html = (empty($role->description))
		? ''
		: word_limiter(html_escape($role->description), 8)
		;

	$actions = [
		'edit' => 'roles/edit/' . $role->role_id,
		'delete' => 'roles/delete/' . $role->role_id,
	];
	$actions_html = $this->load->view('partials/editdelete', $actions, TRUE);

	$this->table->add_row([
		$name_html,
		$description_html,
		$user_count,
		$max_active_bookings,
		$actions_html,
	]);
}

if (empty($roles)) {

	echo msgbox('info', lang('role.no_items'));

} else {

	echo $this->table->generate();

}
