<?php
class Holidays extends Controller {





  function Holidays(){
    parent::Controller();
    
		// Load language
  	$this->lang->load('crbs', 'english');
    
		// Get school id
    $this->school_id = $this->session->userdata('school_id');

    $this->output->enable_profiler($this->session->userdata('profiler'));
    
		// Check user is logged in & is admin
    if(!$this->userauth->loggedin()){
    	$this->session->set_flashdata('login', $this->load->view('msgbox/error', $this->lang->line('crbs_auth_mustbeloggedin'), True) );
			redirect('site/home', 'location');
		} else {
			$this->loggedin = True;
			if(!$this->userauth->CheckAuthLevel(ADMINISTRATOR)){
				$this->session->set_flashdata('auth', $this->load->view('msgbox/error', $this->lang->line('crbs_auth_mustbeadmin'), True) );
				redirect('controlpanel', 'location');
			}
		}
		// Load Date helper
		#$this->load->helper('date');
		// Load required models
		$this->load->model('crud_model', 'crud');
    $this->load->model('weeks_model', 'M_weeks');
    $this->load->model('holidays_model', 'M_holidays');
		// Load calendar, we may need it
		/* $this->load->library('calendar');    
		$cal_config['start_day']		= 'monday';
		$cal_config['month_type']		= 'long';
		$cal_config['day_type']			= 'short';
		$this->calendar->initialize($cal_config); */
  }





	function index(){
  	$body['holidays'] = $this->M_holidays->Get(NULL, $this->school_id);	//$this->session->userdata('schoolcode'));
  	
		$layout['title'] = 'School Holidays';
		$layout['showtitle'] = $layout['title'];
		$layout['body'] = $this->load->view('holidays/holidays_index', $body, True);
		$this->load->view('layout', $layout);
	}





	/**
	 * Controller function to handle the Add page
	 */
	function add(){
		// Load view
		$layout['title'] = 'Add Holiday';
		$layout['showtitle'] = $layout['title'];
		
		$cols[0]['content'] = $this->load->view('holidays/holidays_add', NULL, True);
		$cols[0]['width'] = '70%';
		$cols[1]['content'] = $this->load->view('holidays/holidays_add_side', NULL, True);
		$cols[1]['width'] = '30%';
		
		$layout['body'] = $this->load->view('columns', $cols, True);	#$this->load->view('rooms/rooms_add', $body, True);
		$this->load->view('layout', $layout);
	}
	
	
	
	
	
	/**
	 * Controller function to handle the Edit page
	 */
	function edit($id = NULL){
		if($id == NULL){ $id = $this->uri->segment(3); }
		$content0['holiday'] = $this->M_holidays->Get($id, $this->school_id);
		
		// Load view
		$layout['title'] = 'Edit Holiday';
		$layout['showtitle'] = $layout['title'];
		
		$cols[0]['content'] = $this->load->view('holidays/holidays_add', $content0, True);
		$cols[0]['width'] = '70%';
		$cols[1]['content'] = $this->load->view('holidays/holidays_add_side', NULL, True);
		$cols[1]['width'] = '30%';
		
		$layout['body'] = $this->load->view('columns', $cols, True);	#$this->load->view('rooms/rooms_add', $body, True);
		$this->load->view('layout', $layout);
	}
	
	
	
	
	
	function save(){
	 	// Get ID from form
		$holiday_id = $this->input->post('holiday_id');
		
		#print_r($_POST);
		
		// Validation rules
		$vrules['holiday_id']		= 'required';
		$vrules['name']					= 'required|min_length[1]|max_length[30]';
		$vrules['date_start']		= 'required|min_length[8]|max_length[10]';
		$vrules['date_end']			= 'required|min_length[8]|max_length[10]';
		$this->validation->set_rules($vrules);

		// Pretty it up a bit for error validation message
		$vfields['holiday_id']		= 'Holiday ID';
		$vfields['name']					= 'Name';
		$vfields['date_start']		= 'Start date';
		$vfields['date_end']			= 'End date';
		$this->validation->set_fields($vfields);

		// Set the error delims to a nice styled red hint under the fields
		$this->validation->set_error_delimiters('<p class="hint error"><span>', '</span></p>');
		
    if ($this->validation->run() == FALSE){
    
    	// Validation failed
			if($holiday_id != "X"){
				return $this->edit($holiday_id);
			} else {
				return $this->add();
			}
    
    } else {
    
			// Validation succeeded!
			$date_format = "Y-m-d";
			
			$start_date = explode('/', $this->input->post('date_start'));
			$end_date = explode('/', $this->input->post('date_end'));
			
			$data = array();
			$data['name']					= $this->input->post('name');
			$data['date_start']		=	sprintf("%s-%s-%s", $start_date[2], $start_date[1], $start_date[0]);
			$data['date_end']			= sprintf("%s-%s-%s", $end_date[2], $end_date[1], $end_date[0]);
			
			#print_r($data);
			
			#echo $data['date_start'] . " : " . $this->input->post('date_start');
			
			// Now see if we are editing or adding
			if($holiday_id == 'X'){
				// No ID, adding new record
				$holiday_id = $this->M_holidays->Add($data);
				$this->session->set_flashdata('saved', $this->load->view('msgbox/info', 'Holiday named <strong>'.$data['name'].'</strong> has been added.', True) );
			} else {
				// We have an ID, updating existing record
				$this->M_holidays->Edit($holiday_id, $data);
				$this->session->set_flashdata('saved', $this->load->view('msgbox/info', 'Holiday named <strong>'.$data['name'].'</strong> has been modified.', True) );
			}
			
			// Go back to index
			redirect('holidays', 'redirect');
    
		}
	}
	
	
	
	
	
	/**
	 * Controller function to delete a holiday
	 */
	function delete(){
	  // Get ID from URL
		$holiday_id = $this->uri->segment(3);
		
		// Check if a form has been submitted; if not - show it to ask user confirmation
		if( $this->input->post('id') ){
			// Form has been submitted (so the POST value exists)
			// Call model function to delete manufacturer
			$this->crud->Delete('holidays', 'holiday_id', $this->input->post('id'));
			$this->session->set_flashdata('saved', $this->load->view('msgbox/info', 'The holiday has been deleted.', True) );
			// Redirect
			redirect('holidays', 'redirect');
		} else {
			// Initialise page
			$body['action'] = 'holidays/delete';
			$body['id'] = $holiday_id;
			$body['cancel'] = 'holidays';
			#$body['text'] = 'If you delete this department, you must re-assign any of its members to another department.';
			// Load page
			$row = $this->crud->Get('holidays', 'holiday_id', $holiday_id);
			$layout['title'] = 'Delete Holiday ('.$row->name.')';
			$layout['showtitle'] = $layout['title'];
			$layout['body'] = $this->load->view('partials/deleteconfirm', $body, True);
			$this->load->view('layout', $layout);
		}
	}






	/**
	 * VALIDATION	_dates_valid
	 * 
	 * Check that the time entered (time_end) is greater than or equal to the start time
	 * 
	 * @param		string		$time		Time
	 * @return		bool on success	 
	 *
	 */	 	 	 	 	 	 
	function _dates_valid($end){
		$d_start = strtotime($this->input->post('date_start') . "00:00:00");
		$d_end = strtotime($end);
		echo "Start: $d_start; End: $d_end";
		if( $d_end >= $d_start ){
			$ret = true;
		} else {
			$this->validation->set_message('_dates_valid', 'The end date must be the same as or after the start date.' );
			$ret = false;
		}
		return $ret;
	}





}
?>
