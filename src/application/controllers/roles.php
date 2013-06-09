<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Classroombookings. Hassle-free resource booking for schools. <http://classroombookings.com/>
 * Copyright (C) 2006-2011 Craig A Rodway <craig.rodway@gmail.com>
 *
 * This file is part of Classroombookings.
 * Classroombookings is licensed under the Affero GNU GPLv3 license.
 * Please see license-classroombookings.txt for the full license text.
 */

class Roles extends Configure_Controller
{	


	function __construct()
	{
		parent::__construct();
		$this->lang->load('configure');
		$this->lang->load('authentication');
		$this->lang->load('roles');
		
		$this->load->model(array('departments_model', 'groups_model', 'users_model', 'permissions_model', 'roles_model'));
		$this->load->helper('role');
		
		
		$this->layout->add_breadcrumb(lang('roles_roles'), 'roles');
		
		$this->data['subnav'] = array(
			array(
				'uri' => 'roles',
				'text' => lang('roles_roles'),
				'test' => $this->auth->check('permissions.view'),
			),
			array(
				'uri' => 'roles/permissions',
				'text' => lang('roles_permissions'),
				'test' => $this->auth->check('permissions.view'),
			),
		);
	}
	
	
	
	
	// =======================================================================
	// Roles & Permissions configuration pages
	// =======================================================================
	
	
	
	
	public function index()
	{
		$this->auth->restrict('permissions.view');
		
		$this->data['roles'] = $this->roles_model->get_all_with_assigned();
		
		$this->layout->set_title(lang('roles_roles'));
		$this->load->library('form');
		$this->data['subnav_active'] = 'roles';
		
		$this->layout->set_js('jquery.autocomplete.min', 'js/plugins/');
		$this->layout->set_js('views/roles/index');
	}
	
	
	
	
	public function permissions()
	{
		$this->layout->add_breadcrumb(lang('roles_permissions'), 'roles/permissions');
		
		$this->layout->set_title(lang('roles_permissions'));
		$this->load->library('form');
		$this->data['subnav_active'] = 'roles/permissions';
	}
	
	
	
	
	// ========================================================================
	// Entities
	// ========================================================================
	
	
	
	public function entity_search()
	{
		$q = $this->input->get('query');
		
		$result = $this->roles_model->entity_search($q);
		
		foreach ($result as $row)
		{
			$row['e_type_lang'] = lang('roles_entity_type_' . $row['e_type']);
			
			$suggestions[] = array(
				'value' => $row['e_name'] . ' (' . $row['e_type_lang'] . ')',
				'data' => $row,
			);
		}
		
		$this->json = array(
			'query' => $q,
			'suggestions' => $suggestions,
		);
		
		return;
	}
	
	
	
	
	public function assign()
	{
		if ($this->input->post())
		{
			$r_id = $this->input->post('r_id');
			$e_type = $this->input->post('e_type');
			$e_id = $this->input->post('e_id');
			
			$role = $this->roles_model->get($r_id);
			
			switch ($e_type)
			{
				case 'U':
					$entity = $this->users_model->get($e_id);
					$name = $entity['u_username'];
				break;
				
				case 'G':
					$entity = $this->groups_model->get($e_id);
					$name = $entity['g_name'];
				break;
				
				case 'D':
					$entity = $this->departments_model->get($e_id);
					$name = $entity['d_name'];
				break;
			}
			
			if ($entity && $role)
			{
				if ($this->roles_model->assign_role($r_id, $e_type, $e_id))
				{
					Events::trigger('role_assign', array(
						'role' => $role,
						'entity' => $entity,
						'name' => $name,
						'e_type' => $e_type,
						'e_type_lang' => lang('roles_entity_type_' . $e_type),
					));
					
					$this->json = array('status' => 'success');
				}
				else
				{
					$this->json = array(
						'status' => 'error',
						'reason' => 'Could not add item to role.',
					);
				}
			}
			else
			{
				$this->json = array(
					'status' => 'error',
					'reason' => 'Could not find role or entity.',
				);
			}
		}
		
		return;
	}
	
	
	
	
	public function unassign()
	{
		if ($this->input->post())
		{
			$r_id = $this->input->post('r_id');
			$e_type = $this->input->post('e_type');
			$e_id = $this->input->post('e_id');
			
			$role = $this->roles_model->get($r_id);
			
			switch ($e_type)
			{
				case 'U':
					$entity = $this->users_model->get($e_id);
					$name = $entity['u_username'];
				break;
				
				case 'G':
					$entity = $this->groups_model->get($e_id);
					$name = $entity['g_name'];
				break;
				
				case 'D':
					$entity = $this->departments_model->get($e_id);
					$name = $entity['d_name'];
				break;
			}
			
			if ($role && $entity)
			{			
				if ($this->roles_model->unassign_role($r_id, $e_type, $e_id))
				{
					
					Events::trigger('role_unassign', array(
						'role' => $role,
						'entity' => $entity,
						'name' => $name,
						'e_type' => $e_type,
						'e_type_lang' => lang('roles_entity_type_' . $e_type),
					));
					
					$this->json = array('status' => 'success');
				}
				else
				{
					$this->json = array(
						'status' => 'error',
						'reason' => 'Could not remove entity from role.',
					);
				}
			}
			else
			{
				$this->json = array(
					'status' => 'error',
					'reason' => 'Could not find role or entity.',
				);
			}
		}
		
		return;
	}
	
	
	
	
}

/* End of file ./application/controllers/roles.php */