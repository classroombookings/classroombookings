<?php

echo $this->session->flashdata('saved');

$iconbar = iconbar(array(
	array('users/add', lang('user.add.action'), 'add.png'),
));

echo $iconbar;

$this->load->view('users/filter');

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
	['data' => sort_link('users', 'enabled', lang('user.field.enabled')), 'width' => '5%'],
	['data' => sort_link('users', 'username', lang('user.field.username')), 'width' => '20%'],
	['data' => sort_link('users', 'displayname', lang('user.field.displayname')), 'width' => '20%'],
	['data' => sort_link('users', 'role', lang('role.role')), 'width' => '15%'],
	['data' => sort_link('users', 'department', lang('department.department')), 'width' => '15%'],
	['data' => sort_link('users', 'lastlogin', lang('user.last_logged_in')), 'width' => '15%'],
	['data' => lang('app.actions'), 'width' => '5%'],
]);

foreach ($users as $user) {

	$img_enabled = ($user->enabled == 1) ? 'enabled.png' : 'no.png';
	$enabled_html = img([
		'src' => asset_url("assets/images/ui/{$img_enabled}"),
		'width' => "16",
		'height' => "16",
		'alt' => $img_enabled,
	]);

	$name = html_escape($user->username);
	$username_html = anchor('users/edit/'.$user->user_id, $name);

	if ($user->displayname == '') {
		$user->displayname = $user->username;
	}
	$display_html = html_escape($user->displayname);

	$role_html = html_escape($user->role);

	$department_html = html_escape($user->department);

	if ($user->lastlogin == '0000-00-00 00:00:00' || empty($user->lastlogin)) {
		$last_login_html = lang('app.never');
	} else {
		$last_login_html = date("d/m/Y H:i", strtotime((string) $user->lastlogin));
	}

	$actions = [
		'edit' => 'users/edit/' . $user->user_id,
		'delete' => 'users/delete/' . $user->user_id,
	];
	$actions_html = $this->load->view('partials/editdelete', $actions, TRUE);

	$this->table->add_row([
		// $type_html,
		$enabled_html,
		$username_html,
		$display_html,
		$role_html,
		$department_html,
		$last_login_html,
		$actions_html,
	]);

}

//


echo "<div id='users_list'>";

if (empty($users)) {

	echo msgbox('info', lang('user.no_items'));

} else {

	echo $this->table->generate();
	echo $pagelinks;

}

echo "</div>";

echo $iconbar;
