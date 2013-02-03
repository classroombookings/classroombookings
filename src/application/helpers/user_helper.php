<?php

function user_classes($user = array())
{
	$classes = array();
	if (element('u_enabled', $user, 0) == 0) $classes[] = 'user-disabled';
	if (element('u_online', $user, 0) == 1) $classes[] = 'user-online';
	return implode(' ', $classes);
}




function user_auth_icon($user = array(), $type = 'tag')
{
	switch ($user['u_auth_method'])
	{
		case 'local':
			$icon = 'database.png';
			$text = 'Local';
			break;
		case 'ldap':
			$icon = 'user-ldap.png';
			$text = 'LDAP';
			break;
		default:
			$icon = '';
			$text = 'Unknown';
	}
	
	if ($type === 'icon')
	{
		return $icon;
	}
	
	if ($type === 'tag')
	{
		return '<img src="img/ico/' . $icon . '" alt="' . $text . '" title="' . $text . '">';
	}
}




function user_last_login($user = array(), $format = 'd/m/Y H:i')
{
	if (empty($user['u_last_login']) OR $user['u_last_login'] === '0000-00-00 00:00:00')
	{
		return 'Never';
	}
	
	return date($format, strtotime($user['u_last_login']));
}




function user_delete($user = array())
{
	return form_button(array(
		'type' => 'link',
		'url' => 'users/delete/' . $user['u_id'],
		'class' => 'small red right',
		'text' => 'Delete',
	));
}




function user_import_status($user = array())
{
	$statuses = array(
		'ignored' => 'grey',
		'skipped' => 'blue',
		'updated' => 'orange',
		'added' => 'green',
		'failed' => 'red',
	);
	
	$str = '<span class="right label ' . $statuses[$user['action']] . '">' . lang('users_import_action_' . $user['action']) . '</span>';
	return $str;
}