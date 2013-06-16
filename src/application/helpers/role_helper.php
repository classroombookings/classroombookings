<?php

function role_delete($role = array())
{
	return form_button(array(
		'type' => 'link',
		'url' => 'roles/delete/' . $role['r_id'],
		'class' => 'small red action-delete',
		'text' => lang('delete'),
		'data' => array(
			'url' => site_url('roles/delete'),
			'redirect' => current_url(),
			'id' => $role['r_id'],
			'name' => $role['r_name'],
			'prompt' => lang('roles_delete_prompt'),
		),
	));
}