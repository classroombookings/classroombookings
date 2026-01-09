<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Fields extends MY_Controller
{

	public $js = [
		'sortable',
	];

	public function __construct()
	{
		parent::__construct();

		$this->require_logged_in();
		$this->require_permission(Permission::SETUP_ROOMS);

		$this->load->model([
			'room_groups_model',
			'rooms_model',
		]);

		$this->data['showtitle'] = lang('custom_field.custom_fields');

		$this->load->library('table');
	}


	function index()
	{
		$this->data['options_list'] = $this->rooms_model->options;
		$this->data['fields'] = $this->rooms_model->GetFields();
		$this->data['title'] = lang('custom_field.custom_fields');

		$this->data['active'] = 'setup/rooms/fields';
		$icons = $this->load->view('setup/rooms/_icons_primary', $this->data, true);
		$body = $this->load->view('setup/rooms/fields/index', $this->data, TRUE);
		$this->data['body'] = $icons . $body;

		return $this->render();
	}





	function add()
	{
		$this->data['options_list'] = $this->rooms_model->options;

		$this->data['title'] = $this->data['showtitle'] = lang('custom_field.add.title');

		$columns = array(
			'c1' => array(
				'content'=> $this->load->view('setup/rooms/fields/add', $this->data, TRUE),
				'width' => '70%',
			),
			'c2' => array(
				'content' => '',
				'width' => '30%',
			),
		);

		$this->data['active'] = 'setup/rooms/fields';
		$icons = $this->load->view('setup/rooms/_icons_primary', $this->data, true);
		$columns = $this->load->view('columns', $columns, TRUE);

		$this->data['body'] = $icons . $columns;

		return $this->render();
	}




	/**
	 * Controller function to handle an edit
	 *
	 */
	function edit($id)
	{
		$this->data['field'] = $this->rooms_model->GetFields($id);
		$this->data['options_list'] = $this->rooms_model->options;

		$this->data['title'] = 'Edit Field';

		$columns = array(
			'c1' => array(
				'content'=> $this->load->view('setup/rooms/fields/add', $this->data, TRUE),
				'width' => '70%',
			),
			'c2' => array(
				'content' => '',
				'width' => '30%',
			),
		);

		$this->data['active'] = 'setup/rooms/fields';
		$icons = $this->load->view('setup/rooms/_icons_primary', $this->data, true);
		$columns = $this->load->view('columns', $columns, TRUE);

		$this->data['body'] = $icons . $columns;

		return $this->render();
	}




	function save()
	{
		// Get ID from form
		$field_id = $this->input->post('field_id');

		$this->load->library('form_validation');

		$this->form_validation->set_rules('field_id', 'ID', 'integer');
		$this->form_validation->set_rules('name', 'lang:custom_field.field.name', 'required|min_length[1]|max_length[64]');
		// $this->form_validation->set_rules('options', 'lang:custom_field.field.options', '');
		$this->form_validation->set_rules('type', 'lang:custom_field.field.type', 'required');

		if ($this->form_validation->run() == FALSE){
			return (empty($field_id) ? $this->add() : $this->edit($field_id));
		}

		// Validation succeeded!
		$field_data = array(
			'name' => $this->input->post('name'),
			'type' => $this->input->post('type'),
			'options' => $this->input->post('options'),
		);

		if (empty($field_id)) {
			$field_id = $this->rooms_model->field_add($field_data);
			$msg = sprintf(lang('custom_field.create.success'), $field_data['name']);
			$flashmsg = msgbox('info', $msg);
		} else {
			$this->rooms_model->field_edit($field_id, $field_data);
			$msg = sprintf(lang('custom_field.update.success'), $field_data['name']);
			$flashmsg = msgbox('info', $msg);
		}

		$this->session->set_flashdata('saved', $flashmsg, TRUE);
		redirect('setup/rooms/fields');
	}





	/**
	 * Delete a field
	 *
	 */
	function delete($id)
	{
		$row = $this->rooms_model->GetFields($id);

		if ($this->input->post('id') == $id) {
			$this->rooms_model->field_delete($this->input->post('id'));
			$flashmsg = msgbox('info', sprintf(lang('custom_field.delete.success'), $row->name));
			$this->session->set_flashdata('saved', $flashmsg, TRUE);
			redirect('setup/rooms/fields');
			return;
		}

		$this->data['action'] = current_url();
		$this->data['id'] = $id;
		$this->data['cancel'] = 'setup/rooms/fields';
		// $this->data['text'] = lang('week.delete.warning');

		$this->data['title'] = $this->data['showtitle'] = sprintf(lang('custom_field.delete.title'), html_escape($row->name));
		$this->data['body'] = $this->load->view('partials/deleteconfirm', $this->data, TRUE);

		$this->data['active'] = 'setup/rooms/fields';
		$icons = $this->load->view('setup/rooms/_icons_primary', $this->data, true);
		$body = $this->load->view('partials/deleteconfirm', $this->data, TRUE);

		$this->data['body'] = $icons . "<br>" . $body;

		return $this->render();
	}





}
