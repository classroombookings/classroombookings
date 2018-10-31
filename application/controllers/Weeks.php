<?php
class Weeks extends Controller {





  function Weeks(){
    parent::Controller();
    
		// Load language
  	$this->lang->load('crbs', 'english');
    
		// Get school id
    $this->school_id = $this->session->userdata('school_id');

    $this->output->enable_profiler($this->session->userdata('profiler'));
    
    // Check user is logged in & is admin
    if(!$this->userauth->loggedin()){
    	$this->session->set_flashdata('login', $this->load->view('msgbox/error', $this->lang->line('crbs_auth_mustbeloggedin'), True) );
			redirect('site/home', 'redirect');
		} else {
			$this->loggedin = True;
			if(!$this->userauth->CheckAuthLevel(ADMINISTRATOR)){
				$this->session->set_flashdata('auth', $this->load->view('msgbox/error', $this->lang->line('crbs_auth_mustbeadmin'), True) );
				redirect('controlpanel', 'redirect');
			}
		}
		// Load models
		$this->load->model('crud_model', 'crud');
		$this->load->model('holidays_model', 'M_holidays');
    $this->load->model('weeks_model', 'M_weeks');
    $this->load->helper('iconsel');
    
    // Load calendar
		/* $this->load->library('calendar');    
		$cal_config['start_day']		= 'monday';
		$cal_config['month_type']		= 'long';
		$cal_config['day_type']			= 'short';
		$this->calendar->initialize($cal_config); */
		
		$this->WeeksCount = 0;
  }
  
  
  
  
  
  function index(){
  	$view['weeks'] = $this->M_weeks->Get(NULL, $this->school_id);	//$this->session->userdata('schoolcode'));
  	$view['cal'] = NULL;
  	$view['academicyear'] = $this->M_weeks->GetAcademicYear();
  	
  	if(!$view['academicyear']){
  		$body = $this->load->view('msgbox/warning', 'Please configure your academic year first.', True);
  	} else {
  		$body = '';
  	}
  	
  	$body .= $this->load->view('weeks/weeks_index', $view, True);
  	#$body .= $this->load->view('weeks/weeks_index_academicyear', $view, True);
  	
		$layout['title'] = 'Timetable Week Cycle';
		$layout['showtitle'] = $layout['title'];
		$layout['body'] = $body; 
		$this->load->view('layout', $layout);
  }





	/**
	 * Controller function to handle the Add page
	 */
	function add(){
		#$this->output->cache(1440);
		$content0['academicyear'] = $this->M_weeks->GetAcademicYear();
		if(!$content0['academicyear']){
			redirect('weeks', 'redirect');
		}
		
		$content0['weeks'] = $this->M_weeks->Get();	// $this->session->userdata('schoolcode') );
		$content0['mondays'] = $this->M_weeks->GetMondays();
		
		$content0['weekscount'] = count($content0['weeks']);	//$this->session->userdata('schoolcode')));
		
		
		// Load view
		$layout['title'] = 'Add Week';
		$layout['showtitle'] = $layout['title'];
		
		$cols[0]['content'] = $this->load->view('weeks/weeks_add', $content0, True);
		$cols[0]['width'] = '70%';
		$cols[1]['content'] = '';	//$this->load->view('rooms/rooms_add_side', $body, True);
		$cols[1]['width'] = '30%';
		
		$layout['body'] = $this->load->view('weeks/weeks_add', NULL, True);	#$this->load->view('rooms/rooms_add', $body, True);
		$this->load->view('layout', $layout);
	}
	
	
	
	
	
