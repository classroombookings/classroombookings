<?php
/*
	This file is part of Classroombookings.

	Classroombookings is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	Classroombookings is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with Classroombookings.  If not, see <http://www.gnu.org/licenses/>.
*/


class Users extends Controller {


	var $tpl;
	var $lasterr;
	
	
	function Users(){
		parent::Controller();
		$this->load->model('security');
		$this->tpl = $this->config->item('template');
		$this->output->enable_profiler($this->config->item('profiler'));
	}
	
	
	
	
	function index(){
		$this->auth->check('users');
		$links[] = array('security/users/add', 'Add a new user');
		$links[] = array('security/users/import', 'Import from file');
		$links[] = array('security/groups', 'Manage groups');
		$links[] = array('security/permissions', 'Change group permissions');
		$tpl['links'] = $this->load->view('parts/linkbar', $links, TRUE);
		
		// Get list of users
		$body['users'] = $this->security->get_user();
		if ($body['users'] == FALSE) {
			$tpl['body'] = $this->msg->err($this->security->lasterr);
		} else {
			$tpl['body'] = $this->load->view('security/users.index.php', $body, TRUE);
		}
		
		$tpl['title'] = 'Users';
		$tpl['pagetitle'] = 'Manage users';
		
		$this->load->view($this->tpl, $tpl);
	}
	
	
	
	
	function ingroup($group_id){
		$this->auth->check('users');
		$links[] = array('security/users/add', 'Add a new user');
		$links[] = array('security/users/import', 'Import from file');
		$links[] = array('security/groups', 'Manage groups');
		$links[] = array('security/permissions', 'Change group permissions');
		$tpl['links'] = $this->load->view('parts/linkbar', $links, TRUE);
		
		$tpl['title'] = 'Users';
		$groupname = $this->security->get_group_name($group_id);
		if ($groupname == FALSE) {
			$tpl['body'] = $this->msg->err($this->security->lasterr);
			$tpl['pagetitle'] = $tpl['title'];
		} else {
			$body['users'] = $this->security->get_user(NULL, $group_id);
			if ($body['users'] === FALSE) {
				$tpl['body'] = $this->msg->err($this->security->lasterr);
			} else {
				$tpl['body'] = $this->load->view('security/users.index.php', $body, TRUE);
			}
			$tpl['pagetitle'] = sprintf('Manage users in the %s group', $groupname);
		}
		
		$this->load->view($this->tpl, $tpl);
	}
	
	
	
	
	function add(){
		$this->auth->check('users.add');
		$body['user'] = NULL;
		$body['user_id'] = NULL;
		$body['groups'] = $this->security->get_groups_dropdown();
		$tpl['sidebar'] = $this->load->view('security/users.addedit.side.php', NULL, TRUE);
		$tpl['title'] = 'Add user';
		$tpl['pagetitle'] = 'Add a new user';
		$tpl['body'] = $this->load->view('security/users.addedit.php', $body, TRUE);
		$this->load->view($this->tpl, $tpl);
	}
	
	
	
	
	function edit($user_id){
		$this->auth->check('users.edit');
		$body['user'] = $this->security->get_user($user_id);
		$body['user_id'] = $user_id;
		$body['groups'] = $this->security->get_groups_dropdown();
		
		$tpl['title'] = 'Edit user';
		$tpl['pagetitle'] = ($body['user']->displayname == FALSE) ? 'Edit ' . $body['user']->username : 'Edit ' . $body['user']->displayname;
		$tpl['body'] = $this->load->view('security/users.addedit.php', $body, TRUE);
		
		$this->load->view($this->tpl, $tpl);
	}
	
	
	
	
	function save(){
		$user_id = $this->input->post('user_id');
		
		$this->form_validation->set_rules('user_id', 'User ID');
		$this->form_validation->set_rules('username', 'Username', 'required|min_length[1]|max_length[64]|trim');
		if(!$user_id){
			$this->form_validation->set_rules('password1', 'Password', 'max_length[104]|required');
			$this->form_validation->set_rules('password2', 'Password (confirmation)', 'max_length[104]|required|matches[password1]');
		}
		$this->form_validation->set_rules('group_id', 'Group', 'required|integer');
		$this->form_validation->set_rules('enabled', 'Enabled', 'exact_length[1]');
		$this->form_validation->set_rules('email', 'Email address', 'max_length[256]|valid_email|trim');
		$this->form_validation->set_rules('displayname', 'Display name', 'max_length[64]|trim');
		//$this->form_validation->set_rules('department_id', 'Department', 'integer');
		$this->form_validation->set_error_delimiters('<li>', '</li>');
		
		if($this->form_validation->run() == FALSE){
			
			// Validation failed - load required action depending on the state of user_id
			($user_id == NULL) ? $this->add() : $this->edit($user_id);
			
		} else {
			
			// Validation OK
			$data['username'] = $this->input->post('username');
			$data['displayname'] = $this->input->post('displayname');
			$data['email'] = $this->input->post('email');
			$data['group_id'] = $this->input->post('group_id');
			//$data['department_id'] = $this->input->post('department_id');
			$data['enabled'] = ($this->input->post('enabled') == '1') ? 1 : 0;
			// Only set password if supplied.
			if($this->input->post('password1')){
				$data['password'] = sha1($this->input->post('password1'));
			}
			
			if($user_id == NULL){
				
				// Adding user
				$data['ldap'] = 0;
				
				#die(var_export($data, true));
				$add = $this->security->add_user($data);
				
				if($add == TRUE){
					$message = ($data['enabled'] == 1) ? 'SECURITY_USER_ADD_OK_ENABLED' : 'SECURITY_USER_ADD_OK_DISABLED';
					$this->msg->add('info', $this->lang->line($message));
				} else {
					$this->msg->add('err', sprintf($this->lang->line('SECURITY_USER_ADD_FAIL', $this->security->lasterr)));
				}
				
			} else {
				
				// Updating existing user
				$edit = $this->security->edit_user($user_id, $data);
				if($edit == TRUE){
					$message = ($data['enabled'] == 1) ? 'SECURITY_USER_EDIT_OK_ENABLED' : 'SECURITY_USER_EDIT_OK_DISABLED';
					$this->msg->add('info', $this->lang->line($message));
				} else {
					$this->msg->add('err', sprintf($this->lang->line('SECURITY_USER_EDIT_FAIL', $this->security->lasterr)));
				}
				
			}
			
			// All done, redirect!
			redirect('security/users');
			
		}
		
	}
	
	
	
	
	/**
	 * User import main function
	 */
	function import($stage = 0){
		
		// Find the stage from the post vars
		//$poststage = $this->input->post('stage');
		//echo $poststage;
		
		if($stage == 0){
			
			$this->auth->check('users.add');
			
			$links[] = array('security/users/import', 'Start import again');
			$tpl['links'] = $this->load->view('parts/linkbar', $links, TRUE);
			
			$body['groups'] = $this->security->get_groups_dropdown();
			$tpl['title'] = 'Import users';
			$tpl['pagetitle'] = 'Import users';
			
			$tpl['body'] = $this->lasterr;
			$tpl['body'] .= $this->load->view('security/users.import.1.php', $body, TRUE);
			$this->load->view($this->tpl, $tpl);
			
		} else {
			
			switch($stage){
				case 1:	$this->_import_1(); break;
				case 2: $this->_import_2(); break;
			}
			
		}
		
	}
	
	
	
