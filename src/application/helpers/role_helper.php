<?php

function role_delete($role = array())
{
	return form_button(array(
		'type' => 'link',
		'url' => 'roles/delete/' . $role['r_id'],
		'class' => 'small red right action-delete',
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


function role_assign_button($role = array(), $type = '')
{
	return form_button(array(
		'type' => 'link',
		'url' => uri_string() . '#',
		'class' => 'small green button assign-item',
		'text' => lang('add'),
		'data' => array(
			'r_id' => $role['r_id'],
			'r_name' => $role['r_name'],
			'type' => $type,
			'type_name' => lang('roles_entity_type_' . $type),
		),
	));
}