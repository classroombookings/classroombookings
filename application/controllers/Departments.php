<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Departments extends MY_Controller
{




	public function __construct()
	{
		parent::__construct();

		$this->require_logged_in();
		$this->require_auth_level(ADMINISTRATOR);

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
		// Get list of rooms from database
		$this->data['departments'] = $this->departments_model->Get(NULL, $pagination_config['per_page'], $page);

		$this->data['title'] = 'Departments';
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
		$this->data['title'] = 'Add Department';
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

		$this->data['title'] = 'Edit Department';
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

		$this->form_validation->set_rules('department_id', 'ID', 'integer');
		$this->form_validation->set_rules('name', 'Name', 'required|min_length[1]|max_length[50]');
		$this->form_validation->set_rules('description', 'Description', 'max_length[255]');

		if ($this->form_validation->run() == FALSE) {
			return (empty($department_id) ? $this->add() : $this->edit($department_id));
		}

		$department_data = array(
			'name' => $this->input->post('name'),
			'description' => $this->input->post('description'),
			'icon' => '',
		);

		if (empty($department_id)) {

			$department_id = $this->departments_model->Add($department_data);

			if ($department_id) {
				$line = sprintf($this->lang->line('crbs_action_added'), $department_data['name']);
				$flashmsg = msgbox('info', $line);
			} else {
				$line = sprintf($this->lang->line('crbs_action_dberror'), 'adding');
				$flashmsg = msgbox('error', $line);
			}

		} else {

			if ($this->departments_model->Edit($department_id, $department_data)) {
				$line = sprintf($this->lang->line('crbs_action_saved'), $department_data['name']);
				$flashmsg = msgbox('info', $line);
			} else {
				$line = sprintf($this->lang->line('crbs_action_dberror'), 'editing');
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
		if ($this->input->post('id')) {
			$this->departments_model->Delete($this->input->post('id'));
			$flashmsg = msgbox('info', $this->lang->line('crbs_action_deleted'));
			$this->session->set_flashdata('saved', $flashmsg);
			redirect('departments');
		}

		$this->data['action'] = 'departments/delete';
		$this->data['id'] = $id;
		$this->data['cancel'] = 'departments';
		$this->data['text'] = 'If you delete this department, you must re-assign any of its members to another department.';

		$row = $this->departments_model->Get($id);
		$this->data['title'] = 'Delete Department ('.html_escape($row->name).')';
		$this->data['showtitle'] = $this->data['title'];
		$this->data['body'] = $this->load->view('partials/deleteconfirm', $this->data, TRUE);

		return $this->render();
	}




}
