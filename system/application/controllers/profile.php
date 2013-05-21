<?php
class Profile extends Controller {





  function Profile(){
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
		}
		
		// Required libraries, models etc
		$this->load->library('email');
		$this->load->model('crud_model', 'crud');
		$this->load->model('bookings_model', 'M_bookings');
		$this->load->model('users_model', 'M_users');
  }
  
  
  
  
  
  function index(){
  	// Get User ID from session
  	$user_id = $this->session->userdata('user_id');
  	// Get bookings for a room if this user owns one
  	$body['myroom'] = $this->M_bookings->ByRoomOwner($user_id);
  	// Get all bookings made by this user (only staff ones)
  	$body['mybookings'] = $this->M_bookings->ByUser($user_id);
  	// Get totals
  	$body['total'] = $this->M_bookings->TotalNum($user_id, $this->school_id);
  	
		$layout['title'] = 'My Profile';
		$layout['showtitle'] = $layout['title'];
		$layout['body'] = $this->load->view('profile/profile_index', $body, True);
		$this->load->view('layout', $layout);
  }
  
  
  
  
  
  function edit(){
  	// Get User ID from session
  	$user_id = $this->session->userdata('user_id');
  	// Get bookings for a room if this user owns one
  	$body['user'] = $this->M_users->Get($user_id);
  	
		$cols[0]['content'] = $this->load->view('profile/profile_edit', $body, True);
		$cols[0]['width'] = '70%';
		$cols[1]['content'] = $this->load->view('profile/profile_edit_side', $body, True);
		$cols[1]['width'] = '30%';
  	
		$layout['title'] = 'Edit my details';
		$layout['showtitle'] = $layout['title'];
		$layout['body'] = $this->load->view('columns', $cols, True);
		$this->load->view('layout', $layout);
  }
  
  
  
  
  
  function save(){
	 	// Get ID from form
		$user_id = $this->input->post('user_id');
		
		// Validation rules
		$vrules['user_id']				= 'required';
		$vrules['password1']			= 'max_length[20]|min_length[6]';
		$vrules['password2']			= 'max_length[20]|min_length[6]|matches[password1]';
		$vrules['email']          = 'required|max_length[255]|valid_email';
		$vrules['firstname']			= 'max_length[20]';
		$vrules['lastname']				= 'max_length[20]';
		$vrules['displayname']		= 'max_length[20]';
		$vrules['extension']			= 'max_length[10]';
		$this->validation->set_rules($vrules);

		// Name the validation fields if an error occurs
		$vfields['user_id']					= 'User ID';
		$vfields['password1']				= 'Password';
		$vfields['password2']				= 'Password confirmation';
		$vfields['email']						= 'Email address';
		$vfields['firstname']				= 'First name';
		$vfields['lastname']				= 'Last name';
		$vfields['displayname']			= 'Display name';
		$vfields['ext']							= 'Extension';
		$this->validation->set_fields($vfields);
		
		// Set the error delims to a nice styled red hint under the fields
		$this->validation->set_error_delimiters('<p class="hint error"><span>', '</span></p>');
		
    if ($this->validation->run() == FALSE){
    
      // Validation failed
			return $this->edit($user_id);

		} else {
		
			// Validation passed!
			$data['email']						= $this->input->post('email');
			$data['firstname']				= $this->input->post('firstname');
			$data['lastname']					= $this->input->post('lastname');
			$data['displayname']			= $this->input->post('displayname');
			$data['ext']							= $this->input->post('ext');
			// Only update password if one was supplied
			if($this->input->post('password1') && $this->input->post('password2')){
				$data['password'] 			= sha1($this->input->post('password1'));
			}
			
			// Update session variable with displayname
			$this->session->set_userdata('displayname', $data['displayname']);
			
			// Now call database to update user and load appropriate message for return value			
			if(!$this->crud->Edit('users', 'user_id', $user_id, $data)){
				$flashmsg = $this->load->view('msgbox/error', 'A database error occured while updating your details.', True);
			} else {
				$flashmsg = $this->load->view('msgbox/info', 'Your details have been successfully updated.', True);
			}
			
			// Go back to index
			$this->session->set_flashdata('saved', $flashmsg);
			redirect('profile', 'redirect');
			
		}
		
  }





}
?>