	/**
	 * Controller function to handle the Edit page
	 */
	function edit($id = NULL){
		// Get ID from URI if function isn't called with parameter (return 0 if URI not present)
		if($id == NULL){ $id = $this->uri->segment(3,0); }
		// If id is 0 (returned if URI is empty) then go to main page - nothing to edit
		if($id == 0){ redirect('weeks', 'redirect'); }
		
		// Layout
		$content0['week'] = $this->M_weeks->Get($id, $this->school_id);
		$content0['weeks'] = $this->M_weeks->Get(NULL, $this->school_id);	// $this->session->userdata('schoolcode') );
		$content0['mondays'] = $this->M_weeks->GetMondays();
		$content0['academicyear'] = $this->M_weeks->GetAcademicYear();
		$content0['weekscount'] = count($content0['weeks']);	//$this->M_weeks->Get());	//$this->session->userdata('schoolcode')));
		
		// Load view
		$layout['title'] = 'Edit Week';
		$layout['showtitle'] = $layout['title'];
		
		$cols[0]['content'] = $this->load->view('weeks/weeks_add', $content0, True);
		$cols[0]['width'] = '70%';
		$cols[1]['content'] = '';
		$cols[1]['width'] = '30%';
		
		$layout['body'] = $this->load->view('weeks/weeks_add', NULL, True);
		$this->load->view('layout', $layout);
	}
	
	
	
	
	
	
	function save(){
	 	// Get ID from form
		$week_id = $this->input->post('week_id');
		
		#print_r($_POST);
		
		// Validation rules
		$vrules['week_id']		= 'required';
		$vrules['name']				= 'required|min_length[1]|max_length[20]';
		$vrules['bgcol']			= 'required|min_length[6]|max_length[7]|callback__is_valid_colour';
		$vrules['fgcol']			= 'required|min_length[6]|max_length[7]|callback__is_valid_colour';
		$this->validation->set_rules($vrules);

		// Pretty it up a bit for error validation message
		$vfields['week_id']		= 'Week ID';
		$vfields['name']			= 'Name';
		$vfields['bgcol']			= 'Background colour';
		$vfields['fgcol']			= 'Foreground colour';
		$vfields['icon']			= 'Icon';
		$this->validation->set_fields($vfields);

		// Set the error delims to a nice styled red hint under the fields
		$this->validation->set_error_delimiters('<p class="hint error"><span>', '</span></p>');
		
    if ($this->validation->run() == FALSE){
    
    	// Validation failed
			if($week_id != "X"){
				$this->edit($week_id);
			} else {
				$this->add();
			}
    
    } else {
    
			// Validation succeeded!
			$data = array();
			$data['name']			= $this->input->post('name');
			$data['bgcol']		=	$this->_makecol($this->input->post('bgcol'));
			$data['fgcol']		= $this->_makecol($this->input->post('fgcol'));
			$data['icon']			= $this->input->post('icon');
			
			// Now see if we are editing or adding
			if($week_id == 'X'){
				// No ID, adding new record
				$week_id = $this->M_weeks->Add($data);
				if($week_id == False){
					$flashmsg = $this->load->view('msgbox/error', sprintf($this->lang->line('crbs_action_dberror'), 'adding'), True);
				} else {
					$flashmsg = $this->load->view('msgbox/info', sprintf($this->lang->line('crbs_action_added'), $data['name']), True);
				}
				#$this->session->set_flashdata('saved_weeks', $this->load->view('msgbox/info', 'Week named <strong>'.$data['name'].'</strong> has been added.', True) );
			} else {
				// We have an ID, updating existing record
				if(!$this->M_weeks->Edit($week_id, $data)){
					$flashmsg = $this->load->view('msgbox/error', sprintf($this->lang->line('crbs_action_dberror'), 'editing'), True);
				} else {
					$flashmsg = $this->load->view('msgbox/info', sprintf($this->lang->line('crbs_action_saved'), $data['name']), True);
				}
				#$this->session->set_flashdata('saved_weeks', $this->load->view('msgbox/info', 'Week named <strong>'.$data['name'].'</strong> has been modified.', True) );
			}
			
			// Update the wees
			if( $this->input->post('dates') ){
				$this->M_weeks->UpdateMondays($week_id, $this->input->post('dates'));
			}
			
			// Go back to index
			$this->session->set_flashdata('saved', $flashmsg);
			redirect('weeks', 'redirect');
    
		}
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
