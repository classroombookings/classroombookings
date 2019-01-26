<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Periods extends MY_Controller
{


	public function __construct()
	{
		parent::__construct();

		$this->require_logged_in();
		$this->require_auth_level(ADMINISTRATOR);

		$this->load->model('crud_model');
		$this->load->model('periods_model');
	}




	function index()
	{
		// Get data from database
		$this->data['periods'] = $this->periods_model->Get();
		$this->data['days_list'] = $this->periods_model->days;

		$this->data['title'] = 'The School Day';
		$this->data['showtitle'] = $this->data['title'];	// . ' ('.$section.')';
		$this->data['body'] = $this->load->view('periods/periods_index', $this->data, TRUE);

		return $this->render();
	}




	/**
	 * Controller function to handle the Add page
	 *
	 */
	function add()
	{
		// Load view

		$this->data['days_list'] = $this->periods_model->days;

		$columns = array(
			'c1' => array(
				'content' => $this->load->view('periods/periods_add', $this->data, TRUE),
				'width' => '70%',
			),
			'c2' => array(
				'content' => $this->load->view('periods/periods_add_side', $this->data, TRUE),
				'width' => '30%',
			),
		);

		$this->data['title'] = 'Add Period';
		$this->data['showtitle'] = $this->data['title'];
		$this->data['body'] = $this->load->view('columns', $columns, TRUE);

		return $this->render();
	}




	/**
	 * Controller function to handle an edit
	 */
	function edit($id = NULL)
	{

		$this->data['period'] = $this->periods_model->get($id);

		if (empty($this->data['period']))
		{
			show_404();
		}

		$this->data['days_list'] = $this->periods_model->days;

		$columns = array(
			'c1' => array(
				'content' => $this->load->view('periods/periods_add', $this->data, TRUE),
				'width' => '70%',
			),
			'c2' => array(
				'content' => $this->load->view('periods/periods_add_side', $this->data, TRUE),
				'width' => '30%',
			),
		);

		$this->data['title'] = 'Edit Period';
		$this->data['showtitle'] = $this->data['title'];
		$this->data['body'] = $this->load->view('columns', $columns, TRUE);
		return $this->render();
	}




	function save()
	{
		// Get ID from form
		$period_id = $this->input->post('period_id');

		$this->load->library('form_validation');

		$this->form_validation->set_rules('period_id', 'ID', 'integer');
		$this->form_validation->set_rules('name', 'Name', 'required|min_length[1]|max_length[30]');
		$this->form_validation->set_rules('day_1', 'Monday', 'required|integer');
		$this->form_validation->set_rules('day_2', 'Tuesday', 'required|integer');
		$this->form_validation->set_rules('day_3', 'Wednesday', 'required|integer');
		$this->form_validation->set_rules('day_4', 'Thursday', 'required|integer');
		$this->form_validation->set_rules('day_5', 'Friday', 'required|integer');
		$this->form_validation->set_rules('day_6', 'Saturday', 'required|integer');
		$this->form_validation->set_rules('day_7', 'Sunday', 'required|integer');
		$this->form_validation->set_rules('time_start', 'Start time', 'required|min_length[4]|max_length[5]|callback__is_valid_time');
		$this->form_validation->set_rules('time_end', 'End time', 'required|min_length[4]|max_length[5]|callback__is_valid_time|callback__is_after[time_start]');
		$this->form_validation->set_rules('bookable', 'Bookable', 'required|integer');

		if ($this->form_validation->run() == FALSE) {
			return (empty($period_id) ? $this->add() : $this->edit($period_id));
		}

		$period_data = array(
			'name' => $this->input->post('name'),
			'time_start' => $this->_fix_time($this->input->post('time_start')),
			'time_end' => $this->_fix_time($this->input->post('time_end')),
			'day_1' => $this->input->post('day_1'),
			'day_2' => $this->input->post('day_2'),
			'day_3' => $this->input->post('day_3'),
			'day_4' => $this->input->post('day_4'),
			'day_5' => $this->input->post('day_5'),
			'day_6' => $this->input->post('day_6'),
			'day_7' => $this->input->post('day_7'),
			'bookable' => $this->input->post('bookable'),
		);

		// Now see if we are editing or adding
		if (empty($period_id)) {
			// No ID, adding new record
			$period_id = $this->periods_model->Add($period_data);
			$this->session->set_flashdata('saved', msgbox('info', "{$period_data['name']} has been added."));
		} else {
			// We have an ID, updating existing record
			$this->periods_model->Edit($period_id, $period_data);
			$this->session->set_flashdata('saved', msgbox('info', "{$period_data['name']} has been modified."));
		}

		redirect('periods');
	}




	/**
	 * Controller function to delete a room
	 */
	function delete($id = NULL)
	{
		// Check if a form has been submitted; if not - show it to ask user confirmation
		if ($this->input->post('id')) {
			// Form has been submitted (so the POST value exists)
			// Call model function to delete manufacturer
			$this->periods_model->Delete($this->input->post('id'));
			$this->session->set_flashdata('saved', msgbox('info', "The period has been deleted."));
			return redirect('periods');
		}

		if (empty($id)){
			show_error("No period ID provided.");
		}

		// Initialise page
		$this->data['action'] = 'periods/delete';
		$this->data['id'] = $id;
		$this->data['cancel'] = 'periods';
		$this->data['text'] = 'If you delete this period, any bookings for this period in <strong>all</strong> rooms will be <strong>permenantly deleted</strong>.';
		$row = $this->periods_model->Get($id);
		$this->data['title'] = 'Delete Period (' . html_escape($row->name) . ')';
		$this->data['showtitle'] = $this->data['title'];
		$this->data['body'] = $this->load->view('partials/deleteconfirm', $this->data, TRUE);
		return $this->render();
	}




	/**
	 * VALIDATION _is_valid_time
	 *
	 * Check to see if time entered is a valid time between 00:00 and 23:59
	 *
	 * @param		string		$time		Time
	 * @return	bool on success
	 *
	 */
	function _is_valid_time($time)
	{
		$times = array();
		$times['am'] = strtotime('00:00');
		$times['pm'] = strtotime('23:59');
		$times['data'] = strtotime($time);

		if ( ($times['data'] >= $times['am'] && $times['data'] <= $times['pm']) || !isset($times['data'])) {
			$ret = true;
		} else {
			$this->form_validation->set_message('_is_valid_time', 'You entered an invalid time. It must be between 00:00 and 23:59.');
			$ret = false;
		}

		return $ret;
	}




	/**
	 * VALIDATION	_is_after
	 *
	 * Check that the time entered (time_end) is greater than or equal to the start time
	 *
	 * @param		string		$time		Time
	 * @return		bool on success
	 *
	 */
	function _is_after($time){
		$start = strtotime( $this->_fix_time( $this->input->post( 'time_start' ) ) );
		$end = strtotime( $this->_fix_time($time) );
		if( $end >= $start ){
			$ret = true;
		} else {
			$this->form_validation->set_message('_is_after', 'The end time must be equal to or greater than the start time of '.$this->_fix_time( $this->input->post( 'time_start' ) ).'.' );
			$ret = false;
		}
		return $ret;
	}





	/**
	 * Fix the time format
	 *
	 * Formats the time properly (HH:MM) for the database for any time given
	 *
	 * @param		string		$time		Time
	 * @return		string		Formatted time
	 */
	function _fix_time($time)
	{
		return strftime('%H:%M', strtotime($time));
	}




}
