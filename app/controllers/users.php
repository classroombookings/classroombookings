<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Classroombookings. Hassle-free resource booking for schools. <http://classroombookings.com/>
 * Copyright (C) 2006-2011 Craig A Rodway <craig.rodway@gmail.com>
 *
 * This file is part of Classroombookings.
 * Classroombookings is licensed under the Affero GNU GPLv3 license.
 * Please see license-classroombookings.txt for the full license text.
 */

class Users extends Configure_Controller
{
	
	
	function __construct()
	{
		parent::__construct();
		$this->load->model('security_model');
		$this->load->model('departments_model');
		$this->load->model('quota_model');
		$this->lasterr = false;
	}
	
	
	
	
	/**
	 * PAGE: Main user list.
	 *
	 * Is also used by ingroup() and p()
	 */
	function index()
	{
		if ($this->uri->segment(3) == 'ingroup')
		{
			$group_id = (int) $this->uri->segment(4);
		}
		else
		{
			$group_id = NULL;
		}
		
		// Check authorisation
		$this->auth->check('users');
		
		if ($group_id == null)
		{
			// ALL users
			$body['groups'] = $this->security_model->get_groups_dropdown();
			$body['users'] = $this->security_model->get_user(NULL, NULL);
			$data['title'] = 'Users';
		}
		else
		{
			// Users in one group
			$groupname = $this->security->get_group_name($group_id);
			$body['users'] = $this->security_model->get_user(NULL, $group_id);
			$data['title'] = sprintf('Users in the %s group', $groupname);
		}
		
		// Get list of users
		if ($body['users'] == false)
		{
			$data['body'] = $this->msg->err($this->security->lasterr);
		}
		else
		{
			$data['body'] = $this->load->view('users/index', $body, true);
		}
		$data['js'] = array('js/crbs.users.js');
		$data['submenu'] = $this->menu_model->users();
		$this->page($data);
	}
	
	
	
	
	/**
	 * PAGE: Users in a group
	 */
	function ingroup($group_id)
	{
		$this->index($group_id);
	}
	
	
	
	
	/**
	 * PAGE: Add a user
	 */
	function add()
	{
		$this->auth->check('users.add');
		$body['user'] = null;
		$body['user_id'] = null;
		$body['groups'] = $this->security_model->get_groups_dropdown();
		$body['departments'] = $this->departments_model->get_dropdown();
		$data['title'] = 'Add new user';
		$data['body'] = $this->load->view('users/addedit', $body, true);
		$this->page($data);
	}
	
	
	
	
	/**
	 * PAGE: Edit a user
	 */
	function edit($user_id)
	{
		$this->auth->check('users.edit');
		$body['user'] = $this->security_model->get_user($user_id);
		$body['user_id'] = $user_id;
		$body['groups'] = $this->security_model->get_groups_dropdown();
		$body['departments'] = $this->departments_model->get_dropdown();
		
		$data['title'] = 'Edit user';
		
		if ($body['user'])
		{
			$data['title'] = 'Edit ' . $body['user']->displayname;
			$data['body'] = $this->load->view('users/addedit', $body, true);
		}
		else
		{
			$data['title'] = 'Error loading user';
			$data['body'] = $this->msg->err($this->security_model->lasterr);
		}
		$this->page($data);
	}
	
	
	
	
	/**
	 * FORM POST: Destination for form submission for add/edit pages
	 */
	function save()
	{
		$user_id = $this->input->post('user_id');
		
		$this->form_validation->set_rules('user_id', 'User ID');
		if (!$user_id)
		{
			$this->form_validation->set_rules('password1', 'Password', 'max_length[104]|required');
			$this->form_validation->set_rules('password2', 'Password (confirmation)', 'max_length[104]|required|matches[password1]');
			$username_rules = 'required|min_length[1]|max_length[64]|trim';
		}
		if ($user_id)
		{
			$username_rules = 'required|min_length[1]|max_length[64]|trim|callback__check_username';
		}
		$this->form_validation->set_rules('group_id', 'Group', 'required|integer');
		$this->form_validation->set_rules('enabled', 'Enabled', 'exact_length[1]');
		$this->form_validation->set_rules('email', 'Email address', 'max_length[256]|valid_email|trim');
		$this->form_validation->set_rules('displayname', 'Display name', 'max_length[64]|trim');
		$this->form_validation->set_rules('username', 'Username', $username_rules);
		$this->form_validation->set_error_delimiters('<li>', '</li>');
		
		if ($this->form_validation->run() == false)
		{
			// Validation failed - load required action depending on the state of user_id
			($user_id == NULL) ? $this->add() : $this->edit($user_id);
		}
		else
		{
			// Validation OK
			$data['username'] = $this->input->post('username');
			$data['displayname'] = $this->input->post('displayname');
			$data['email'] = $this->input->post('email');
			$data['group_id'] = $this->input->post('group_id');
			$data['departments'] = $this->input->post('departments');
			$data['enabled'] = $this->input->post('enabled');
			$data['ldap'] = $this->input->post('ldap');
			// Only set password if supplied.
			if ($this->input->post('password1'))
			{
				$data['password'] = $this->input->post('password1');
			}
			
			#die(print_r($data));
			
			if ($user_id == null)
			{
				$add = $this->security_model->add_user($data);
				if ($add == null)
				{
					$this->msg->add('err', sprintf(lang('SECURITY_USER_ADD_FAIL'),
						$this->security_model->lasterr));
				}
				else
				{
					// Set quota if supplied
					if ($this->input->post('quota'))
					{
						$this->quota->set_quota_u($add, $this->input->post('quota'));
					}
					$message = ($data['enabled'] == 1) 
						? 'SECURITY_USER_ADD_OK_ENABLED' 
						: 'SECURITY_USER_ADD_OK_DISABLED';
					$this->msg->add('notice', lang($message));
				}
				
			}
			else
			{
				// Updating existing user
				$edit = $this->security_model->edit_user($user_id, $data);
				if ($edit == TRUE)
				{
					// Update quota if needed
					if ($this->input->post('quota'))
					{
						$this->quota->set_quota_u($user_id, $this->input->post('quota'));
					}
					$message = ($data['enabled'] == 1) 
						? 'SECURITY_USER_EDIT_OK_ENABLED' 
						: 'SECURITY_USER_EDIT_OK_DISABLED';
					$this->msg->add('notice', lang($message));
				}
				else
				{
					$this->msg->add('err', sprintf(lang('SECURITY_USER_EDIT_FAIL'),
						$this->security->lasterr));
				}
			}
			// All done, redirect!
			redirect('users');
		}
	}
	
	
	
	
	/**
	 * PAGE: User import landing page
	 */
	function import($step = 0)
	{
		$this->auth->check('users.add');
		
		if ($step == 'cancel')
		{
			$this->session->unset_userdata('csvimport');
			$this->session->unset_userdata('importdef');
			$this->session->unset_userdata('users');
		}
		
		if ($step == 0)
		{
			$body['groups'] = $this->security_model->get_groups_dropdown();
			$body['departments'] = $this->departments_model->get_dropdown();
			$body['lasterr'] = (isset($this->lasterr)) ? $this->lasterr : '';
			$data['title'] = 'Import users - Step 1';
			$data['body'] = $this->load->view('users/import-1', $body, true);
			$this->page($data);
		}
		else
		{
			switch ($step)
			{
				case 1:	return $this->_import_1(); break;
				case 2: return $this->_import_2(); break;
				case 3: return $this->_import_3(); break;
			}
		}
	}
	
	
	
	
	/**
	 * User Import: step 1 - user has uploaded a file and hopefully set some default values
	 */
	function _import_1()
	{
		
		// Check where we are getting the CSV data from
		
		if ($this->input->post('step') == 1)
		{
			// Do upload if it was submitted
			$config['upload_path'] = 'temp';
			$config['allowed_types'] = 'csv|txt';
			$config['encrypt_name'] = true;
			$this->load->library('upload', $config);
			
			$upload = $this->upload->do_upload();
			
			// Get default values
			$defaults['password'] = $this->input->post('default_password');
			$defaults['group_id'] = $this->input->post('default_group_id');
			$defaults['departments'] = $this->input->post('default_departments');
			$defaults['enabled'] = (int) $this->input->post('default_enabled');
			$defaults['emaildomain'] = str_replace('@', '', $this->input->post('default_emaildomain'));
			
			// Store defaults in session to retrieve later
			$this->session->set_userdata('importdef', $defaults);
		}
		elseif (is_array($this->session->userdata('csvimport')))
		{
			// Otherwise fetch CSV data from 
			$upload = true;
			$csv = $this->session->userdata('csvimport');
		}
		else
		{
			$this->lasterr = $this->msg->err('Expected CSV data via form upload or session, but none was found');
			return $this->import(0);
		}
		
		// Test for valid data
		if ($upload == false)
		{
			// Upload failed
			$this->lasterr = $this->msg->err(strip_tags($this->upload->display_errors()), 'File upload error');
			return $this->import(0);
		}
		else
		{
			// Obtain the CSV data either from upload, or from session (if returning to here from an error)
			$csv = (!isset($csv)) ? $this->upload->data() : $csv;
			
			// $csv now contains the array of the file upload info
			
			// Store it in session for use later
			$this->session->set_userdata('csvimport', $csv);
			
			// Open the CSV file for reading
			$fhandle = fopen($csv['full_path'], 'r');
			
			if ($fhandle == FALSE)
			{
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
			$data['title'] = 'Import users';
			$body['lasterr'] = (isset($this->lasterr)) ? $this->lasterr : '';
			$data['body'] = $this->load->view('users/import-2', $body, true);
			$this->page($data);
		}
	}
	
	
	
	
	/**
	 * User import: Stage 2 - preview the user page
	 */
	function _import_2()
	{
		$col = $this->input->post('col');
		$col_num = $col;
		$col = array_flip($col);
		$rows = $this->input->post('row');
		
		$groups_id = $this->security_model->get_groups_dropdown();
		$groups_name = array_flip($groups_id);
		
		$csv = $this->session->userdata('csvimport');
		
		$defaults = $this->session->userdata('importdef');
		
		// No username column chosen? Can't continue
		if (!isset($col['username']))
		{
			$this->lasterr = $this->msg->err('You have not chosen a column that contains the username.', 'Required column not selected');
			return $this->import(1);
		}
		
		// Check for password in column or default
		$pass_col = in_array('password', $col_num);
		$pass_def = !empty($defaults['password']);
		
		if ($pass_col == false && $pass_def == false)
		{
			$this->lasterr = $this->msg->err('You have not chosen a password column or set a default password on the previous page.');
			return $this->import(1);
		}
		
		// Check if any users were selected
		if (empty($rows))
		{
			$this->lasterr = $this->msg->err('You must choose at least one user to import.');
			return $this->import(1);
		}
		
		$users = array();
		
		// Go through each row, and try to get proper user details from it
		foreach ($rows as $row)
		{
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
			
			// DEPARTMENTS
			if (!empty($defaults['departments']))
			{
				$user['departments'] = $defaults['departments'];
			}
			
			// Enabled or not?
			$user['enabled'] = $defaults['enabled'];
			
			// Finally add this user to the big list if we should import them
			if(isset($row['import']) 
				&& $row['import'] == 1 
				&& !empty($user['username']))
			{
				array_push($users, $user);
			}
			unset($user);
		}
		
		$body['users'] = $users;
		$body['groups'] = $groups_id;
		$body['departments'] = $this->departments_model->get_dropdown();
		
		$this->session->set_userdata('users', $users);
		
		$body['lasterr'] = (isset($this->lasterr)) ? $this->lasterr : '';
		
		// Load page
		$data['title'] = 'Import users';
		$data['body'] = $this->load->view('users/import-3', $body, true);
		$this->page($data);
		
	}
	
	
	
	
	/**
	 * User Import: Stage 3 - add the actual users and show success/failures
	 */
	function _import_3()
	{
		// Get array of users to add from the session (stored in previous stage)
		$users = $this->session->userdata('users');
		// Get CSV data
		$csv = $this->session->userdata('csvimport');
		
		if(count($users) > 0)
		{
			// Arrays to hold details of successes and failures
			$fail = array();
			$success = array();
			
			// Loop through all users and add them to the database
			foreach($users as $user)
			{
				// Create array of fields to be sent to the database
				$data = array();
				$data['username'] = $user['username'];
				$data['displayname'] = $user['display'];
				$data['email'] = $user['email'];
				$data['group_id'] = $user['group_id'];
				$data['enabled'] = $user['enabled'];
				$data['password'] = $user['password'];
				$data['departments'] = $user['departments'];
				$data['ldap'] = 0;
				
				// Add user to database
				$add = $this->security_model->add_user($data);
				
				// Test result of the add
				if ($add == false)
				{
					$user['fail'] = $this->security_model->lasterr;
					array_push($fail, $user);
				}
				else
				{
					array_push($success, $user);
				}
				unset($data);
			}
			
			// Finished adding users
			
			$body['fail'] = $fail;
			$body['success'] = $success;
			
			// Remove session data
			$this->session->unset_userdata(array('csvimport', 'users'));
			
			// Load page
			$data['title'] = 'Import users';
			$data['body'] = $this->load->view('users/import-4', $body, true);
			$this->page($data);
			
		}
		else
		{
			// No users - weird - shouldn't get to this stage without them.
			$this->lasterr = $this->msg->err('No users were supplied');
			return $this->import(2);
		}
	}
	
	
	
	
	/**
	 * PAGE: Deleting a user
	 */
	function delete($user_id = null)
	{
		$this->auth->check('users.delete');
		
		// Check if a form has been submitted; if not - show it to ask user confirmation
		if ($this->input->post('id'))
		{
			// Form has been submitted (so the POST value exists)
			// Call model function to delete user
			$delete = $this->security_model->delete_user($this->input->post('id'));
			if ($delete == false)
			{
				$this->msg->add('err', $this->security_model->lasterr, 'An error occured');
			}
			else
			{
				$this->msg->add('notice', 'The user has been deleted.');
			}
			// Redirect
			redirect('users');
		}
		else
		{
			// Are we trying to delete ourself?
			if ( ($this->session->userdata('user_id')) && ($user_id == $this->session->userdata('user_id')) )
			{
				$this->msg->add(
					'err',
					base64_decode('WW91IGNhbm5vdCBkZWxldGUgeW91cnNlbGYsIHRoZSB1bml2ZXJzZSB3aWxsIGltcGxvZGUu'),
					base64_decode('RXJyb3IgSUQjMTBU')
				);
				redirect('users');
			}
			
			// Deleting the annymous user?
			$anon = $this->settings->get('auth_anonuserid');
			if ($user_id == $anon)
			{
				$this->msg->add('err', 'Cannot delete the anonymous user.');
				redirect('users');
			}
			
			if ($user_id == null)
			{
				$data['title'] = 'Delete user';
				$data['body'] = $this->msg->err('Cannot find the user or no user ID given.');
			}
			else
			{
				// Get user info so we can present the confirmation page with a dsplayname/username
				$user = $this->security_model->get_user($user_id);
				if ($user == false)
				{
					$data['title'] = 'Delete user';
					$data['body'] = $this->msg->err('Could not find that user or no user ID given.');
				}
				else
				{
					// Initialise page
					$body['action'] = 'users/delete';
					$body['id'] = $user_id;
					$body['cancel'] = 'users';
					$body['text'] = 'If you delete this user, all of their bookings and room owenership information will also be deleted.';
					$body['title'] = 'Are you sure you want to delete user ' . $user->username . '?';
					$data['title'] = 'Delete ' . $user->displayname;
					$data['body'] = $this->load->view('parts/deleteconfirm', $body, true);
				}	// if user == false-else
			}	// if user_id == null-else
			
			$this->page($data);
			
		}	// if post(id) else
		
	}	// endfunction
	
	
	
	
	
	/**
	 * Validation function.
	 *
	 * When renaming a user, check if new name doesn't already exist
	 */
	function  _check_username($new_username)
	{
		$old_username = $this->input->post('old_username');
		if ($new_username == $old_username)
		{
			return true;
		}
		
		if ($this->auth->userexists($new_username))
		{
			$this->form_validation->set_message('_check_username',
				"The username '$new_username' already exists.");
			return false;
		}
	}
	
	
	
	
}


/* End of file app/controllers/security/users.php */