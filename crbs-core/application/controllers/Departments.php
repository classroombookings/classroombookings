<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Departments extends MY_Controller
{


	public function __construct()
	{
		parent::__construct();

		$this->require_logged_in();
		$this->require_permission(Permission::SETUP_DEPARTMENTS);

		$this->load->library('pagination');
		$this->load->model('crud_model');
		$this->load->model('departments_model');
	}


	function index($page = NULL)
	{
		$pagination_config = array(
			'base_url' => site_url('departments/index'),
			'total_rows' => $this->crud_model->Count('departments'),
			'per_page' => 25,
			'full_tag_open' => '<p class="pagination">',
			'full_tag_close' => '</p>',
		);

		$this->pagination->initialize($pagination_config);

		$this->data['pagelinks'] = $this->pagination->create_links();
		$this->data['departments'] = $this->departments_model->Get(NULL, $pagination_config['per_page'], $page);

		$this->data['title'] = lang('department.departments');
		$this->data['showtitle'] = $this->data['title'];
		$this->data['body'] = $this->load->view('departments/departments_index', $this->data, TRUE);

		return $this->render();
	}


	/**
	 * Add a new department
	 *
	 */
	function add()
	{
		// Load view
		$this->data['title'] = lang('department.add.title');
		$this->data['showtitle'] = $this->data['title'];
		$this->data['body'] = $this->load->view('departments/departments_add', NULL, TRUE);

		return $this->render();
	}


	/**
	 * Edit a department
	 *
	 */
	function edit($department_id = NULL)
	{
		$this->data['department'] = $this->departments_model->Get($department_id);

		if (empty($this->data['department'])) {
			show_404();
		}

		$this->data['title'] = lang('department.edit.title');
		$this->data['showtitle'] = $this->data['title'];
		$this->data['body'] = $this->load->view('departments/departments_add', $this->data, TRUE);

		return $this->render();
	}


	/**
	 * Save changes to add/edit a department
	 *
	 */
	function save()
	{
		$department_id = $this->input->post('department_id');

		$this->load->library('form_validation');

		$this->form_validation->set_rules('department_id', 'lang:app.id', 'integer');
		$this->form_validation->set_rules('name', 'lang:department.field.name', 'required|min_length[1]|max_length[50]');
		$this->form_validation->set_rules('description', 'lang:department.field.description', 'max_length[255]');

		if ($this->form_validation->run() == FALSE) {
			return (empty($department_id) ? $this->add() : $this->edit($department_id));
		}

		$department_data = array(
			'name' => $this->input->post('name'),
			'description' => $this->input->post('description'),
			'icon' => '',
		);

		if (empty($department_id)) {

			$department_id = $this->departments_model->insert($department_data);

			if ($department_id) {
				$line = sprintf(lang('department.create.success'), $department_data['name']);
				$flashmsg = msgbox('info', $line);
			} else {
				$line = sprintf(lang('department.create.error'));
				$flashmsg = msgbox('error', $line);
			}

		} else {

			if ($this->departments_model->update($department_id, $department_data)) {
				// update
				$line = sprintf(lang('department.update.success'), $department_data['name']);
				$flashmsg = msgbox('info', $line);
			} else {
				$line = lang('department.update.error');
				$flashmsg = msgbox('error', $line);
			}

		}

		$this->session->set_flashdata('saved', $flashmsg);
		redirect('departments');
	}


	/**
	 * Delete a department
	 *
	 */
	function delete($id = NULL)
	{
		$department = $this->departments_model->Get($id);
		if ( ! $department) show_404();

		if ($this->input->post('id') == $id) {
			$this->departments_model->delete($this->input->post('id'));
			$line = sprintf(lang('department.delete.success'), $department->name);
			$flashmsg = msgbox('info', $line);
			$this->session->set_flashdata('saved', $flashmsg);
			redirect('departments');
			return;
		}

		$this->data['action'] = 'departments/delete/'.$id;
		$this->data['id'] = $id;
		$this->data['cancel'] = 'departments';
		$this->data['text'] = lang('department.delete.warning');

		$this->data['title'] = sprintf(lang('department.delete.title'), $department->name);
		$this->data['showtitle'] = $this->data['title'];
		$this->data['body'] = $this->load->view('partials/deleteconfirm', $this->data, TRUE);

		return $this->render();
	}


}
