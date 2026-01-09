<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Access_checker extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();

		$this->require_logged_in();
		$this->require_any_permission([Permission::SETUP_ROOMS_ACL, Permission::SETUP_USERS]);

		$this->load->model([
			'rooms_model',
			'room_groups_model',
			'users_model',
			'departments_model',
			'roles_model',
			'auth_model',
			'permissions_model',
		]);
	}


	public function index()
	{
		$all_users = $this->users_model->Get(NULL, NULL, NULL);
		foreach ($all_users as $user) {
			$label = empty($user->displayname)
				? $user->username
				: sprintf('%s (%s)', $user->username, $user->displayname)
				;
			$user_options[ $user->user_id ] = $label;
		}
		$this->data['user_options'] = $user_options;

		$all_rooms = $this->rooms_model->get_all_grouped();
		$room_options = [];
		foreach ($all_rooms as $group => $rooms) {
			$group_name = $rooms[0]->group->name;
			foreach ($rooms as $room) {
				$room_options[$group_name][ $room->room_id ] = $room->name;
			}
		}
		$this->data['room_options'] = $room_options;

		if ($this->input->post('room_id') && $this->input->post('user_id')) {
			$user_id = $this->input->post('user_id');
			$room_id = $this->input->post('room_id');
			$user = $this->users_model->Get($user_id);
			$room = $this->rooms_model->get_by_id($room_id);
			if ($user && $room) {
				$this->data['user'] = $user;
				$this->data['room'] = $room;
				$result = $this->get_unified_permissions($user_id, $room_id);
				$this->data['result'] = $result;
			}
		}

		$columns = array(
			'c1' => array(
				'content' => $this->load->view('setup/access_checker/index_form', $this->data, TRUE),
				'width' => '50%',
			),
			'c2' => array(
				'content' => $this->load->view('setup/access_checker/index_result', $this->data, TRUE),
				'width' => '50%',
			),
		);

		$columns = $this->load->view('columns', $columns, TRUE);

		$this->data['title'] = $this->data['showtitle'] = lang('acl.check_user_access');

		$this->data['active'] = 'setup/access_checker';
		$icons = $this->load->view('setup/rooms/_icons_primary', $this->data, true);

		$this->data['body'] = $icons . $columns;

		return $this->render();
	}


	public function user($user_id)
	{
		$user = $this->users_model->Get($user_id);
		if (!$user) {
			echo '';
			return;
		}

		$all_rooms = $this->rooms_model->get_all_grouped();
		$room_options = [];
		foreach ($all_rooms as $group => $rooms) {
			$group_name = $rooms[0]->group->name;
			foreach ($rooms as $room) {
				$room_options[$group_name][ $room->room_id ] = $room->name;
			}
		}

		$this->data['user'] = $user;
		$this->data['room_options'] = $room_options;

		if ($this->input->post('room_id')) {
			$result = $this->get_unified_permissions($user_id, $this->input->post('room_id'));
			$this->data['result'] = $result;
		}

		$this->load->view('setup/access_checker/user', $this->data);
	}


	public function room($room_id)
	{
		$room = $this->rooms_model->get_by_id($room_id);
		if (!$room) {
			echo '';
			return;
		}

		$all_users = $this->users_model->Get(NULL, NULL, NULL);
		foreach ($all_users as $user) {
			$label = empty($user->displayname)
				? $user->username
				: sprintf('%s (%s)', $user->username, $user->displayname)
				;
			$user_options[ $user->user_id ] = $label;
		}

		$this->data['room'] = $room;
		$this->data['user_options'] = $user_options;

		if ($this->input->post('user_id')) {
			$result = $this->get_unified_permissions($this->input->post('user_id'), $room_id);
			$this->data['result'] = $result;
		}

		$this->load->view('setup/access_checker/room', $this->data);
	}


	private function get_unified_permissions($user_id, $room_id)
	{
		$user = $this->users_model->Get($user_id);

		$from_role = [];
		if ($user->role_id) {
			$from_role = $this->roles_model->get_permission_names($user->role_id);
		}

		$from_acls = [];
		$acl_permissions = $this->auth_model->user_room_permissions($user_id, $room_id);
		$from_acls = array_column($acl_permissions, 'permission');

		$result = array_merge($from_role, $from_acls);
		$result = array_unique($result);

		$out = [];
		$all_permissions = $this->permissions_model->get_scoped(Permissions_model::SCOPE_BOOKINGS);
		foreach ($all_permissions as $group => $permissions) {
			foreach ($permissions as $id => $name) {
				$out[$group][$name] = in_array($name, $result);
			}
		}

		return $out;
	}

}
