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
		
		$data['title'] = 'Permissions';
		$data['submenu'] = $this->menu_model->permissions();
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
		$body['permission_list'] = $this->config->item('permissions');
		
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
		
		$entity_type = $this->input->post('entity_type');
		$permission_id = $this->input->post('permission_id');
		
		$this->form_validation->set_rules('permission_id', 'Permission ID');
		$this->form_validation->set_rules('entity_type', 'Entity type', 'exact_length[1]');
		$this->form_validation->set_rules('permissions[]', 'Permissions');
		// Add a rule depending on chosen entity type
		switch($entity_type)
		{
			case 'D':
				$this->form_validation->set_rules('department_id', 'Department', 'required|integer');
				break;
			case 'G':
				$this->form_validation->set_rules('group_id', 'Group', 'required|integer');
				break;
			case 'U':
				$this->form_validation->set_rules('user_id', 'User', 'required|integer');
				break;
		}
		
		$this->form_validation->set_error_delimiters('<li>', '</li>');
		
		// Validate form
		if ($this->form_validation->run() == FALSE)
		{
			// Validation failed - load required action depending on the state of user_id
			($permission_id == NULL) ? $this->add() : $this->edit($permission_id);
			
		}
		else
		{
			
		}
		
	}
	
	
	
}


/* End of file controllers/permissions.php */