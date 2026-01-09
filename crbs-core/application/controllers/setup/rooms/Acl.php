<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Acl extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();

		$this->require_logged_in();
		$this->require_permission(Permission::SETUP_ROOMS_ACL);

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


	public function room_group($room_group_id)
	{
		$group = $this->data['group'] = $this->find_room_group($room_group_id);

		$title = sprintf('%s %s: %s', $group->name, strtolower(lang('room_group.group')), lang('acl.access_control'));
		$this->data['title'] = $this->data['showtitle'] = $title;

		$this->data['active'] = "setup/rooms/acl/room_group/{$room_group_id}";
		$this->data['icons'] = $this->load->view('setup/rooms/_icons_primary', $this->data, true);

		$this->data['add_uri'] = 'setup/rooms/acl/add/room_group/'.$room_group_id;

		return $this->acl_index('room_group', $group->room_group_id);
	}


	public function room($room_id)
	{
		$room = $this->data['room'] = $this->find_room($room_id);
		if ($room->room_group_id) {
			$group = $this->data['room_group'] = $this->find_room_group($room->room_group_id);
		}

		$title = sprintf('%s: %s', $room->name, lang('acl.access_control'));

		$this->data['title'] = $this->data['showtitle'] = $title;

		$this->data['active'] = "setup/rooms/acl/room/{$room_id}";
		$this->data['icons'] = $this->load->view('setup/rooms/_icons_rooms', $this->data, true);

		$this->data['add_uri'] = 'setup/rooms/acl/add/room/'.$room_id;

		return $this->acl_index('room', $room->room_id);
	}


	private function acl_index(string $type, int $id)
	{
		$acls = $this->auth_model->get_acl_for_entity($type, $id);

		$this->data['acls'] = $acls;

		$body = $this->load->view('setup/rooms/acl/index', $this->data, true);
		$this->data['body'] = $this->data['icons'] . $body;

		if ($this->input->get_request_header('hx-request')) {
			$this->output->set_header('hx-retarget: .content_area');
			$this->output->set_header('hx-reswap: innerHTML');
			$this->output->set_header('hx-reselect: .content_area');
		}
		return $this->render();
	}


	public function add($entity_type, $entity_id, $context_type)
	{
		$this->data['all_permissions'] = $this->permissions_model->get_scoped(permissions_model::SCOPE_BOOKINGS);
		$this->data['action'] = 'create';

		if ( ! in_array($entity_type, ['room', 'room_group'])) {
			hx_toast('error', lang('acl.error.invalid_entity_type'));
			return '';
		}

		if ( ! in_array($context_type, ['user', 'role', 'department'])) {
			hx_toast('error', lang('acl.error.invalid_context_type'));
			return '';
		}

		$this->data['entity_type'] = $entity_type;
		$this->data['entity_id'] = $entity_id;
		$this->data['context_type'] = $context_type;

		$this->data['cancel_url'] = site_url(sprintf('setup/rooms/acl/%s/%d', $entity_type, $entity_id));

		switch ($context_type) {
			case 'user':
				$users = $this->users_model->Get(NULL, NULL, NULL);
				$user_options = ['' => ''];
				foreach ($users as $user) {
					$label = empty($user->displayname)
						? $user->username
						: sprintf('%s (%s)', $user->username, $user->displayname)
						;
					$user_options[ $user->user_id ] = $label;
				}
				$this->data['user_options'] = $user_options;
				$this->data['context_input'] = 'setup/rooms/acl/_add_user';
				break;
			case 'role':
				$roles = $this->roles_model->get_all();
				$role_options = ['' => ''];
				foreach ($roles as $role) {
					$role_options[ $role->role_id ] = $role->name;
				}
				$this->data['role_options'] = $role_options;
				$this->data['context_input'] = 'setup/rooms/acl/_add_role';
				break;
			case 'department':
				$departments = $this->departments_model->Get(NULL, NULL, NULL);
				$department_options = ['' => ''];
				if (is_array($departments)) {
					foreach ($departments as $department) {
						$department_options[ $department->department_id ] = $department->name;
					}
				}
				$this->data['department_options'] = $department_options;
				$this->data['context_input'] = 'setup/rooms/acl/_add_department';
				break;
		}

		if ($this->input->post('action')) {
			$this->save();
		}

		$this->load->view('setup/rooms/acl/add', $this->data);
	}


	public function edit($acl_id)
	{
		$this->data['acl'] = $this->auth_model->get_acl($acl_id);
		$this->data['all_permissions'] = $this->permissions_model->get_scoped(permissions_model::SCOPE_BOOKINGS);

		if ($this->input->post('action')) {
			$this->save();
		}

		$this->load->view('setup/rooms/acl/edit', $this->data);
	}


	private function save()
	{
		$acl_id = $this->input->post('acl_id');

		switch ($this->input->post('action')) {

			case 'add':
				if (empty($this->input->post('context_id'))) {
					hx_toast('error', lang('acl.error.no_context_selected'));
					return;
				}
				$acl_data = [
					'entity_type' => $this->input->post('entity_type'),
					'entity_id' => $this->input->post('entity_id'),
					'context_type' => $this->input->post('context_type'),
					'context_id' => $this->input->post('context_id'),
				];
				$acl_id = $this->auth_model->insert_acl($acl_data, $this->input->post('permissions'));
				if ($acl_id) {
					hx_toast('success', lang('acl.create.success'));
					$method = $this->input->post('entity_type');
					return $this->{$method}($this->input->post('entity_id'));
				} else {
					hx_toast('error', lang('acl.create.error'));
					return;
				}
				break;

			case 'edit':
				$result = $this->auth_model->set_acl_permissions($acl_id, $this->input->post('permissions'));
				if ($result) {
					$msg = sprintf(lang('acl.update.success'), html_escape($this->data['acl']->context_label));
					hx_toast('success', $msg);
					$method = $this->data['acl']->entity_type;
					return $this->{$method}($this->data['acl']->entity_id);
				} else {
					hx_toast('error', lang('acl.update.error'));
					return;
				}
				break;
		}
	}


	public function delete($acl_id)
	{
		$acl = $this->auth_model->get_acl($acl_id);

		$result = $this->auth_model->delete_acl($acl_id);
		if ($result) {
			$msg = sprintf(lang('acl.delete.success'), html_escape($acl->context_label));
			hx_toast('success', $msg);
		} else {
			hx_toast('error', lang('acl.delete.error'));
		}

		return '';
	}


	/**
	 * Get and return a group by ID or show error page.
	 *
	 */
	private function find_room_group($room_group_id)
	{
		if (empty($room_group_id)) {
			show_404();
		}

		$group = $this->room_groups_model->get($room_group_id);

		if (empty($group)) {
			show_404();
		}

		return $group;
	}


	private function find_room($room_id)
	{
		if (empty($room_id)) {
			show_404();
		}

		$room = $this->rooms_model->get_by_id($room_id);

		if (empty($room)) {
			show_404();
		}

		return $room;
	}


}
