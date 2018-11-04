<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Weeks extends MY_Controller
{


	public $WeeksCount = 0;


	public function __construct()
	{
		parent::__construct();

		$this->require_logged_in();
		$this->require_auth_level(ADMINISTRATOR);

		$this->load->model('crud_model');
		$this->load->model('holidays_model');
		$this->load->model('weeks_model');
	}




	function index()
	{

		$this->data['weeks'] = $this->weeks_model->Get(NULL);
		$this->data['cal'] = NULL;
		$this->data['academicyear'] = $this->weeks_model->GetAcademicYear();

		if ( ! $this->data['academicyear']) {
			$this->data['body'] = msgbox('warning', "Please configure your academic year first.");
		} else {
			$this->data['body'] = '';
		}

		$this->data['body'] .= $this->load->view('weeks/weeks_index', $this->data, TRUE);

		$this->data['title'] = 'Timetable Week Cycle';
		$this->data['showtitle'] = $this->data['title'];

		return $this->render();
	}





	/**
	 * Controller function to handle the Add page
	 *
	 */
	function add()
	{
		$this->data['academicyear'] = $this->weeks_model->GetAcademicYear();

		if ( ! $this->data['academicyear']) {
			redirect('weeks');
		}

		$this->data['weeks'] = $this->weeks_model->Get();
		$this->data['mondays'] = $this->weeks_model->GetMondays();
		$this->data['weekscount'] = count($this->data['weeks']);

		// Load view
		$this->data['title'] = 'Add Week';
		$this->data['showtitle'] = $this->data['title'];
		$this->data['body'] = $this->load->view('weeks/weeks_add', $this->data, TRUE);

		return $this->render();
	}




	/**
	 * Controller function to handle the Edit page
	 *
	 */
	function edit($id = NULL)
	{
		$this->data['week'] = $this->weeks_model->Get($id);

		if (empty($this->data['week']))
		{
			show_404();
		}

		$this->data['weeks'] = $this->weeks_model->Get(NULL);	// $this->session->userdata('schoolcode') );
		$this->data['mondays'] = $this->weeks_model->GetMondays();
		$this->data['academicyear'] = $this->weeks_model->GetAcademicYear();
		$this->data['weekscount'] = count($this->data['weeks']);	//$this->M_weeks->Get());	//$this->session->userdata('schoolcode')));

		$this->data['title'] = 'Edit Week';
		$this->data['showtitle'] = $this->data['title'];

		$this->data['body'] = $this->load->view('weeks/weeks_add', $this->data, TRUE);

		return $this->render();
	}




	function save()
	{
		// Get ID from form
		$week_id = $this->input->post('week_id');

		$this->load->library('form_validation');

		$this->form_validation->set_rules('week_id', 'ID', 'integer');
		$this->form_validation->set_rules('name', 'Name', 'min_length[1]|max_length[20]');
		$this->form_validation->set_rules('bgcol', 'Background colour', 'min_length[6]|max_length[7]|callback__is_valid_colour');
		$this->form_validation->set_rules('fgcol', 'Foreground colour', 'min_length[6]|max_length[7]|callback__is_valid_colour');

		if ($this->form_validation->run() == FALSE) {
			return (empty($week_id) ? $this->add() : $this->edit($week_id));
		}

		// Validation succeeded!
		$week_data = array(
			'name' => $this->input->post('name'),
			'bgcol' => $this->_makecol($this->input->post('bgcol')),
			'fgcol' => $this->_makecol($this->input->post('fgcol')),
			'icon' => '',
		);

		// Now see if we are editing or adding
		if (empty($week_id)) {
			// No ID, adding new record
			$week_id = $this->weeks_model->Add($week_data);
			if ($week_id) {
				$flashmsg = msgbox('info', sprintf($this->lang->line('crbs_action_added'), $week_data['name']));
			} else {
				$flashmsg = msgbox('error', sprintf($this->lang->line('crbs_action_dberror'), 'adding'));
			}
		} else {
			// We have an ID, updating existing record
			if ($this->weeks_model->Edit($week_id, $week_data)){
				$flashmsg = msgbox('info', sprintf($this->lang->line('crbs_action_saved'), $week_data['name']));
			} else {
				$flashmsg = msgbox('error', sprintf($this->lang->line('crbs_action_dberror'), 'editing'));
			}
		}

		if ($this->input->post('dates')) {
			$this->weeks_model->UpdateMondays($week_id, $this->input->post('dates'));
		}

		$this->session->set_flashdata('saved', $flashmsg);
		redirect('weeks');
	}





	/**
	 * Controller function to delete a week
	 */
	function delete(){
	  // Get ID from URL
		$id = $this->uri->segment(3);

		// Check if a form has been submitted; if not - show it to ask user confirmation
		if( $this->input->post('id') ){
			// Form has been submitted (so the POST value exists)
			// Call model function to delete manufacturer
			$this->M_weeks->delete($this->input->post('id'));
			$this->session->set_flashdata('saved', $this->load->view('msgbox/info', $this->lang->line('crbs_action_deleted'), True) );
			// Redirect to rooms again
			redirect('weeks', 'redirect');
		} else {
			// Initialise page
			$body['action'] = 'weeks/delete';
			$body['id'] = $id;
			$body['cancel'] = 'weeks';
			$body['text'] = 'If you delete this week, <strong>all static bookings</strong> made in this week will be <strong>permanently deleted</strong> as well.';
			// Load page
			$row = $this->M_weeks->Get($id, $this->school_id);
			$layout['title'] = 'Delete Week ('.$row->name.')';
			$layout['showtitle'] = $layout['title'];
			$layout['body'] = $this->load->view('partials/deleteconfirm', $body, TRUE);
			$this->load->view('layout', $layout);
		}
	}





	/**********
	 ACADEMIC YEAR
	 **********/





	 function academicyear(){
	 	$body['academicyear'] = $this->M_weeks->GetAcademicYear();

	 	if(!$body['academicyear']){
	 		$body['academicyear'] = new Stdclass();
	 		$body['academicyear']->date_start = date("Y-m-d");
	 		$body['academicyear']->date_end = date("Y-m-d", strtotime("+1 Year", strtotime(date("Y-m-d"))));
	 	}

	 	$layout['title'] = 'Academic Year';
	 	$layout['showtitle'] = $layout['title'];
	 	$layout['body'] = $this->load->view('weeks/weeks_academicyear', $body, True);
	 	$this->load->view('layout', $layout);
	 }





	 function saveacademicyear(){
		#print_r($_POST);

		// Validation rules
	 	$vrules['date_start']			= 'required|min_length[8]|max_length[10]';
	 	$vrules['date_end']				= 'required|min_length[8]|max_length[10]';
	 	$this->validation->set_rules($vrules);

		// Pretty it up a bit for error validation message
	 	$vfields['date_start']		= 'Start date';
	 	$vfields['date_end']			= 'End Date';
	 	$this->validation->set_fields($vfields);

		// Set the error delims to a nice styled red hint under the fields
	 	$this->validation->set_error_delimiters('<p class="hint error"><span>', '</span></p>');

	 	if ($this->validation->run() == FALSE){

	 		return $this->academicyear();

	 	} else {

			// Validation succeeded!
	 		$date_format = "Y-m-d";

	 		$start_date = explode('/', $this->input->post('date_start'));
	 		$end_date = explode('/', $this->input->post('date_end'));

	 		$data = array();
	 		$data['date_start']		=	sprintf("%s-%s-%s", $start_date[2], $start_date[1], $start_date[0]);
	 		$data['date_end']			= sprintf("%s-%s-%s", $end_date[2], $end_date[1], $end_date[0]);

	 		$this->M_weeks->SaveAcademicYear($data);
	 		$this->session->set_flashdata('saved', $this->load->view('msgbox/info', 'The Academic Year dates have been updated.', True) );

	 	}

			// Go back to index
	 	redirect('weeks/academicyear', 'redirect');

	 }





	 function _is_valid_colour($colour){
	 	$hex = array('0','1','2','3','4','5','6','7','8','9','A','B','C','D','E','F');
		#print_r($hex);
		// Remove the hash
	 	$colour = strtoupper(str_replace('#', '', $colour));
		// Make sure we do have 6 digits
	 	if(strlen($colour) == 6){
	 		$ret = true;
	 		for($i=0;$i<strlen($colour);$i++){
				#echo $colour{$i};
	 			if(!in_array($colour{$i}, $hex)){
	 				$this->validation->set_message('_is_valid_colour', $this->lang->line('colour_invalid'));
	 				return false;
	 				$ret = false;
	 			}
	 		}
	 	} else {
	 		$this->validation->set_message('_is_valid_colour', $this->lang->line('colour_invalid'));
	 		$ret = false;
	 	}
	 	return $ret;
	 }





	 function _makecol($colour){
	 	return strtoupper(str_replace('#', '', $colour));
	 }





	}
	?>
