<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Classroombookings. Hassle-free resource booking for schools. <http://classroombookings.com/>
 * Copyright (C) 2006-2011 Craig A Rodway <craig.rodway@gmail.com>
 *
 * This file is part of Classroombookings.
 * Classroombookings is licensed under the Affero GNU GPLv3 license.
 * Please see license-classroombookings.txt for the full license text.
 */

class Permissions extends Configure_Controller
{	


	function __construct()
	{
		parent::__construct();
		$this->load->model('security_model');
		$this->load->model('departments_model');
	}
	
	
	
	
	/**
	 * PAGE: Main roles & permission page
	 */
	function index()
	{
		$this->auth->check('permissions');
		
		$tabs = array();
		
		$roles_data['roles'] = $this->permissions_model->get_roles();
		$roles_data['weights']['max'] = $this->permissions_model->get_role_weight('max');
		$roles_data['weights']['min'] = $this->permissions_model->get_role_weight('min');
		
		$tabs[] = array(
			'id' => 'roles',
			'title' => 'Roles',
			'view' => $this->load->view('permissions/tab_roles_index', $roles_data, true),
		);
		
		$tabs[] = array(
			'id' => 'role_assignments',
			'title' => 'Role Assignments',
			'view' => ''
		);
		
		$tabs[] = array(
			'id' => 'permissions',
			'title' => 'Permissions',
			'view' => '',
		);
		
		$body['tabs'] = $tabs;
		$body['active_tab'] = 'roles';
		
		$data['title'] = 'Roles &amp; Permissions';
		$data['submenu'] = $this->menu_model->permissions();
		$data['body'] = $this->load->view('parts/tabs', $body, true);
		
		//$data['body'] .= $this->load->view('permissions/index', null, true);
		
		$data['js'] = array('js/tristate-checkbox.js');
		
		$this->page($data);
	}
	
	
	
	
	/*
	 * Roles
	 * =====
	 */
	
	
	
	
	/**
	 * PAGE: Add a new role
	 */
	function save_role()
	{
		$role_id = $this->input->post('role_id');

		$this->form_validation->set_rules('role_id', 'Role ID');
		$this->form_validation->set_rules('name', 'Name', 'required|max_length[20]|trim');
		$this->form_validation->set_rules('weight', 'Weight', 'numeric');
		$this->form_validation->set_error_delimiters('<li>', '</li>');

		if ($this->form_validation->run() == false)
		{
			// Validation failed - load required action depending on the state of user_id
			return $this->index();
		}
		else
		{
			// Validation OK
			$data['name'] = $this->input->post('name');

			if ($role_id == null)
			{
				$add = $this->permissions_model->add_role($data);
				if ($add == true)
				{
					$this->msg->add('info', 'The role has been added.');
				}
				else
				{
					$this->msg->add('err', 'Could not add the role: ' . $this->permissions_model->lasterr);
				}
			}
			else
			{
				// Updating existing role
				$edit = $this->permissions_model->edit_role($role_id, $data);
				if ($edit == true)
				{
					$this->msg->add('info', 'The role has been updated.');
				}
				else
				{
					$this->msg->add('err', 'Could not update the role: ' . $this->permissions_model->lasterr);
				}
			}

			// All done, redirect!
			redirect('permissions');

		}

	}
	
	
	
	
	/**
	 * PAGE: Add a new permission entry
	 */
	function add()
	{
		$this->auth->check('permissions');
		
		// Get lists of stuff we need
		$body['groups'] = $this->security_model->get_groups_dropdown();
		$body['departments'] = $this->departments_model->get_dropdown();
		$body['users'] = $this->security_model->get_users_dropdown();
		
		$body['permission_id'] = null;
		
		// List of all available permissions
		$body['available_permissions'] = $this->config->item('permissions');
		
		$data['js'] = array('js/tristate-checkbox.js');
		
		$data['title'] = 'Add permission entry';
		$data['body'] = $this->load->view('permissions/add', $body, true);
		$this->page($data);
	}
	
	
	
	
	/**
	 * Save the submitted permissions
	 */
	function save_one()
	{
		$this->auth->check('permissions');
		
		$this->output->enable_profiler(true);
		
		$entity_type = $this->input->post('entity_type');
		$permission_id = $this->input->post('permission_id');
		$entity_id = null;
		
		$this->form_validation->set_rules('permission_id', 'Permission ID');
		$this->form_validation->set_rules('entity_type', 'Entity type', 'exact_length[1]');
		$this->form_validation->set_rules('permissions[]', 'Permissions');
		
		$valid_id = 'required|integer|is_natural_no_zero';
		
		// Add a rule depending on chosen entity type
		switch($entity_type)
		{
			case 'D':
				$this->form_validation->set_rules('department_id', 'Department', $valid_id);
				$entity_id = $this->input->post('department_id');
				break;
			case 'G':
				$this->form_validation->set_rules('group_id', 'Group', $valid_id);
				$entity_id = $this->input->post('group_id');
				break;
			case 'U':
				$this->form_validation->set_rules('user_id', 'User', $valid_id);
				$entity_id = $this->input->post('user_id');
				break;
		}
		
		$this->form_validation->set_error_delimiters('<li>', '</li>');
		
		// Validate form
		if ($this->form_validation->run() == FALSE)
		{
			// Validation failed - load required action depending on the state of user_id
			return ($permission_id == NULL) ? $this->add() : $this->edit($permission_id);
		}
		else
		{
			
			$data = array();
			$data['entity_type'] = $entity_type;
			$data['entity_id'] = $entity_id;
			$data['permissions'] = $this->input->post('permissions');
			
			if (empty($permission_id))
			{
				// Add new permission
				$ret = $this->permissions_model->add($data);
			}
			else
			{
				$ret = $this->permissions_model->edit($permission_id, $data);
			}
			
			echo "Done!";
			echo $ret;
			echo $this->permissions_model->lasterr;
			
			//redirect('permissions');
			
		}
		
	}
	
	
	
}


/* End of file controllers/permissions.php */