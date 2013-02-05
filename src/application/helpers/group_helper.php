<?php

function group_delete($group = array())
{
	return form_button(array(
		'type' => 'link',
		'url' => 'groups/delete/' . $group['g_id'],
		'class' => 'small red right action-delete',
		'text' => lang('delete'),
		'data' => array(
			'url' => site_url('groups/delete'),
			'redirect' => current_url(),
			'id' => $group['g_id'],
			'name' => $group['g_name'],
			'prompt' => lang('groups_delete_prompt'),
		),
	));
}