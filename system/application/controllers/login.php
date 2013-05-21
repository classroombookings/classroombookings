<?php
class Login extends Controller {





  function Login(){
    parent::Controller();
    $this->loggedin = False;
    $this->load->model('School_model', 'M_school');
  }
  
  
  
  
  
  function index(){
  	
  	$layout['title'] = 'Login';
  	$layout['showtitle'] = $layout['title'];
		$school_data = $this->M_school->GetInfo();
  	$logo = 'webroot/images/schoollogo/200/'.$school_data->logo;
  			
		$cols[0]['content'] = $this->load->view('login/login_index', NULL, True);
		$cols[0]['width'] = '60%';

		if(file_exists($logo) && ! empty($school_data->logo)){
			$cols[1]['content'] = '<img src="'.$logo.'" />';
		} else {
			$cols[1]['content'] = '';
		}
		$cols[1]['width'] = '40%';
		
  	$layout['showtitle'] = 'Login to '.$school_data->name;
		$layout['body'] = $this->load->view('columns', $cols, True);

  	$this->load->view('layout', $layout);
  }
  
  
  
  
  
  function submit(){
  	log_message('debug', 'login submit');
		// Load validation rules & fields (used multiple times)
		#$this->load->script('validation/validation_login');
		
		// Validation rules
		$vrules['username']				= "required|max_length[20]|min_length[4]";
		$vrules['password']				= "required|max_length[20]|min_length[6]";
		$this->validation->set_rules($vrules);
		
		// Pretty it up a bit for error validation message
		$vfields['username']			= 'Username';
		$vfields['password1']			= 'Password';
		$this->validation->set_fields($vfields);

		// Set the error delims to a nice styled red box
		#$this->validation->set_error_delimiters('<span class="error">', '</span>');
		$this->validation->set_error_delimiters('<p class="hint error"><span>', '</span></p>');
		
		// Run validation
    if ($this->validation->run() == FALSE){

      // Validation failed, load login page again
			return $this->index();

    } else {

			// Form validation for length etc. passed, now see if the credentials are OK in the DB
			// Post values 
			$username = $this->input->post('username');
			$password = $this->input->post('password');
			
			// Now see if we can login
			if( $this->userauth->trylogin($username, $password) ){
			
				// Success! Redirect to control panel
				redirect('controlpanel', 'location');
				
			} else {
			
				//$school_data = $this->M_school->GetInfo();
		  	// User is from the /login/<schoolcode> page
		  	//$layout['title'] = 'Sorry!';
  			//$layout['showtitle'] = 'Login to '.$school_data->name;
				//$layout['body'] = $this->load->view('msgbox/error', 'Incorrect username and/or password.', True) . $this->load->view('login/login_index', $view, True);
		  	//$this->load->view('layout', $layout);
		  	$this->session->set_flashdata('auth', $this->load->view('msgbox/error', 'Incorrect username and/or password.', True));
		  	redirect('login');
		  	
			}

  	}
  
  }




}
