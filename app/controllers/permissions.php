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
	
	
	
	
	function save()
	{
		$this->output->enable_profiler(true);
	}
	
	
	
}


/* End of file controllers/permissions.php */