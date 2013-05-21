<?php
class Periods extends Controller {


	/**
	 *	
	 * School Day controller
	 *
	 */	  	 	 	
  
  
  
  
  
  function Periods(){
		parent::Controller();
		
		// Load language
  	$this->lang->load('crbs', 'english');
    
		// Get school id
    $this->school_id = $this->session->userdata('school_id');

    $this->output->enable_profiler($this->session->userdata('profiler'));
    
    // Check user is logged in & is admin
    if( !$this->userauth->loggedin() ){
    	$this->session->set_flashdata('login', $this->load->view('msgbox/error', $this->lang->line('crbs_auth_mustbeloggedin'), True) );
			redirect('site/home', 'location');
		} else {
			$this->loggedin = True;
			if(!$this->userauth->CheckAuthLevel(ADMINISTRATOR)){
				$this->session->set_flashdata('auth', $this->load->view('msgbox/error', $this->lang->line('crbs_auth_mustbeadmin'), True) );
				redirect('controlpanel', 'location');
			}
		}
		// Load models etc
		$this->load->script('bitmask');
		$this->load->model('crud_model', 'crud');
		$this->load->model('periods_model', 'M_periods');
    $this->load->model('school_model', 'M_school');

	}
	
	
	
	
	
	function index(){
		// Get data from database
		$body['periods'] = $this->M_periods->Get();	//$this->session->userdata('schoolcode'));
		$body['days_list'] = $this->M_periods->days;
		$body['days_bitmask'] = $this->M_periods->days_bitmask;
		$layout['title'] = 'The School Day';
		$layout['showtitle'] = $layout['title'];	// . ' ('.$section.')';
		$layout['body'] = $this->load->view('periods/periods_index', $body, True);
		$this->load->view('layout', $layout );
	}
	
	
	
	
	
	/**
	 * Controller function to handle the Add page
	 */
	function add(){
		// Load view

		$content0['days_list'] = $this->M_periods->days;
		$content0['days_bitmask'] = $this->M_periods->days_bitmask;
		
		$cols[0]['content'] = $this->load->view('periods/periods_add', $content0, True);
		$cols[0]['width'] = '70%';
		$cols[1]['content'] = $this->load->view('periods/periods_add_side', NULL, True);
		$cols[1]['width'] = '30%';
		
		$layout['title'] = 'Add Period';
		$layout['showtitle'] = $layout['title'];
		$layout['body'] = $this->load->view('columns', $cols, True);	#$this->load->view('rooms/rooms_add', $body, True);
		$this->load->view('layout', $layout);
	}
	
	
	
	
	
	/**
	 * Controller function to handle an edit
	 */
	function edit($id = NULL){
		if($id == NULL){ $id = $this->uri->segment(3); }
		
		$content0['period'] = $this->M_periods->Get($id);
		
		// Load view
		
		$content0['days_list'] = $this->M_periods->days;
		$content0['days_bitmask'] = $this->M_periods->days_bitmask;
				
		$cols[0]['content'] = $this->load->view('periods/periods_add', $content0, True);
		$cols[0]['width'] = '70%';
		$cols[1]['content'] = $this->load->view('periods/periods_add_side', NULL, True);
		$cols[1]['width'] = '30%';

		$layout['title'] = 'Edit Period';
		$layout['showtitle'] = $layout['title'];
		$layout['body'] = $this->load->view('columns', $cols, True);	#$this->load->view('rooms/rooms_add', $body, True);
		$this->load->view( 'layout', $layout);
	}
	
	
	
	
	
