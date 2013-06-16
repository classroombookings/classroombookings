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
				'test' => $this->auth->check('permissions.view'),		// @TODO
			),
			array(
				'uri' => 'roles/set',
				'text' => lang('roles_add_new'),
				'test' => $this->auth->check('permissions.view'),		// @TODO
			),
			array(
				'uri' => 'roles/permissions',
				'text' => lang('roles_permissions'),
				'test' => $this->auth->check('permissions.view'),		// @TODO
			),
		);
	}
	
	
	
	
	// =======================================================================
	// Roles & Permissions configuration pages
	// =======================================================================
	
	
	
	
	/**
	 * Roles index page
	 */
	public function index()
	{
		$this->auth->restrict('permissions.view');
		
		$this->data['roles'] = $this->roles_model->get_all_with_assigned();
		
		$this->layout->set_title(lang('roles_roles'));
		$this->load->library('form');
		$this->data['subnav_active'] = 'roles';
		
		$this->layout->set_js('jquery.autocomplete.min', 'js/plugins/');
		$this->layout->set_js('jquery.sortable.min', 'js/plugins/');
		$this->layout->set_js('views/roles/index');
	}
	
	
	
	
	/**
	 * Add or update a role
	 */
	public function set($r_id = 0)
	{
		if ($r_id)
		{
			// Updating role $d_id
			$this->auth->restrict('permissions.view');		// @TODO
			$this->data['role'] = $this->roles_model->get($r_id);
			$title = lang('roles_edit');
			$this->layout->add_breadcrumb(lang('roles_edit'), 'roles/set/' . $r_id);
		}
		else
		{
			// Adding new role
			$this->auth->restrict('permissions.view');		// @TODO
			$this->data['role'] = array();
			$title = lang('roles_add_new');
			$this->layout->add_breadcrumb(lang('roles_add_new'), 'roles/set');
			$this->data['subnav_active'] = 'roles/set';
		}
		
		if ($this->input->post())
		{
			$this->form_validation->set_rules(array(
				array('field' => 'r_name', 'label' => lang('roles_role_name'), 'rules' => 'required|max_length[32]|trim'),
			));
			
			
			if ($this->form_validation->run())
			{
				$role_data = array(
					'r_name' => $this->input->post('r_name'),
				);
				
				if ($r_id)
				{
					// Update
					$r_id = $this->roles_model->update($r_id, $role_data);
					$success = sprintf(lang('roles_update_success'), $role_data['r_name']);
					$error = sprintf(lang('roles_update_error'), $role_data['r_name']);
					$event = 'role_update';
				}
				else
				{
					// Insert
					$r_id = $this->roles_model->insert($role_data);
					$success = sprintf(lang('roles_insert_success'), $role_data['r_name']);
					$error = sprintf(lang('roles_insert_error'), $role_data['r_name']);
					$event = 'role_insert';
				}
				
				if ($r_id)
				{
					// Success
					
					Events::trigger($event, array(
						'r_id' => $r_id,
						'role' => $role_data,
					));
					
					$this->flash->set('success', $success, TRUE);
					redirect($this->session->get_return_uri('r', 'roles'));
				}
				else
				{
					$this->flash->set('error', $error);
				}
				
			}  // end validation->run()
			
		}  // end POST check
		
		$this->load->library('form');
	}
	
	
	
	
	/**
	 * Delete a role
	 */
	function delete()
	{
		$this->auth->restrict('permissions.view');		// @TODO
		
		$id = $this->input->post('id');
		
		if ( ! $id)
		{
			redirect('roles');
		}
		
		$role = $this->roles_model->get($id);
		
		if ($this->roles_model->delete($id))
		{
			$this->flash->set('success', lang('roles_delete_success'), TRUE);
			
			Events::trigger('role_delete', array(
				'r_id' => $id,
				'role' => $role,
			));
		}
		else
		{
			$this->flash->set('error', lang('roles_delete_error'), TRUE);
		}
		
		redirect($this->input->post('redirect'));
	}
	
	
	
	
	// ========================================================================
	// AJAX functions for entity search and assign/unassign operations
	// ========================================================================
	
	
	
	
	/**
	 * AJAX function: handle update of role order
	 */
	public function set_order()
	{
		$order = $this->input->post('order');
		
		if ($this->roles_model->set_order($order))
		{
			$this->json = array('status' => 'success');
		}
		else
		{
			$this->json = array(
				'status' => 'error',
				'reason' => 'Unable to re-order.',
			);
		}
		
		return;
	}
	
	
	
	
	/**
	 * Find an entity to assign a role to
	 */
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
	
	
	
	
	/**
	 * AJAX function: assign a role to an entity
	 */
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
	
	
	
	
	/**
	 * AJAX function: unassign a role from an entity
	 */
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
	
	
	
	
	// =======================================================================
	// Permissions
	// =======================================================================
	
	
	
	
	/**
	 * Permissions page (for all roles or just one)
	 */
	public function permissions($r_id = 0)
	{
		$this->layout->add_breadcrumb(lang('roles_permissions'), 'roles/permissions');
		
		$this->layout->set_title(lang('roles_permissions'));
		$this->load->library('form');
		$this->data['subnav_active'] = 'roles/permissions';
	}
	
	
	
	
}

/* End of file ./application/controllers/roles.php */