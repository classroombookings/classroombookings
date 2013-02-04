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
		$filter['pp'] = element('pp', $filter, 10);
		
		$this->load->library('pagination');
		$config = array(
			'base_url' => site_url('users/index'),
			'total_rows' => $this->users_model->count_all(),
			'per_page' => $filter['pp'],
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
	
	
	
	
	// =======================================================================
	// User import
	// =======================================================================
	
	
	
	
	/**
	 * PAGE: User import landing page
	 */
	function import($step = 1)
	{
		$this->auth->restrict('users.import');
		
		$this->data['groups'] = $this->groups_model->dropdown('g_name');
		$this->data['departments'] = $this->departments_model->dropdown('d_name');
		
		$this->data['destination_fields'] = array(
			'' => lang('users_import_header_ignore'),
			'u_username' => lang('users_username'),
			'u_email' => lang('users_email'),
			'u_display' => lang('users_display'),
			'u_password' => lang('password'),
			'u_enabled' => lang('users_import_header_enabled'),
			'u_g_id' => lang('users_group'),
			'd_id' => lang('users_import_department'),
		);
		
		$this->data['import'] = $this->session->userdata('import');
		
		// No import data and step isn't 1 means that data is expected but not present. GOTO start.
		if (empty($this->data['import']) && $step != 1)
		{
			$step = 1;
		}
		
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
			case 4: return $this->_import_4(); break;
			case 'cancel': return $this->_import_cancel(); break;
			case 'finish': return $this->_import_finish(); break;
		}
	}
	
	
	
	
	/**
	 * User Import: Step 1: File upload and default values
	 */
	private function _import_1()
	{
		$this->layout->add_breadcrumb(lang('step') . ' 1', 'users/import/1');
		
		if ($this->input->post())
		{
			$upload_config = array(
				'upload_path' => APPPATH . '/uploads',
				'allowed_types' => 'csv|txt|tsv',
				'encrypt_name' => TRUE,
			);
			
			$this->load->library('upload', $upload_config);
			
			$upload = $this->upload->do_upload();
			
			if ( ! $upload)
			{
				// Fail
				$this->flash->set('error', strip_tags($this->upload->display_errors()));
				return;
			}
			
			// OK. Upload data
			$upload_data = $this->upload->data();
			
			// All import data that will be stored in the session between steps
			$import_data = array(
				'file_name' => $upload_data['file_name'],
				'file_path' => $upload_data['full_path'],
				'existing' => $this->input->post('existing'),
				'password' => $this->input->post('password'),
				'g_id' => $this->input->post('g_id'),
				'd_id' => $this->input->post('d_id'),
				'email_domain' => str_replace('@', '', $this->input->post('email_domain')),
				'u_enabled' => $this->input->post('u_enabled'),
				'step' => '2',
			);
			
			// Store this info in the session for retrieval in next stages
			$this->session->set_userdata('import', $import_data);
			
			// ALL DONE!!!
			
			// Go to next step!
			redirect('users/import/2');
		}
	}
	
	
	
	
	/**
	 * User Import: Step 2: Review columns
	 */
	private function _import_2()
	{
		$this->layout->add_breadcrumb(lang('step') . ' 2', 'users/import/2');
		
		$this->load->library('csv_data');
		
		$this->csv_data->load($this->data['import']['file_path']);
		
		if ( ! $this->csv_data->countRows() > 1)
		{
			$this->flash->set('error', lang('users_import_insufficient_rows'));
			return FALSE;
		}
		
		// Check the data is symmetric (data columns match headers)
		if ( ! $this->csv_data->isSymmetric())
		{
			$this->csv_data->symmetrize();
		}
		
		// Get the CSV headers
		$this->data['headers'] = $this->csv_data->getHeaders();
		
		if ($this->input->post())
		{
			// Get the matched fields but store them the other way round, with our data as keys and header index as value
			$fields = $this->input->post('fields');
			$fields = array_filter($fields, 'strlen');
			$fields = array_flip($fields);
			
			// Iterate through the fields and update the CSV header value to use the name instead of the index
			foreach ($fields as $crbs => &$csv_index)
			{
				$csv_index = $this->data['headers'][$csv_index];
			}
			
			// Check that some of the required fields are valid
			
			if ( ! array_key_exists('u_username', $fields))
			{
				// No username field chosen
				$this->flash->set('error', lang('users_import_no_username_field'));
				return FALSE;
			}
			
			if ( ! array_key_exists('u_password', $fields) && empty($this->data['import']['password']))
			{
				// No password field, and the default password is empty
				$this->flash->set('error', lang('users_import_no_password_field'));
				return FALSE;
			}
			
			if ( ! array_key_exists('u_email', $fields) && empty($this->data['import']['email_domain']))
			{
				// No email field and the default email domain is empty
				$this->flash->set('error', lang('users_import_no_email'));
				return FALSE;
			}
			
			// Update session data
			$this->data['import']['fields'] = $fields;
			$this->data['import']['step'] = '3';
			$this->session->set_userdata('import', $this->data['import']);
			
			// Go to next step!
			redirect('users/import/3');
		}
	}
	
	
	
	
	/**
	 * User Import: Step 3: Preview data
	 */
	private function _import_3()
	{
		$this->layout->add_breadcrumb(lang('step') . ' 3', 'users/import/3');
		
		$this->load->library('csv_data');
		
		$this->csv_data->load($this->data['import']['file_path']);
		
		// Check the data is symmetric (data columns match headers)
		if ( ! $this->csv_data->isSymmetric())
		{
			$this->csv_data->symmetrize();
		}
		
		// Get the CSV headers
		$this->data['headers'] = $this->csv_data->getHeaders();
		
		if ($this->input->post())
		{
			$users = $this->input->post('users');
			
			if (empty($users))
			{
				$this->flash->set('error', lang('users_import_none_selected'));
				return FALSE;
			}
			
			// Do the import!
			$result = $this->users_model->import($users, $this->data['import']['existing']);
			
			// Update session data
			$this->data['import']['result'] = $result;
			$this->data['import']['step'] = '4';
			$this->session->set_userdata('import', $this->data['import']);
			
			// Go to next step!
			redirect('users/import/4');
		}
		
		/**
		 * > Iterate through the CSV data rows
		 * > Build up array of users using our columns as keys
		 * > Get the value to use for each column from CSV or use default
		 */
		
		$rows = $this->csv_data->connect();
		$users = array();
		
		$fields = $this->data['import']['fields'];
		
		// Field names to use from CSV, based on matched fields
		$username_field = element('u_username', $fields);
		$email_field = element('u_email', $fields);
		$display_field = element('u_display', $fields);
		$password_field = element('u_password', $fields);
		$enabled_field = element('u_enabled', $fields);
		$group_field = element('u_g_id', $fields);
		$department_field = element('d_id', $fields);
		
		$groups = array_change_key_case(array_flip($this->data['groups']), CASE_LOWER);
		
		// Loop through the CSV data rows to get our users
			
		foreach ($rows as $row)
		{
			// Create initial dataset based on defaults
			$user = array(
				'u_password' => $this->data['import']['password'],
				'u_g_id' => $this->data['import']['g_id'],
				'u_enabled' => $this->data['import']['u_enabled'],
				'd_id' => $this->data['import']['d_id'],
			);
			
			// Gather data from other fields if they're set
			
			if ($username_field)
			{
				$user['u_username'] = strtolower(element($username_field, $row));
			}
			
			if ($password_field)
			{
				$user['u_password'] = element($password_field, $row, $user['u_password']);
			}
			
			if ($email_field)
			{
				$user['u_email'] = trim(element($email_field, $row, $user['u_username'] . '@' . $this->data['import']['email_domain']));
			}
			else
			{
				$user['u_email'] = trim($user['u_username'] . '@' . $this->data['import']['email_domain']);
			}
			
			if ($enabled_field)
			{
				$user['u_enabled'] = filter_var(element($enabled_field, $row, $user['u_enabled']), FILTER_VALIDATE_BOOLEAN);
			}
			
			if ($display_field)
			{
				$user['u_display'] = element($display_field, $row, $user['u_username']);
			}
			
			if ($group_field)
			{
				$group = element($group_field, $row, FALSE);
				
				if (is_numeric($group) && element($group, $this->data['groups']))
				{
					// Supplied value for group is an ID.
					$user['u_g_id'] = $group;
				}
				else
				{
					$group = strtolower($group);
					if (array_key_exists($group, $groups))
					{
						// Supplied group is a name matching existing group
						$user['u_g_id'] = $groups[$group];
					}
				}
			}
			
			if ($department_field)
			{
				$department = element($department_field, $row, FALSE);
				
				if (is_numeric($department) && element($department, $this->data['departments']))
				{
					// Supplied value for department is a valid ID
					$user['d_id'] = $department;
				}
				else
				{
					$department = strtolower($department);
					if (array_key_exists($department, $departments))
					{
						// Supplied department is a name matching existing department
						$user['d_id'] = $departments[$department];
					}
				}
			}
			
			$users[] = $user;
		}
		
		$this->data['users'] = $users;
		
	}
	
	
	
	
	/**
	 * User Import: Step 4: Import Results
	 */
	private function _import_4()
	{
		$this->layout->add_breadcrumb(lang('step') . ' 4', 'users/import/4');
	}
	
	
	
	
	/**
	 * Cancel the import process. Clear the session data and go to import step 1
	 */
	private function _import_cancel()
	{
		$this->session->set_userdata('import', array());
		redirect('users/import');
	}
	
	
	
	
	/**
	 * Complete the import process. Clear the session data and go to the users index
	 */
	private function _import_finish()
	{
		$this->session->set_userdata('import', array());
		redirect('users');
	}
	
	
	
	
	// =======================================================================
	// Other
	// =======================================================================
	
	
	
	
	/**
	 * Delete a user account
	 */
	function delete()
	{
		$this->auth->restrict('users.delete');
		
		$id = $this->input->post('id');
		
		if ( ! $id)
		{
			redirect('users/index');
		}
		
		if ($this->users_model->delete($id))
		{
			$this->flash->set('success', lang('users_delete_success'), TRUE);
		}
		else
		{
			$this->flash->set('error', lang('users_delete_error'), TRUE);
		}
		
		redirect($this->input->post('redirect'));
	}
	
	
	
	
}

/* End of file ./application/controllers/users.php */