	function save(){
	 	// Get ID from form
		$period_id = $this->input->post('period_id');
		
		#print_r($_POST);
		
		// Load validation (dont need this any more as it's autoloaded)
		#$this->load->library('validation');
		
		// Validation rules
		$vrules['period_id']		= 'required';
		$vrules['name']					= 'required|min_length[1]|max_length[30]';
		$vrules['time_start']		= 'required|min_length[4]|max_length[5]|callback__is_valid_time';
		$vrules['time_end']			= 'required|min_length[4]|max_length[5]|callback__is_valid_time|callback__is_after[time_start]';
		#$vrules['bookable']			= 'max_length[255]';
		$this->validation->set_rules($vrules);

		// Pretty it up a bit for error validation message
		$vfields['period_id']			= 'Period ID';
		$vfields['name']					= 'Name';
		$vfields['time_start']		= 'Start time';
		$vfields['time_end']			= 'End time';
		$vfields['bookable']			= 'Can be booked';
		$this->validation->set_fields($vfields);

		// Set the error delims to a nice styled red hint under the fields
		$this->validation->set_error_delimiters('<p class="hint error"><span>', '</span></p>');
		
    if ($this->validation->run() == FALSE){
    
    	// Validation failed
			if($period_id != "X"){
				return $this->edit($period_id);
			} else {
				return $this->add();
			}
    
    } else {
    
			// Validation succeeded!
			
			// Compile bitmask of days
			foreach( $this->input->post('days') as $day ){
				$this->M_periods->days_bitmask->set_bit($day);
			}
			
			// Array of information to send to the database
			$data = array();
			$data['name']					= $this->input->post('name');
			$data['time_start']		=	$this->_fix_time($this->input->post('time_start'));
			$data['time_end']			= $this->_fix_time($this->input->post('time_end'));
			$data['days']					= $this->M_periods->days_bitmask->forward_mask;
			$data['bookable']			= ($this->input->post('bookable')) ? 1 : 0;
			
			// Now see if we are editing or adding
			if($period_id == 'X'){
				// No ID, adding new record
				$period_id = $this->M_periods->Add($data);
				$this->session->set_flashdata('saved', $this->load->view('msgbox/info', $data['name'] . ' has been added.', True) );
			} else {
				// We have an ID, updating existing record
				$this->M_periods->Edit($period_id, $data);
				$this->session->set_flashdata('saved', $this->load->view('msgbox/info', $data['name'] . ' has been modified.', True) );
			}
			
			// Go back to index
			redirect('periods', 'redirect');
    
		}
	}
	
	
	
	
	
	/**
	 * Controller function to delete a room
	 */
	function delete(){
	  // Get ID from URL
		$id = $this->uri->segment(3);
		
		// Check if a form has been submitted; if not - show it to ask user confirmation
		if( $this->input->post('id') ){
			// Form has been submitted (so the POST value exists)
			// Call model function to delete manufacturer
			$this->M_periods->Delete($this->input->post('id'));
			$this->session->set_flashdata('saved', $this->load->view('msgbox/info', 'The period has been deleted.', True) );
			// Redirect to rooms again
			redirect('periods', 'redirect');
		} else {
			// Initialise page
			$body['action'] = 'periods/delete';
			$body['id'] = $id;
			$body['cancel'] = 'periods';
			$body['text'] = 'If you delete this period, any bookings for this period in <strong>all</strong> rooms will be <strong>permenantly deleted</strong>.';
			// Load page
			$row = $this->M_periods->Get($this->session->userdata('schoolcode'), $id);
			$layout['title'] = 'Delete Period ('.$row->name.')';
			$layout['showtitle'] = $layout['title'];
			$layout['body'] = $this->load->view('partials/deleteconfirm', $body, TRUE);
			$this->load->view('layout', $layout);
		}
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
	function _is_valid_time($time){
		$times['am'] = strtotime('00:00');
		$times['pm'] = strtotime('23:59');
		$times['data'] = strtotime($time);
		if( ($times['data'] >= $times['am'] && $times['data'] <= $times['pm']) || !isset($times['data']) ){
			$ret = true;
		} else {
			$this->validation->set_message('_is_valid_time', 'You entered an invalid time. It must be between 00:00 and 23:59.');
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
			$this->validation->set_message('_is_after', 'The end time must be equal to or greater than the start time of '.$this->_fix_time( $this->input->post( 'time_start' ) ).'.' );
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
	function _fix_time($time){
		return strftime('%H:%M', strtotime($time));
	}





}
?>
