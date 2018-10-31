<?php
class Departments extends Controller {





  function Departments(){
    parent::Controller();
    
		// Load language
  	$this->lang->load('crbs', 'english');
    
		// Get school id
    $this->school_id = $this->session->userdata('school_id');

    $this->output->enable_profiler($this->session->userdata('profiler'));
    
    // Check user is logged in & is admin
    if(!$this->userauth->loggedin()){
    	$this->session->set_flashdata('login', $this->load->view('msgbox/error', $this->lang->line('crbs_auth_mustbeloggedin'), True));
			redirect('site/home', 'location');
		} else {
			$this->loggedin = True;
			if(!$this->userauth->CheckAuthLevel(ADMINISTRATOR)){
				$this->session->set_flashdata('auth', $this->load->view('msgbox/error', $this->lang->line('crbs_auth_mustbeadmin'), True));
				redirect('controlpanel', 'location');
			}
		}
		
		// Load models etc
		$this->load->library('pagination');
		$this->load->model('crud_model', 'crud');
    $this->load->model('school_model', 'M_school');
    #$this->load->model('departments_model', 'M_departments');
    // Load the icon selector helper
    $this->load->helper('iconsel');
    #$this->load->scaffolding('rooms');
  }
  
  
  
  
  function index($start_at = NULL){
  	log_message('debug', 'Departments/index');
  	if($start_at == NULL){ $start_at = $this->uri->segment(3); }
		// Init pagination
		$pages['base_url'] = site_url('departments/index');
		$pages['total_rows'] = $this->crud->Count('departments');
		$pages['per_page'] = '10';
		$pages['full_tag_open'] = '<p style="text-align:center">';
		$pages['full_tag_close'] = '</p>';
		$pages['cur_tag_open'] = ' <b>';
		$pages['cur_tag_close'] = '</b>';
		$pages['first_link'] = '<img src="webroot/images/ui/resultset_first.png" width="16" height"16" alt="First" title="First" align="top" />';
		$pages['last_link'] = '<img src="webroot/images/ui/resultset_last.png" width="16" height"16" alt="Last" title="Last" align="top" />';
		$pages['next_link'] = '<img src="webroot/images/ui/resultset_next.png" width="16" height"16" alt="Next" title="Next" align="top" />';
		$pages['prev_link'] = '<img src="webroot/images/ui/resultset_previous.png" width="16" height"16" alt="Previous" title="Previous" align="top" />';
		$this->pagination->initialize($pages);
		$body['pagelinks'] = $this->pagination->create_links();
		// Get list of rooms from database
		$body['departments'] = $this->crud->Get('departments', NULL, NULL, $this->school_id, 'name asc', $pages['per_page'], $start_at );
		// Set main layout
		$layout['title'] = 'Departments';
		$layout['showtitle'] = $layout['title'];
		$layout['body'] = $this->load->view('departments/departments_index', $body, True);
		$this->load->view('layout', $layout);
  }
  
  
  
  
  
	/**
	 * Controller function to handle the Add page
	 */
	function add(){
		// Load view
		$layout['title'] = 'Add Department';
		$layout['showtitle'] = $layout['title'];
		$layout['body'] = $this->load->view('departments/departments_add', NULL, True);
		$this->load->view('layout', $layout);
	}
	
	
	
	
	
	/**
	 * Controller function to handle the Edit page
	 */
	function edit($department_id = NULL){
		if($department_id == NULL){ $department_id = $this->uri->segment(3); }
		// Load view
		$body['department'] = $this->crud->Get('departments', 'department_id', $department_id, $this->school_id);
		$layout['title'] = 'Edit Department';
		$layout['showtitle'] = $layout['title'];
		
		$layout['body'] = $this->load->view('departments/departments_add', $body, True);	#$this->load->view('rooms/rooms_add', $body, True);
		$this->load->view('layout', $layout);
	}
	
	
	
	
	
	function save(){
		#print_r($_POST);
	 	// Get ID from form
		$department_id = $this->input->post('department_id');
		
		// Validation rules
		$vrules['department_id']		= 'required';
		$vrules['name']							= 'required|min_length[1]|max_length[50]';
		$vrules['description']			= 'max_length[255]';
		$vrules['icon']							= 'max_length[255]';
		$this->validation->set_rules($vrules);

		// Pretty it up a bit for error validation message
		$vfields['department_id']		= 'Department ID';
		$vfields['name']						= 'Name';
		$vfields['description']			= 'Description';
		$vfields['icon']						= 'Icon';
		$this->validation->set_fields($vfields);

		// Set the error delims to a nice styled red hint under the fields
		$this->validation->set_error_delimiters('<p class="hint error"><span>', '</span></p>');
		
    if ($this->validation->run() == FALSE){
    
    	// Validation failed
			if($department_id != "X"){
				return $this->edit($department_id);
			} else {
				return $this->add();
			}
    
    } else {
    
			// Validation succeeded!
			// Create array for database fields & data
			$data = array();
			$data['name']						= $this->input->post('name');
			$data['description']		=	$this->input->post('description');
			$data['icon']						= $this->input->post('icon');
			
			// Now see if we are editing or adding
			if($department_id == 'X'){
				// No ID, adding new record
				#echo 'adding';
				if( !$this->crud->Add('departments', 'department_id', $data) ){
					$flashmsg = $this->load->view('msgbox/error', 'An error occured adding department <strong>'.$data['name'].'</strong>.', True);
				} else {
					$flashmsg = $this->load->view('msgbox/info', 'Department named <strong>'.$data['name'].'</strong> has been added.', True);
				}
			} else {
				// We have an ID, updating existing record
				if( !$this->crud->Edit('departments', 'department_id', $department_id, $data) ){
					$flashmsg = $this->load->view('msgbox/error', 'A database error occured editing department <strong>'.$data['name'].'</strong>.', True);
				} else {
					$flashmsg = $this->load->view('msgbox/info', 'Department named <strong>'.$data['name'].'</strong> has been modified.', True);
				}
				
			}
			
			// Go back to index
			$this->session->set_flashdata('saved', $flashmsg);
			redirect('departments', 'redirect');
    
		}
	
	}
	
	
	
	
	
	/**
	 * Controller function to delete a department
	 */
	function delete(){
	  // Get ID from URL
		$department_id = $this->uri->segment(3);
		
		// Check if a form has been submitted; if not - show it to ask user confirmation
		if( $this->input->post('id') ){
			// Form has been submitted (so the POST value exists)
			// Call model function to delete manufacturer
			$this->crud->Delete('departments', 'department_id', $this->input->post('id'));
			$this->session->set_flashdata('saved', $this->load->view('msgbox/info', 'The department has been deleted.', True) );
			// Redirect
			redirect('departments', 'redirect');
		} else {
			// Initialise page
			$body['action'] = 'departments/delete';
			$body['id'] = $department_id;
			$body['cancel'] = 'departments';
			$body['text'] = 'If you delete this department, you must re-assign any of its members to another department.';
			// Load page
			$row = $this->crud->Get('departments', 'department_id', $department_id);
			$layout['title'] = 'Delete Department ('.$row->name.')';
			$layout['showtitle'] = $layout['title'];
			$layout['body'] = $this->load->view('partials/deleteconfirm', $body, True);
			$this->load->view('layout', $layout);
		}
	}





}
?>
