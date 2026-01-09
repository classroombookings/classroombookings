<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Roles extends MY_Controller
{


	public function __construct()
	{
		parent::__construct();

		$this->require_logged_in();
		$this->require_permission(Permission::SETUP_ROLES);

		$this->load->model([
			'roles_model',
			'users_model',
			'permissions_model',
		]);

		$this->data['showtitle'] = lang('role.roles');
	}


	public function index()
	{
		$this->load->library('table');

		$this->data['roles'] = $this->roles_model->get_all();

		$this->data['title'] = lang('role.roles');

		$body = $this->load->view('roles/index', $this->data, TRUE);

		$this->data['body'] = $body;

		return $this->render();
	}


	public function add()
	{
		$this->data['title'] = $this->data['showtitle'] = lang('role.add.title');

		$this->data['all_permissions'] = $this->permissions_model->get_scoped();

		if ($this->input->post()) {
			$this->save_role();
		}

		$form = $this->load->view('roles/add', $this->data, TRUE);
		$users = $this->load->view('roles/user_list', $this->data, TRUE);

		$columns = [
			'c1' => ['content' => $form, 'width' => '60%'],
			'c2' => ['content' => $users, 'width' => '40%'],
		];

		$body = $this->load->view('columns', $columns, TRUE);

		$this->data['body'] = $body;

		return $this->render();
	}


	public function edit($role_id)
	{
		$this->data['title'] = $this->data['showtitle'] = lang('role.edit.title');

		$this->data['role'] = $this->find_role($role_id);
		$this->data['role_users'] = $this->users_model->get_by_role($role_id);
		$this->data['all_permissions'] = $this->permissions_model->get_scoped();

		if ($this->input->post()) {
			$this->save_role($role_id);
		}

		$form = $this->load->view('roles/add', $this->data, TRUE);
		$users = $this->load->view('roles/user_list', $this->data, TRUE);

		$columns = [
			'c1' => ['content' => $form, 'width' => '60%'],
			'c2' => ['content' => $users, 'width' => '40%'],
		];

		$body = $this->load->view('columns', $columns, TRUE);

		$this->data['body'] = $body;

		return $this->render();
	}


	/**
	 * Add or edit a role
	 *
	 */
	private function save_role($role_id = NULL)
	{
		$this->load->library('form_validation');

		$this->form_validation->set_rules('name', 'lang:role.field.name', 'required|max_length[100]');
		$this->form_validation->set_rules('description', 'lang:role.field.description', "max_length[255]");
		$this->form_validation->set_rules('max_active_bookings', 'lang:constraint.max_active_bookings.short', "is_natural");
		$this->form_validation->set_rules('range_min', 'lang:constraint.range_min', "is_natural");
		$this->form_validation->set_rules('range_max', 'lang:constraint.range_max', "is_natural");
		$this->form_validation->set_rules('recur_max_instances', 'lang:constraint.recur_max_instances', "is_natural");
		$this->form_validation->set_rules('permission_ids[]', 'lang:permission.permissions', "integer");

		$data = array(
			'name' => $this->input->post('name'),
			'description' => $this->input->post('description'),
			'max_active_bookings' => $this->input->post('max_active_bookings'),
			'range_min' => $this->input->post('range_min'),
			'range_max' => $this->input->post('range_max'),
			'recur_max_instances' => $this->input->post('recur_max_instances'),
		);

		if ($this->form_validation->run() == FALSE) {
			return FALSE;
		}

		$uri = "roles";

		if ($role_id) {
			if ($this->roles_model->update($role_id, $data)) {
				$line = sprintf(lang('role.update.success'), $data['name']);
				$flashmsg = msgbox('info', $line);
			} else {
				$line = lang('role.update.error');
				$flashmsg = msgbox('error', $line);
			}
		} else {
			if ($role_id = $this->roles_model->insert($data)) {
				$line = sprintf(lang('role.create.success'), $data['name']);
				$flashmsg = msgbox('info', $line);
			} else {
				$line = lang('role.create.error');
				$flashmsg = msgbox('error', $line);
			}
		}

		if (!empty($role_id)) {
			$permissions = $this->input->post('permissions') ?? [];
			$this->roles_model->set_permissions($role_id, $permissions);
		}

		$this->session->set_flashdata('saved', $flashmsg);
		redirect($uri);
	}


	/**
	 * Delete a role
	 *
	 */
	public function delete($id)
	{
		$role = $this->find_role($id);

		if ($this->input->post('id') == $id) {
			$this->roles_model->delete($this->input->post('id'));
			$line = sprintf(lang('role.delete.success'), $role->name);
			$flashmsg = msgbox('info', $line);
			$this->session->set_flashdata('saved', $flashmsg);
			redirect('roles');
		}

		$this->data['action'] = current_url();
		$this->data['id'] = $id;
		$this->data['cancel'] = 'roles';
		$this->data['text'] = lang('role.delete.warning');

		$this->data['title'] = $this->data['showtitle'] = sprintf(lang('role.delete.title'), $role->name);

		$title = "<h2>{$this->data['title']}</h2>";
		$body = $this->load->view('partials/deleteconfirm', $this->data, TRUE);

		$this->data['body'] = $body;

		return $this->render();
	}


	/**
	 * Get and return a role by ID or show error page.
	 *
	 */
	private function find_role($role_id)
	{
		if (empty($role_id)) {
			show_404();
		}

		$role = $this->roles_model->get($role_id);

		if (empty($role)) {
			show_404();
		}

		return $role;
	}


}
