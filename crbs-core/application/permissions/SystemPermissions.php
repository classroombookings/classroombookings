<?php

namespace app\permissions;

defined('BASEPATH') OR exit('No direct script access allowed');

class SystemPermissions
{

	private $CI;

	private $role_permissions = [];

	public function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->load->model('roles_model');
	}

	public function system(?object $user)
	{
		$allowed = [];
		if (empty($user->role_id)) return $allowed;

		$role_permissions = $this->CI->roles_model->get_permission_names($user->role_id);
		$allowed = $this->only($role_permissions, 'system');
		return $allowed;
	}

	public function setup(?object $user)
	{
		$allowed = [];
		if (empty($user->role_id)) return $allowed;

		$role_permissions = $this->CI->roles_model->get_permission_names($user->role_id);
		$allowed = $this->only($role_permissions, 'setup');
		return $allowed;
	}

	private function only(array $permission_list, $prefix)
	{
		if (empty($permission_list)) return $permission_list;

		$mapped = array_map(function($permission) use ($prefix) {
			[$section,$action] = explode('.', $permission);
			if ($section === $prefix) return $action;
			return false;
		}, $permission_list);

		return array_filter($mapped);
	}


}
