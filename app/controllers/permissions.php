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
	 * PAGE: Main permission page
	 */
	function index()
	{
		$this->auth->check('permissions');
		
		$body['defined_permissions'] = $this->permissions_model->get_list();
		$body['available_permissions'] = $this->config->item('permissions');
		$body['permission_values'] = array();
		
		// Loop through the defined permissions
		foreach ($body['defined_permissions'] as $permission)
		{
			// Get the actual values for this permission ID
			$body['permission_values'][$permission->permission_id] = $this->permissions_model->get_values($permission->permission_id);
			
			// Create view data array for this tab
			$tabview = array();
			$tabview['id'] = $permission->permission_id;
			$tabview['defined_permissions'] = $body['defined_permissions'];
			$tabview['available_permissions'] = $body['available_permissions'];
			$tabview['permission_values'] = $body['permission_values'][$permission->permission_id];
			
			// Work out the nice title for it
			if ($permission->entity_type == 'E')
			{
				$title = 'Everyone';
			}
			else
			{
				$title = sprintf('%s: %s', $permission->entity_type, $permission->entity_name);
			}
			
			// Add a main tab to the page
			$tabs[] = array(
				'id' => 'p_' . $permission->permission_id,
				'title' => $title,
				'view' => $this->load->view('permissions/list', $tabview, true),
			);
		}
		
		$body['tabs'] = $tabs;
		$body['active_tab'] = 'p_E';
		
		$data['title'] = 'Permissions';
		$data['submenu'] = $this->menu_model->permissions();
		$data['body'] = $this->load->view('parts/tabs', $body, true);
		$data['body'] .= $this->load->view('permissions/index', null, true);
		
		$data['js'] = array('js/tristate-checkbox.js');
		
		$this->page($data);
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
	function save()
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