<?php
class Install extends Controller {





  function Install(){
    parent::Controller();
    $this->loggedin = False;
    $this->load->model('crud_model', 'crud');
    $this->load->model('school_model', 'M_school');
    $this->load->helper('file');
  }
  
  
  
  
  
  function index(){
  	// Read database info from file
  	include('system/application/config/database.php');
  	$content0['db']['hostname'] = $db['default']['hostname'];
  	$content0['db']['username'] = $db['default']['username'];
  	$content0['db']['database'] = $db['default']['database'];
  	$content0['db']['password'] = str_repeat('*', strlen($db['default']['password']));
  	unset($db);
  	
	  // Initialise columns view
	  $content[0]['content'] = $this->load->view('install/install_index', $content0, True);
	  $content[0]['width'] = '72%';
	  $content[1]['content'] = $this->load->view('install/install_index_side', NULL, True);
	  $content[1]['width'] = '28%';
	  
	  // Load view
	  $layout['title'] = 'Install classroombookings';
	  $layout['showtitle'] = $layout['title'];
	  $layout['body'] = $this->load->view('columns', $content, True );
    $this->load->view('layout', $layout);
  }
  
  
  
  
  
  /**
   * Controller function to handle a submitted form
   */
  function submit(){
		// Parse data input from view and carry out appropriate action.
		// Get ID from form
		#$mfrid = $this->input->post('mfrid');
		
		// Validation rules
		$vrules['schoolname']			= "required|max_length[255]";
		$vrules['website']	  		= "prep_url|max_length[255]";
		$vrules['username']   		= "required|max_length[20]|min_length[4]";
		$vrules['password1']			= "required|max_length[20]|min_length[6]";
		$vrules['password2']			= "required|max_length[20]|min_length[6]|matches[password1]";
		$this->validation->set_rules($vrules);

		// Pretty it up a bit for error validation message
		$vfields['schoolname']		= 'School name';
		$vfields['website']	  		= 'Website address';
		$vfields['username']			= 'Username';
		$vfields['password1']			= 'Password';
		$vfields['password2']			= 'Password confirmation';
		$this->validation->set_fields($vfields);

		// Set the error delims to a nice styled red box
		$this->validation->set_error_delimiters('<p class="hint error"><span>', '</span></p>');
		
	
    if ($this->validation->run() == FALSE){
    
      // Validation failed
			return $this->index();
			
		} else {
		  
		  // Validation succeeded!
		  
		  // Create database tables first
			$tables = $this->_create_tables();
			if($tables == FALSE){
				$body['db'] = $this->load->view('msgbox/error', 'An error occured creating the database tables.', True);
			} else {
				$body['db'] = $this->load->view('msgbox/info', 'Database tables were created successfully.', True);
			}
		  
		  
		  // Create school
			$school_data['name'] = $this->input->post('schoolname');
			$school_data['website'] = $this->input->post('website');
			$school_id = $this->M_school->add($school_data);
			
			
			// Create user
			$user_data['school_id']		= $school_id;
			$user_data['username'] 		= $this->input->post('username');
			$user_data['password']		= sha1($this->input->post('password1'));
			$user_data['authlevel']		= 1;
			$user_data['enabled']			= 1;
			$user_data['email']				= $this->input->post('email');
			$this->crud->Add('users', 'user_id', $user_data);
			
			
			/* if( !$this->crud->Add('ci_users', 'user_id', $user_data) ){
				$flashmsg = $this->load->view('msgbox/error', 'A database error occured while adding the user. Please notify the administrator.', True);
			} else {
				$flashmsg = $this->load->view('msgbox/info', 'User <strong>'.$data['username'].'</strong> has been created.', True);
			} */

			
			// Body info
			$body['user'] = $user_data;
			$body['school'] = $school_data;
			
		  $layout['title'] = 'Congratulations!';
		  $layout['showtitle'] = $layout['title'];
		  $layout['body'] = $this->load->view('install/finished', $body, True);
		  $this->load->view('layout', $layout);
		}

	}
	
	
	
	
	
	function _create_tables(){
		$errcount = 0;
		$file = read_file('classroombookings.sql');
		$array = explode(';', $file);
		foreach($array as $query){
			if($query != NULL){
				// Read file successfully - return the result of the query (true/false)
				$query = $this->db->query($query);
				if( $query == FALSE ){ $errcount++; }
			}
		}
		if($errcount > 0){
			return false;
		} else {
			return true;
		}
	}
	
	

	
	
	function validate($valcode){
		$retval = $this->_validate($valcode);
		if($retval != False){
			$code = 'login/'.$retval; 
			$body = $this->load->view('msgbox/info', 'Your account has been successfully validated!', True);
			$icondata[0] = array($code, 'Click here to login', 'user_go.png' );
			$body .= $this->load->view('partials/iconbar', $icondata, True);
		} else {
			$body = $this->load->view('msgbox/error', 'Your account could not be validated. Please <a href="'.site_url('contact').'">contact us</a>.', True);
		}
	  $layout['title'] = 'Congratulations!';
	  $layout['showtitle'] = $layout['title'];
		$layout['body'] = $body;
	  $this->load->view('layout', $layout);
	}
	
	
	
	
	
	function _validate($valcode){
		// See if we have validation code (also get school_id)
		$query_str = "SELECT school_id,validate FROM ci_users WHERE validate='$valcode' LIMIT 1";
		$query = $this->db->query($query_str);
		// One row - validation code exists!
		if($query->num_rows() == 1){
			
			// Now we get the school_id
			$row = $query->row();
			$school_id = $row->school_id;
			
			// Get the school code
			$query_str = "SELECT code FROM schools WHERE school_id='$school_id' LIMIT 1";
			$query = $this->db->query($query_str);
			
			if($query->num_rows() == 1){
				// Got school code
				$row = $query->row();
				$school_code = $row->code;
				
				// Now update the ci_users table and enable the user
				$query_str = "UPDATE ci_users SET enabled=1 WHERE validate='$valcode' LIMIT 1";
				$query = $this->db->query($query_str);
	
				if($query){
					// User updated OK
					return $school_code;
				} else {
					return false;
				}
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	
	
	
	function schoolcode_exists($schoolcode){
		$lookup = $this->M_school->schoolcode_exists($schoolcode);
		if( $lookup == 0 ){
			$ret = true;
		}
		if( $lookup == 1 ){
			$this->validation->set_message('schoolcode_exists', 'The school code you entered has already been taken.');
			$ret = false;
		}
		if( $lookup == 3){
			$this->validation->set_message('schoolcode_exists', 'The school code you entered is restricted; please choose another one.');
			$ret = false;
		}
		return $ret;
		/*if ($this->M_school->schoolcode_exists($schoolcode)){
			$this->validation->set_message('schoolcode_exists', 'The school code you entered already exists');
			return false;
		} else {
			return true;
		}*/
	}





}
?>
