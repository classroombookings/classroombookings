<?php

function department_delete($department = array())
{
	return form_button(array(
		'type' => 'link',
		'url' => 'departments/delete/' . $department['d_id'],
		'class' => 'small red right action-delete',
		'text' => lang('delete'),
		'data' => array(
			'url' => site_url('departments/delete'),
			'redirect' => current_url(),
			'id' => $department['d_id'],
			'name' => $department['d_name'],
			'prompt' => lang('departments_delete_prompt'),
		),
	));
}




function department_user_count($department = array())
{
	return $department['user_count'] . ' ' . ($department['user_count'] == 1 ? lang('departments_members_singular') : lang('departments_members_plural'));
}




function department_block($department = array())
{
	return '<div style="width: 14px; height: 14px; text-indent: -9999px; background: ' . $department['d_colour'] . '">&nbsp;</div>';
}