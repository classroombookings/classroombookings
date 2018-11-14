<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Holidays extends MY_Controller
{


	public function __construct()
	{
		parent::__construct();

		$this->require_logged_in();
		$this->require_auth_level(ADMINISTRATOR);

		$this->load->model('crud_model');
		$this->load->model('weeks_model');
		$this->load->model('holidays_model');
	}




	function index()
	{
		$this->data['holidays'] = $this->holidays_model->Get();

		$this->data['title'] = 'School Holidays';
		$this->data['showtitle'] = $this->data['title'];
		$this->data['body'] = $this->load->view('holidays/holidays_index', $this->data, TRUE);
		return $this->render();
	}




	/**
	 * Handle the Add page
	 *
	 */
	function add()
	{
		$this->data['title'] = 'Add Holiday';
		$this->data['showtitle'] = $this->data['title'];

		$columns = array(
			'c1' => array(
				'content' => $this->load->view('holidays/holidays_add', NULL, TRUE),
				'width' => '70%',
			),
			'c2' => array(
				'content' => $this->load->view('holidays/holidays_add_side', NULL, TRUE),
				'width' => '30%',
			),
		);

		$this->data['body'] = $this->load->view('columns', $columns, TRUE);

		return $this->render();
	}




	/**
	 * Controller function to handle the Edit page
	 *
	 */
	function edit($id = NULL)
	{
		$this->data['holiday'] = $this->holidays_model->Get($id);

		if (empty($this->data['holiday'])) {
			show_404();
		}

		// Load view
		$this->data['title'] = 'Edit Holiday';
		$this->data['showtitle'] = $this->data['title'];

		$columns = array(
			'c1' => array(
				'content' => $this->load->view('holidays/holidays_add', $this->data, TRUE),
				'width' => '70%',
			),
			'c2' => array(
				'content' => $this->load->view('holidays/holidays_add_side', $this->data, TRUE),
				'width' => '30%',
			),
		);

		$this->data['body'] = $this->load->view('columns', $columns, TRUE);

		return $this->render();
	}




	function save()
	{
		// Get ID from form
		$holiday_id = $this->input->post('holiday_id');

		$this->load->library('form_validation');

		$this->form_validation->set_rules('holiday_id', 'ID', 'integer');
		$this->form_validation->set_rules('name', 'Name', 'required|min_length[1]|max_length[30]');
		$this->form_validation->set_rules('date_start', 'Start date', 'required|min_length[8]|max_length[10]');
		$this->form_validation->set_rules('date_end', 'End date', 'required|min_length[8]|max_length[10]');;

		if ($this->form_validation->run() == FALSE) {
			return (empty($holiday_id) ? $this->add() : $this->edit($holiday_id));
		}

		$date_format = "Y-m-d";

		$start_date = explode('/', $this->input->post('date_start'));
		$end_date = explode('/', $this->input->post('date_end'));

		$holiday_data = array(
			'name'=> $this->input->post('name'),
			'date_start'=>	sprintf("%s-%s-%s", $start_date[2], $start_date[1], $start_date[0]),
			'date_end'=> sprintf("%s-%s-%s", $end_date[2], $end_date[1], $end_date[0]),
		);

		if (empty($holiday_id)) {

			$holiday_id = $this->holidays_model->Add($holiday_data);

			if ($holiday_id) {
				$line = sprintf($this->lang->line('crbs_action_added'), $holiday_data['name']);
				$flashmsg = msgbox('info', $line);
			} else {
				$line = sprintf($this->lang->line('crbs_action_dberror'), 'adding');
				$flashmsg = msgbox('error', $line);
			}

		} else {

			if ($this->holidays_model->Edit($holiday_id, $holiday_data)) {
				$line = sprintf($this->lang->line('crbs_action_saved'), $holiday_data['name']);
				$flashmsg = msgbox('info', $line);
			} else {
				$line = sprintf($this->lang->line('crbs_action_dberror'), 'editing');
				$flashmsg = msgbox('error', $line);
			}

		}

		$this->session->set_flashdata('saved', $flashmsg);
		redirect('holidays');
	}





	/**
	 * Delete a holiday
	 *
	 */
	function delete($id = NULL)
	{
		// Check if a form has been submitted; if not - show it to ask user confirmation
		if ($this->input->post('id')) {
			$this->holidays_model->Delete($this->input->post('id'));
			$flashmsg = msgbox('info', $this->lang->line('crbs_action_deleted'));
			$this->session->set_flashdata('saved', $flashmsg);
			redirect('holidays');
		}

		// Initialise page
		$this->data['action'] = 'holidays/delete';
		$this->data['id'] = $id;
		$this->data['cancel'] = 'holidays';

		$row = $this->holidays_model->Get($id);
		$this->data['title'] = 'Delete Holiday (' . html_escape($row->name) . ')';
		$this->data['showtitle'] = $this->data['title'];
		$this->data['body'] = $this->load->view('partials/deleteconfirm', $this->data, TRUE);
		return $this->render();
	}




}
