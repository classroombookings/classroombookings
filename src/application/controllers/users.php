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
		
		$this->lang->load('configure');
		$this->lang->load('users');
		$this->load->model(array('users_model', 'groups_model', 'departments_model', 'quota_model'));
		$this->load->helper('user_helper');
		$this->data['nav_current'][] = 'users';
		
		$this->layout->add_breadcrumb(lang('configure_users'), 'users');
		
		$this->data['subnav'] = array(
			array(
				'uri' => 'users',
				'text' => lang('configure_users'),
				'test' => $this->auth->check('users.view'),
			),
			array(
				'uri' => 'users/set',
				'text' => lang('users_add_new'),
				'test' => $this->auth->check('users.add'),
			),
			array(
				'uri' => 'users/import',
				'text' => lang('users_bulk_import'),
				'test' => $this->auth->check('users.import'),
			),
		);
		
		// Valid authentication methods for users
		$this->data['auth_methods'] = array(
			'local' => lang('users_auth_method_local'),
			'ldap' => lang('users_auth_method_ldap'),
		);
	}
	
	
	
	
	// =======================================================================
	// User management pages
	// =======================================================================
	
	
	
	
	function index($page = 0)
	{
		$this->auth->restrict('users.view');
		
		$filter = $this->input->get(NULL, TRUE);
		
		$this->load->library('pagination');
		$config = array(
			'base_url' => site_url('users/index'),
			'total_rows' => $this->users_model->count_all(),
			'per_page' => $this->input->get('pp'),
			'uri_segment' => 3,
		);
		$this->pagination->initialize($config);
		
		$this->users_model->set_filter($filter);
		$this->users_model->order_by('u_username', 'asc');
		$this->users_model->limit($config['per_page'], $page);
		
		$this->data['filter'] = $filter;
		$this->data['users'] = $this->users_model->get_all();
		$this->data['groups'] = $this->groups_model->dropdown('g_id', 'g_name');
		
		$this->layout->set_js('views/users/index');
		
		$this->layout->set_title(lang('configure_users'));
		$this->load->library('form');
		$this->data['subnav_active'] = 'users';
	}
	
	
	
	
	/**
	 * Add new or edit existing user
	 *
	 * @param int $u_id		ID of user to edit.
	 */
	public function set($u_id = 0)
	{
		// Initial validation rules
		$rules = array(
			'u_username' => '',
			'password1' => '',
			'password2' => '',
		);
		
		if ($u_id)
		{
			// Updating user $u_id
			$this->auth->restrict('users.edit');
			$this->data['user'] = $this->users_model->get($u_id);
			$title = lang('users_edit');
			$this->layout->add_breadcrumb(lang('users_edit'), 'users/set/' . $u_id);
			
			// Username field validation to check it's not already taken
			$rules['u_username'] = 'required|min_length[1]|max_length[104]|trim|valid_current_username';
			
			// Validate password fields only if new passowrd set
			if ($this->input->post('password1'))
			{
				$rules['password1'] = 'required|min_length[1]|max_length[100]';
				$rules['password2'] = 'required|matches[password1]';
			}
		}
		else
		{
			// Adding new user
			$this->auth->restrict('users.add');
			$title = lang('users_add_new');
			$this->layout->add_breadcrumb(lang('users_add_new'), 'users/set');
			$this->data['subnav_active'] = 'users/set';
			$this->data['user'] = array();
			
			// Validation rules specific for creating new accounts
			$rules['u_username'] = 'required|min_length[1]|max_length[104]|trim|valid_new_username';
			$rules['password1'] = 'required|min_length[1]|max_length[100]';
			$rules['password2'] = 'required|matches[password1]';
		}
		
		if ($this->input->post())
		{
			$this->form_validation->set_rules('u_username', lang('users_username'), $rules['u_username'])
								  ->set_rules('password1', lang('password'), $rules['password1'])
								  ->set_rules('password2', lang('password_confirm'), $rules['password2'])
								  ->set_rules('u_email', lang('users_email'), 'required|trim|max_length[255]|valid_email')
								  ->set_rules('u_display', lang('users_display'), 'trim|max_length[64]')
								  ->set_rules('u_enabled', lang('users_account_enables'), 'required|integer')
								  ->set_rules('u_auth_method', lang('users_auth_method'), 'required')
								  ->set_rules('u_g_id', lang('users_group'), 'required|integer');
			
			if ($this->form_validation->run())
			{
				$user_data = array(
					'u_username' => $this->input->post('u_username'),
					'u_email' => $this->input->post('u_email'),
					'u_display' => $this->input->post('u_display'),
					'u_enabled' => (int) $this->input->post('u_enabled'),
					'u_auth_method' => $this->input->post('u_auth_method'),
					'u_g_id' => (int) $this->input->post('u_g_id'),
				);
				
				if ($this->input->post('password1'))
				{
					$user_data['u_password'] = $this->auth->local->hash_password($this->input->post('password1'));
				}
				
				if ($u_id)
				{
					// Update
					$u_id = $this->users_model->update($u_id, $user_data);
					$success = lang('users_update_success');
					$error = lang('users_update_error');
				}
				else
				{
					// Insert
					$u_id = $this->users_model->insert($user_data);
					$success = lang('users_insert_success');
					$error = lang('users_insert_error');
				}
				
				if ($u_id)
				{
					// Success
					
					// Set department membership
					$this->users_model->set_user_departments($u_id, $this->input->post('departments'));
					
					$this->flash->set('success', $success, TRUE);
					redirect('users');
				}
				else
				{
					$this->flash->set('error', $error);
				}
			}
		}
		
		$this->data['groups'] = $this->groups_model->dropdown('g_name');
		$this->data['departments'] = $this->departments_model->dropdown('d_name');
		
		$this->layout->set_title($title);
		$this->load->library('form');
	}
	
	
	
	
	/**
	 * PAGE: User import landing page
	 */
	function import($step = 1)
	{
		$this->auth->restrict('users.import');
		
		$this->data['groups'] = $this->groups_model->dropdown('g_name');
		$this->data['departments'] = $this->departments_model->dropdown('d_name');
		
		$this->data['import'] = $this->session->userdata('import');
		
		$this->layout->add_breadcrumb(lang('import'), 'users/import');
		
		$this->layout->set_title(lang('users_bulk_import'));
		$this->load->library('form');
		$this->data['subnav_active'] = 'users/import';
		
		$this->auto_view = FALSE;
		$this->layout->set_view('content', 'default/users/import/step_' . $step);
		
		switch ($step)
		{
			case 1:	return $this->_import_1(); break;
			case 2: return $this->_import_2(); break;
			case 3: return $this->_import_3(); break;
			case 'cancel': return $this->_import_cancel(); break;
		}
	}
	
	
	
	
	/**
	 * User Import: Step 1: File upload and default values
	 */
	private function _import_1()
	{
		$this->layout->add_breadcrumb(lang('step') . '1', 'users/import/1');
		
		if ($this->input->post())
		{
			// @TODO Process file and store defaults chosen
			//die();
			
			// Go to next step!
			redirect('users/import/2');
		}
		
		/*
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
			//$this->lasterr = $this->msg->err('Expected CSV data via form upload or session, but none was found');
			return;
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
			fclose($fhandle);
		}
		*/
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
				$data['departments'] = (!empty($user['departments'])) ? $user['departments'] : array();
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
			
			// This will erase the temporary session data used during import
			Events::trigger('users.import.end');
			
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