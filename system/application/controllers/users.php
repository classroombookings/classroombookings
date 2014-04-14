<?php
class Users extends Controller {





  function Users(){
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
			if( !$this->userauth->CheckAuthLevel( ADMINISTRATOR ) ){
				$this->session->set_flashdata('auth', $this->load->view('msgbox/error', $this->lang->line('crbs_auth_mustbeadmin'), True) );
				redirect('controlpanel', 'location');
			}
		}
		$this->load->model('crud_model', 'crud');
    $this->load->model('users_model', 'M_users');
    $this->load->helper('iconsel');
  }





  function index($start_at = NULL){
  	if($start_at == NULL){ $start_at = $this->uri->segment(3); }

  	$this->load->library('pagination');

		// Init pagination
		$pages['base_url'] = site_url('users/index');
		$pages['total_rows'] = $this->crud->Count('users');
		$pages['per_page'] = '15';
		$pages['full_tag_open'] = '<p style="text-align:center">';
		$pages['full_tag_close'] = '</p>';
		$pages['cur_tag_open'] = ' <b>';
		$pages['cur_tag_close'] = '</b>';
		$pages['first_link'] = '<img src="webroot/images/ui/resultset_first.gif" width="16" height"16" alt="First" title="First" align="top" />';
		$pages['last_link'] = '<img src="webroot/images/ui/resultset_last.gif" width="16" height"16" alt="Last" title="Last" align="top" />';
		$pages['next_link'] = '<img src="webroot/images/ui/resultset_next.gif" width="16" height"16" alt="Next" title="Next" align="top" />';
		$pages['prev_link'] = '<img src="webroot/images/ui/resultset_previous.gif" width="16" height"16" alt="Previous" title="Previous" align="top" />';
		$this->pagination->initialize($pages);

		$body['pagelinks'] = $this->pagination->create_links();
		// Get list of rooms from database
		$body['users'] = $this->crud->Get('users', NULL, NULL, $this->school_id, 'authlevel asc, enabled asc, username asc', $pages['per_page'], $start_at );
		#$body['users'] = $this->M_users->Get();	//$this->session->userdata('school_id'));

		// Set main layout
		$layout['title'] = 'Manage Users';
		$layout['showtitle'] = $layout['title'];
		$layout['body'] = $this->load->view('users/users_index', $body, True);
		$this->load->view('layout', $layout);
  }





  /* function index(){
  	$body['users'] = $this->M_users->Get();	//$this->session->userdata('school_id'));

		$layout['title'] = 'Manage Users';
		$layout['showtitle'] = $layout['title'];
		$layout['body'] = $this->load->view('users/users_index', $body, True);
		$this->load->view('layout', $layout);
  } */





	function add(){
		$body['departments'] = $this->crud->Get('departments');
		// Load view
		$layout['title'] = 'Add User';
		$layout['showtitle'] = $layout['title'];

		$cols[0]['content'] = $this->load->view('users/users_add', $body, True);
		$cols[0]['width'] = '70%';
		$cols[1]['content'] = $this->load->view('users/users_add_side', $body, True);
		$cols[1]['width'] = '30%';

		$layout['body'] = $this->load->view('columns', $cols, True);
		$this->load->view('layout', $layout);
	}





	function edit($id = NULL){
		if($id == NULL){ $id = $this->uri->segment(3); }
		$body['user'] = $this->M_users->Get($id);
		#print_r($body);
		$body['departments'] = $this->crud->Get('departments');

		// Load view
		$layout['title'] = 'Edit User';
		$layout['showtitle'] = $layout['title'];

		$cols[0]['content'] = $this->load->view('users/users_add', $body, True);
		$cols[0]['width'] = '70%';
		$cols[1]['content'] = $this->load->view('users/users_add_side', $body, True);
		$cols[1]['width'] = '30%';

		$layout['body'] = $this->load->view('columns', $cols, True);
		$this->load->view( 'layout', $layout);
	}





	/**
	 * Save
	 */
	function save(){
		#print_r($_POST);

	 	// Get ID from form
		$user_id = $this->input->post('user_id');

		// Load validation
		#$this->load->library('validation');

		// Validation rules
		$vrules['user_id']				= 'required';
		$vrules['username']   		= 'required|max_length[20]|min_length[1]';
		$vrules['password1']			= 'max_length[64]|min_length[1]';
		$vrules['password2']			= 'max_length[64]|min_length[1]|matches[password1]';
		$vrules['authlevel']			= 'required';
		$vrules['bquota']					= 'numeric|max_length[3]';
		$vrules['email']          = 'max_length[255]|valid_email';
		$vrules['firstname']			= 'max_length[20]';
		$vrules['lastname']				= 'max_length[20]';
		$vrules['displayname']		= 'max_length[20]';
		$vrules['extension']			= 'max_length[10]';
		$this->validation->set_rules($vrules);

		// Name the validation fields if an error occurs
		$vfields['user_id']					= 'User ID';
		$vfields['username']				= 'Username';
		$vfields['password1']				= 'Password';
		$vfields['password2']				= 'Password confirmation';
		$vfields['authlevel']				= 'User type';
		$vfields['enabled']					= 'Enabled';
		$vfields['bquota']					= 'Booking quota';
		$vfields['email']						= 'Email address';
		$vfields['firstname']				= 'First name';
		$vfields['lastname']				= 'Last name';
		$vfields['displayname']			= 'Display name';
		$vfields['department_id']		= 'Department';
		$vfields['ext']							= 'Extension';
		$this->validation->set_fields($vfields);

		// Set the error delims to a nice styled red hint under the fields
		$this->validation->set_error_delimiters('<p class="hint error"><span>', '</span></p>');

    if ($this->validation->run() == FALSE){

      // Validation failed
			if($user_id != "X"){
				$this->edit($user_id);
			} else {
				$this->add();
			}

		} else {

			// Validation succeeded!

			$data['username'] 				= $this->input->post('username');
			$data['authlevel'] 				= $this->input->post('authlevel');
			$data['enabled'] 					= ($this->input->post('enabled') == '1') ? 1 : 0;
			#$data['bquota']						= $this->input->post('bquota');
			$data['email']						= $this->input->post('email');
			$data['firstname']				= $this->input->post('firstname');
			$data['lastname']					= $this->input->post('lastname');
			$data['displayname']			= $this->input->post('displayname');
			$data['department_id']		= $this->input->post('department_id');
			$data['ext']							= $this->input->post('ext');
			// Only update password if one was supplied
			if($this->input->post('password1') && $this->input->post('password2')){
				$data['password'] 			= sha1($this->input->post('password1'));
			}

			// Now see if we are editing or adding
			if($user_id == 'X'){
				// No ID, adding new record
				if(!$this->crud->Add('users', 'user_id', $data)){
					$flashmsg = $this->load->view('msgbox/error', 'A database error occured while adding the user.', True);
				} else {
					$flashmsg = $this->load->view('msgbox/info', 'User <strong>'.$data['username'].'</strong> has been created.', True);
					if($data['enabled'] == 0){
						$flashmsg .= $this->load->view('msgbox/warning', '<strong>'.$data['username'].'</strong> will not be able to log on until their account is enabled.', True);
					}
				}
			} else {
				// We have an ID, updating existing record
				if(!$this->crud->Edit('users', 'user_id', $user_id, $data)){
					$flashmsg = $this->load->view('msgbox/error', 'A database error occured while editing the user.', True);
				} else {
					$flashmsg = $this->load->view('msgbox/info', 'User properties for <strong>'.$data['username'].'</strong> have been successfully changed.', True);
					if($data['enabled'] == 0){
						$flashmsg .= $this->load->view('msgbox/warning', '<strong>'.$data['username'].'</strong> will not be able to log on until their account is enabled.', True);
					}
				}
			}

			// Go back to index
			$this->session->set_flashdata('saved', $flashmsg);
			redirect('users', 'redirect');

		}

	}





	/**
	 * Controller function to delete a user
	 */
	function delete(){
	  // Get ID from URL
		$user_id = $this->uri->segment(3);

		// Check if a form has been submitted; if not - show it to ask user confirmation
		if( $this->input->post('id') ){
			// Form has been submitted (so the POST value exists)
			// Call model function to delete user
			$ret = $this->crud->Delete('users', 'user_id', $this->input->post('id'));
			if(!$ret){
				$flashmsg = $this->load->view('msgbox/info', 'The user has been deleted.', True);
			} else {
				$flashmsg = $this->load->view('msgbox/error', 'A database error occured deleting the user.', True);
			}
			// Redirect
			$this->session->set_flashdata('saved', $flashmsg);
			redirect('users', 'redirect');
		} else {
			// Initialise page
			$body['action'] = 'users/delete';
			$body['id'] = $user_id;
			$body['cancel'] = 'users';
			$body['text'] = 'If you delete this user, all of their bookings and room owenership information will also be deleted.';
			// Load page
			$row = $this->crud->Get('users', 'user_id', $user_id);
			if($row){
				$layout['title'] = 'Delete User ('.$row->username.')';
				$layout['showtitle'] = $layout['title'];
				$layout['body'] = $this->load->view('partials/deleteconfirm', $body, True);
				$this->load->view('layout', $layout);
			} else {
				$this->load->view('layout', array('title' => 'Sorry!', 'body' => 'Not your user to delete!') );
			}
		}
	}





	function import(){
		$layout['title'] = 'Import Users';
		$layout['showtitle'] = $layout['title'];
		$layout['body'] = $this->load->view('users/import/stage1', NULL, True);
		$this->load->view('layout', $layout);
	}


	function import2(){
		// Load upload library
		$this->load->library('upload');

		// Load file helper library
		$this->load->helper('file');

		// Upload config
		$upload['upload_path'] 			= 'temp';
		$upload['allowed_types']		= 'csv';
		$upload['max_size']					= '1024';
		$upload['encrypt_name']			= true;
		$this->upload->initialize($upload);

		// Get default values
		$password = $this->input->post('password');
		$authlevel = $this->input->post('authlevel');

		// Do upload of CSV
		if(!$this->upload->do_upload()){

			// Upload failed
			$error = $this->upload->display_errors('<div class="msgbox error">','</div>');

		} else {

			// Upload OK
			$csv = $this->upload->data();
			$file = $csv['full_path'];
			$users = array();
			$user = 0;
			$handle = fopen($csv['full_path'], 'r');

			// Parse CSV file
			while(($csvdata = fgetcsv($handle, filesize($csv['full_path']), ',')) !== FALSE){
				// Get columns
				$users[$user]['username'] = $csvdata[0];
				$users[$user]['firstname'] = $csvdata[1];
				$users[$user]['lastname'] = $csvdata[2];
				$users[$user]['email'] = $csvdata[3];
				$users[$user]['password'] = (isset($csvdata[4])) ? $csvdata[4] : $password;

				// Data array to send to database
				$data['username'] 				= $users[$user]['username'];
				$data['authlevel'] 				= $authlevel;
				$data['enabled'] 					= 1;
				$data['email']						= $users[$user]['email'];
				$data['firstname']				= $users[$user]['firstname'];
				$data['lastname']					= $users[$user]['lastname'];
				$data['displayname']			= $data['firstname'] . ' ' . $data['lastname'];
				$data['department_id']		= NULL;
				$data['ext']							= NULL;
				$data['password']					= sha1($users[$user]['password']);

				// Run checks before finally submitting to database.
				if(!$data['username']){
					$users[$user]['_status'] = 'Failed (No username)';
				} else {
					if(!$users[$user]['password']){
						$users[$user]['_status'] = 'Failed (No password)';
					} else {
						// Check if user already exists
						if($this->_userexists($data['username']) == TRUE){
							$users[$user]['_status'] = 'Failed (User already exists)';
						} else {
							// Add the user to database
							if($this->crud->Add('users', 'user_id', $data) == TRUE){
								$users[$user]['_status'] = 'Success';
							} else {
								$users[$user]['_status'] = 'Failed (Database error)';
							}
						}
					}
				}

				unset($data);
				$user++;

			}

			// All done, delete file
			@unlink($csv['full_path']);

		}


		// Check what we need to do - if $error then the file upload failed.
		if(isset($error)){
			// The file upload failed
			$body['result'] = $error;
			$layout['showtitle'] = 'CSV Upload Failed';
		} else {
			// Put user data into array
			$body['result'] = $users;
			$layout['showtitle'] = 'User Import Results';
		}


		// Load view
		$layout['title'] = 'Imported Users';
		$layout['body'] = $this->load->view('users/import/stage2', $body, True);
		$this->load->view('layout', $layout);

	}



	function _userexists($username){
		$sql = "SELECT user_id FROM users WHERE username='$username' LIMIT 1";
		$query = $this->db->query($sql);
		if($query->num_rows() == 1){
			return true;
		} else {
			return false;
		}
	}





	/*function import(){
		// Load upload library
		$this->load->library('upload');

		// Load file helper library
		$this->load->helper('file');

		// Upload config
		$upload['upload_path'] 			= 'temp';
		$upload['allowed_types']		= 'csv';
		$upload['max_size']					= '1024';
		$upload['encrypt_name']			= true;
		$this->upload->initialize($upload);

		#echo var_export($_POST, true);
		#$stage = $this->uri->segment(3,1);

		// Set the number of stages in this wizard
		$stage_config['first'] = 1;
		$stage_config['last'] = 3;

		// Get stage number from post var. If first time, load first stage
		$stage = ($this->input->post('stage')) ? $this->input->post('stage') : 1;

		$continue = true;

		switch($this->input->post('stage')){
			case 1:

				// Uploading CSV file
				#echo "Processing CSV file";
				if(!$this->upload->do_upload()){
					$error = $this->upload->display_errors('<div class="msgbox error">','</div>');
					echo $error;
					$continue = false;
				} else {
					$csv = $this->upload->data();
					#print_r($csv);
					$rows = array();
					$row = 1;
					$handle = fopen($csv['full_path'], 'r');
					while(($data = fgetcsv($handle, filesize($csv['full_path']), ',')) !== FALSE){
						$rows[$row] = $data;
						$row++;
					}
					$body['csvdata'] = $rows;
					$_POST['csvdata'] = serialize($rows);
					$continue = true;
				}

			break;
		}

		// Adjust the stage number according to which button was clicked
		if($continue == true){
			if($this->input->post('submit') == '   < Back   '){ $stage--; unset($_POST); }
			if($this->input->post('submit') == '   Next >   '){ $stage++; }
		}

		// Layout
		$layout['title'] = 'Import Users - Stage '.$stage;
		$layout['showtitle'] = $layout['title'];

		// Put the stage number into the post array (which is then passed to the form creation helper to make hidden inputs)
		$_POST['stage'] = $stage;

		// Load view!
		$body['stage'] = $stage;
		$body['post'] = $_POST;
		$body['stage_config'] = $stage_config;
		$body['departments'] = $this->crud->Get('departments');
		$layout['body'] = $this->load->view('users/import/stage'.$stage, $body, True);
		$this->load->view('layout', $layout);
	} */





}
?>
