<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends MY_Controller
{




	public function __construct()
	{
		parent::__construct();

		$this->require_logged_in();
		$this->require_auth_level(ADMINISTRATOR);

		$this->load->model('crud_model');
		$this->load->model('users_model');
		$this->load->model('departments_model');
	}




	function index($page = NULL)
	{
		$pagination_config = array(
			'base_url' => site_url('users/index'),
			'total_rows' => $this->crud_model->Count('users'),
			'per_page' => 25,
			'full_tag_open' => '<p class="pagination">',
			'full_tag_close' => '</p>',
		);

		$this->load->library('pagination');
		$this->pagination->initialize($pagination_config);

		$this->data['pagelinks'] = $this->pagination->create_links();
		$this->data['users'] = $this->users_model->Get(NULL, $pagination_config['per_page'], $page);

		$this->data['title'] = 'Manage Users';
		$this->data['showtitle'] = $this->data['title'];
		$this->data['body'] = $this->load->view('users/users_index', $this->data, TRUE);

		return $this->render();
	}




	function add()
	{
		$this->data['departments'] = $this->departments_model->Get();

		$this->data['title'] = 'Add User';
		$this->data['showtitle'] = $this->data['title'];

		$columns = array(
			'c1' => array(
				'content' => $this->load->view('users/users_add', $this->data, TRUE),
				'width' => '70%',
			),
			'c2' => array(
				'content' => $this->load->view('users/users_add_side', $this->data, TRUE),
				'width' => '30%',
			),
		);

		$this->data['body'] = $this->load->view('columns', $columns, TRUE);

		return $this->render();
	}





	function edit($id = NULL)
	{
		$this->data['user'] = $this->users_model->Get($id);

		if (empty($this->data['user'])) {
			show_404();
		}

		$this->data['departments'] = $this->departments_model->Get();

		$this->data['title'] = 'Edit User';
		$this->data['showtitle'] = $this->data['title'];

		$columns = array(
			'c1' => array(
				'content' => $this->load->view('users/users_add', $this->data, TRUE),
				'width' => '70%',
			),
			'c2' => array(
				'content' => $this->load->view('users/users_add_side', $this->data, TRUE),
				'width' => '30%',
			),
		);

		$this->data['body'] = $this->load->view('columns', $columns, TRUE);

		return $this->render();
	}





	/**
	 * Save user details
	 *
	 */
	function save()
	{
		$user_id = $this->input->post('user_id');

		$this->load->library('form_validation');

		$this->form_validation->set_rules('user_id', 'ID', 'integer');
		$this->form_validation->set_rules('username', 'Username', 'required|max_length[20]');
		$this->form_validation->set_rules('authlevel', 'Type', 'required|integer');
		$this->form_validation->set_rules('enabled', 'Enabled', 'required|integer');
		$this->form_validation->set_rules('email', 'Email address', 'valid_email|max_length[255]');

		if (empty($user_id)) {
			$this->form_validation->set_rules('password1', 'Password', 'trim|required');
			$this->form_validation->set_rules('password2', 'Password (confirm)', 'trim|matches[password1]');
		} else {
			if ($this->input->post('password1')) {
				$this->form_validation->set_rules('password1', 'Password', 'trim');
				$this->form_validation->set_rules('password2', 'Password (confirm)', 'trim|matches[password1]');
			}
		}

		$this->form_validation->set_rules('firstname', 'First name', 'max_length[20]');
		$this->form_validation->set_rules('lastname', 'Last name', 'max_length[20]');
		$this->form_validation->set_rules('displayname', 'Display name', 'max_length[20]');
		$this->form_validation->set_rules('department_id', 'Department', 'integer');
		$this->form_validation->set_rules('ext', 'Extension', 'max_length[10]');

		if ($this->form_validation->run() == FALSE) {
			return (empty($user_id) ? $this->add() : $this->edit($user_id));
		}

		$user_data = array(
			'username' => $this->input->post('username'),
			'authlevel' => $this->input->post('authlevel'),
			'enabled' => $this->input->post('enabled'),
			'email' => $this->input->post('email'),
			'firstname' => $this->input->post('firstname'),
			'lastname' => $this->input->post('lastname'),
			'displayname' => $this->input->post('displayname'),
			'department_id' => $this->input->post('department_id'),
			'ext' => $this->input->post('ext'),
		);

		if ($this->input->post('password1') && $this->input->post('password2')) {
			$user_data['password'] = sha1($this->input->post('password1'));
		}

		if (empty($user_id)) {

			$user_id = $this->users_model->Add($user_data);

			if ($user_id) {
				$line = sprintf($this->lang->line('crbs_action_added'), $user_data['username']);
				$flashmsg = msgbox('info', $line);
			} else {
				$line = sprintf($this->lang->line('crbs_action_dberror'), 'adding');
				$flashmsg = msgbox('error', $line);
			}

		} else {

			if ($this->users_model->Edit($user_id, $user_data)) {
				$line = sprintf($this->lang->line('crbs_action_saved'), $user_data['username']);
				$flashmsg = msgbox('info', $line);
			} else {
				$line = sprintf($this->lang->line('crbs_action_dberror'), 'editing');
				$flashmsg = msgbox('error', $line);
			}

		}

		$this->session->set_flashdata('saved', $flashmsg);
		redirect('users');
	}





	/**
	 * Controller function to delete a user
	 */
	function delete($id = NULL)
	{
		if ($this->input->post('id')) {
			$ret = $this->users_model->Delete($this->input->post('id'));
			$flashmsg = msgbox('info', $this->lang->line('crbs_action_deleted'));
			$this->session->set_flashdata('saved', $flashmsg);
			return redirect('users');
		}

		if ($id == $_SESSION['user_id']) {
			$flashmsg = msgbox('error', "You cannot delete your own user account.");
			$this->session->set_flashdata('saved', $flashmsg);
			return redirect('users');
		}

		$this->data['action'] = 'users/delete';
		$this->data['id'] = $id;
		$this->data['cancel'] = 'users';
		$this->data['text'] = 'If you delete this user, all of their past and future bookings will also be deleted, and their rooms will no longer be owned by them.';

		$row = $this->users_model->Get($id);

		$this->data['title'] = 'Delete User ('.html_escape($row->username).')';
		$this->data['showtitle'] = $this->data['title'];
		$this->data['body'] = $this->load->view('partials/deleteconfirm', $this->data, TRUE);

		return $this->render();
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