	/**
	 * Import stage 1 - user has uploaded a file and hopefully set some default values
	 */
	function _import_1(){
		

		
		// Check where we are getting the CSV data from
		if($this->input->post('stage') == 1){
			
			// Do upload if it was submitted
			$config['upload_path'] = 'temp';
			$config['allowed_types'] = 'csv|txt';
			$config['encrypt_name'] = TRUE;
			$this->load->library('upload', $config);
			
			$upload = $this->upload->do_upload();
			
			// Get default values
			$defaults['password'] = $this->input->post('default_password');
			$defaults['group_id'] = $this->input->post('default_group_id');
			$defaults['enabled'] = ($this->input->post('default_enabled') == '1') ? 1 : 0;
			$defaults['emaildomain'] = str_replace('@', '', $this->input->post('default_emaildomain'));
			
			// Store defaults in session to retrieve later
			$this->session->set_userdata('importdef', $defaults);
			
		} elseif(is_array($this->session->userdata('csvimport'))){
			
			// Otherwise fetch CSV data from 
			$upload = TRUE;
			$csv = $this->session->userdata('csvimport');
			
		} else {
			
			$this->lasterr = $this->msg->err('Expected CSV data via form upload or session, but none was found');
			return $this->import(0);
			
		}
		
		// Test for valid data
		if($upload == FALSE){
			
			// Upload failed
			$this->lasterr = $this->msg->err(strip_tags($this->upload->display_errors()), 'File upload error');
			return $this->import(0);
			
		} else {
		
			
			// Elements to show on the page
			$links[] = array('security/users/import', 'Start import again');
			$tpl['links'] = $this->load->view('parts/linkbar', $links, TRUE);
			
			// Obtain the CSV data either from upload, or from session (if returning to here from an error)
			$csv = (!isset($csv)) ? $this->upload->data() : $csv;
			
			// $csv now contains the array of the file upload info
			
			// Store it in session for use later
			$this->session->set_userdata('csvimport', $csv);
			
			// Open the CSV file for reading
			$fhandle = fopen($csv['full_path'], 'r');
			
			if($fhandle == FALSE){
				// Check we can actually open the file
				$this->lasterr = $this->msg->err("Could not open uploaded file {$csv['full_path']}.");
				return $this->import(0);
			}
			
			#$fread = fread($fhandle, filesize($csv['full_path']));
			
			// Supply the CSV details to the view so it can open and then parse it
			$body['csv'] = $csv;
			$body['fhandle'] = $fhandle;
			
			#$body['csvdata'] = fgetcsv($fhandle, filesize($csv['full_path']), ',');
			
			// Load page
			$tpl['title'] = 'Import users';
			$tpl['pagetitle'] = "Import users (stage 2) - {$csv['orig_name']}";
			$tpl['body'] = $this->lasterr;
			$tpl['body'] .= $this->load->view('security/users.import.2.php', $body, TRUE);
			$this->load->view($this->tpl, $tpl);
			
		}
		
	}
	
	
	
	
	/**
	 * Stage 2 of the import process
	 */
	function _import_2(){
		
		$col = $this->input->post('col');
		$col_num = $col;
		$col = array_flip($col);
		$rows = $this->input->post('row');
		
		#die(print_r($col));
		
		$groups_id = $this->security->get_groups_dropdown();
		$groups_name = array_flip($groups_id);
		
		#print_r($col_num);
		
		$csv = $this->session->userdata('csvimport');
		
		$defaults = $this->session->userdata('importdef');
		
		#print_r($csv);
		#print_r($defaults);
		
		// No username column chosen? Can't continue
		if(!isset($col['username'])){
			$this->lasterr = $this->msg->err('You have not chosen a column that contains the username.', 'Required column not selected');
			return $this->import(1);
		}
		
		#print_r($defaults);
		
		// No default password or no password column?
		#echo (int) array_key_exists('password', $col);
		#echo (int) empty($defaults['password']);
		
		/*if( (!in_array('password', $col_num)) or (empty($defaults['password'])) ){
			$this->lasterr = $this->msg->err('You have not chosen a password column or set the default password on the previous page.', 'Required column not selected');
			return $this->import(1);
		}*/
		
		$pass_col = in_array('password', $col_num);
		$pass_def = !empty($defaults['password']);
		
		#echo var_dump($pass_col, $pass_def);
		
		if($pass_col == FALSE && $pass_def == FALSE){
			$this->lasterr = $this->msg->err('You have not chosen a password column or set a default password on the previous page.');
			return $this->import(1);
		}
		
		$users = array();
		
		// Go through each row, and try to get proper user details from it
		foreach($rows as $row){
			
			$user = array();
			
			// USERNAME
			$user['username'] = trim($row[$col['username']]);
			
			// PASSWORD
			$user['password'] = $defaults['password'];
			if(array_key_exists('password', $col)){
				if(!empty($row[$col['password']])){
					$user['password'] = trim($row[$col['password']]);
				}
			}
			
			// DISPLAY
			$user['display'] = $user['username'];
			if(array_key_exists('display', $col)){
				if(!empty($row[$col['display']])){
					$user['display'] = trim($row[$col['display']]);
				}
			}
			
			// EMAIL
			$user['email'] = '';
			if(!empty($defaults['emaildomain'])){
				$user['email'] = sprintf('%s@%s', $user['username'], $defaults['emaildomain']);
			}
			if(array_key_exists('email', $col)){
				$email = trim($row[$col['email']]);
				if(!empty($email) && $this->form_validation->valid_email($email)){
					$user['email'] = $email;
				}
			}
			
			// GROUP
			$user['group_id'] = $defaults['group_id'];
			if(array_key_exists('groupname', $col)){
				$groupname = trim($row[$col['groupname']]);
				if(array_key_exists($groupname, $groups)){
					$user['group_id'] = $groups_name[$groupname];
				}
			}
			
			// Enabled or not?
			$user['enabled'] = ($defaults['enabled'] == 1) ? 1 : 0;
			
			// Finally add this user to the big list if we should import them
			if(isset($row['import']) && $row['import'] == 1 && !empty($user['username'])){
				array_push($users, $user);
			}
			unset($user);
			
		}
		
		$body['users'] = $users;
		$body['groups'] = $groups_id;
		
		$this->session->set_userdata('users', $users);
		
		// Load page
		$links[] = array('security/users/import', 'Start import again');
		$tpl['links'] = $this->load->view('parts/linkbar', $links, TRUE);
		$tpl['title'] = 'Import users';
		$tpl['pagetitle'] = "Import users (stage 3) - {$csv['orig_name']}";
		$tpl['body'] = $this->lasterr;
		$tpl['body'] .= $this->load->view('security/users.import.3.php', $body, TRUE);
		$this->load->view($this->tpl, $tpl);
		
	}
	
	
	
	
	function delete($user_id = NULL){
		$this->auth->check('users.delete');
		
		// Check if a form has been submitted; if not - show it to ask user confirmation
		if($this->input->post('id')){
		
			// Form has been submitted (so the POST value exists)
			// Call model function to delete user
			$delete = $this->security->delete_user($this->input->post('id'));
			if($delete == FALSE){
				$this->msg->add('err', $this->security->lasterr, 'An error occured');
			} else {
				$this->msg->add('info', 'The user has been deleted.');
			}
			// Redirect
			redirect('security/users');
			
		} else {
		
			// Are we trying to delete ourself?
			if( ($this->session->userdata('user_id')) && ($user_id == $this->session->userdata('user_id')) ){
				$this->msg->add(
					'warn',
					base64_decode('WW91IGNhbm5vdCBkZWxldGUgeW91cnNlbGYsIHRoZSB1bml2ZXJzZSB3aWxsIGltcGxvZGUu'),
					base64_decode('RXJyb3IgSUQjMTBU')
				);
				redirect('security/users');
			}
			
			if($user_id == NULL){
				
				$tpl['title'] = 'Delete user';
				$tpl['pagetitle'] = $tpl['title'];
				$tpl['body'] = $this->msg->err('Cannot find the user or no user ID given.');
				
			} else {
				
				// Get user info so we can present the confirmation page with a dsplayname/username
				$user = $this->security->get_user($user_id);
				
				if($user == FALSE){
				
					$tpl['title'] = 'Delete user';
					$tpl['pagetitle'] = $tpl['title'];
					$tpl['body'] = $this->msg->err('Could not find that user or no user ID given.');
					
				} else {
					
					// Initialise page
					$body['action'] = 'security/users/delete';
					$body['id'] = $user_id;
					$body['cancel'] = 'security/users';
					$body['text'] = 'If you delete this user, all of their bookings and room owenership information will also be deleted.';
					$tpl['title'] = 'Delete user';
					$tpl['pagetitle'] = 'Delete ' . $user->display2;
					$tpl['body'] = $this->load->view('parts/deleteconfirm', $body, TRUE);
					
				}
				
			}
			
			$this->load->view($this->tpl, $tpl);
			
		}
		
	}
	
	
	
	
}


/* End of file app/controllers/security/users.php */