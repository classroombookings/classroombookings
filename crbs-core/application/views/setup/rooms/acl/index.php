<?php
$icons = [
	'user' => 'school_manage_users.png',
	'role' => 'vcard_key.png',
	'department' => 'school_manage_departments.png',
];

echo "<div hx-target='#add_target' style='margin-top:32px'>";
echo iconbar([
	[
		'link' => $add_uri.'/user',
		'attrs' => ['hx-get' => site_url($add_uri.'/user')],
		'name' => lang('acl.action.add_user'),
		'icon' => 'school_manage_users.png',
	],
	[
		'link' => $add_uri.'/role',
		'attrs' => ['hx-get' => site_url($add_uri.'/role')],
		'name' => lang('acl.action.add_role'),
		'icon' => 'vcard_key.png',
	],
	[
		'link' => $add_uri.'/department',
		'attrs' => ['hx-get' => site_url($add_uri.'/department')],
		'name' => lang('acl.action.add_department'),
		'icon' => 'school_manage_departments.png',
	],
]);
echo "</div>";
echo "<div id='add_target'></div>";

echo "<br>";

if (empty($acls)) {

	echo msgbox('info', lang('acl.no_items'));

} else {

	foreach ($acls as $acl) {
		$this->load->view('setup/rooms/acl/_acl_row', ['acl' => $acl]);
	}

}
