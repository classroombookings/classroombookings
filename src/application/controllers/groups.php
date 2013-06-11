<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Classroombookings. Hassle-free resource booking for schools. <http://classroombookings.com/>
 * Copyright (C) 2006-2011 Craig A Rodway <craig.rodway@gmail.com>
 *
 * This file is part of Classroombookings.
 * Classroombookings is licensed under the Affero GNU GPLv3 license.
 * Please see license-classroombookings.txt for the full license text.
 */

class Groups extends Configure_Controller
{	
	
	
	function __construct()
	{
		parent::__construct();
		
		$this->lang->load('configure');
		$this->lang->load('groups');
		$this->load->model(array('groups_model', 'ldap_groups_model'));
		$this->load->helper('group_helper');
		$this->data['nav_current'][] = 'groups';
		
		$this->layout->add_breadcrumb(lang('configure_groups'), 'groups');
		
		$this->data['subnav'] = array(
			array(
				'uri' => 'groups',
				'text' => lang('configure_groups'),
				'test' => $this->auth->check('groups.view'),
			),
			array(
				'uri' => 'groups/set',
				'text' => lang('groups_add_new'),
				'test' => $this->auth->check('groups.add'),
			),
		);
	}
	
	
	
	
	// =======================================================================
	// Group management pages
	// =======================================================================
	
	
	
	
	/**
	 * Index listing of groups
	 */
	function index($page = 0)
	{
		$this->auth->restrict('groups.view');
		
		$filter = $this->input->get(NULL, TRUE);
		$filter['pp'] = element('pp', $filter, 10);
		
		$this->load->library('pagination');
		$config = array(
			'base_url' => site_url('groups/index'),
			'total_rows' => $this->groups_model->count_all(),
			'per_page' => $filter['pp'],
			'uri_segment' => 3,
			'suffix' => '?' . @http_build_query($filter),
		);
		$this->pagination->initialize($config);
		
		$this->groups_model->set_filter($filter);
		$this->groups_model->order_by('g_name', 'asc');
		$this->groups_model->limit($config['per_page'], $page);
		
		$this->data['filter'] = $filter;
		$this->data['groups'] = $this->groups_model->get_all();
		
		$this->layout->set_title(lang('configure_groups'));
		$this->data['subnav_active'] = 'groups';
	}
	
	
	
	
	public function set($g_id = 0)
	{
		if ($g_id)
		{
			// Updating group $g_id
			$this->auth->restrict('groups.edit');
			$this->data['group'] = $this->groups_model->get($g_id);
			$title = lang('groups_edit');
			$this->layout->add_breadcrumb(lang('groups_edit'), 'groups/set/' . $g_id);
		}
		else
		{
			// Adding new group
			$this->auth->restrict('groups.add');
			$this->data['group'] = array();
			$title = lang('groups_add_new');
			$this->layout->add_breadcrumb(lang('groups_add_new'), 'groups/set');
			$this->data['subnav_active'] = 'groups/set';
		}
		
		if ($this->input->post())
		{
			$this->form_validation->set_rules(array(
				array('field' => 'g_name', 'label' => lang('groups_group_name'), 'rules' => 'required|max_length[20]|trim'),
				array('field' => 'g_description', 'label' => lang('groups_group_description'), 'rules' => 'max_length[255]|trim'),
			));
			
			
			if ($this->form_validation->run())
			{
				$group_data = array(
					'g_name' => $this->input->post('g_name'),
					'g_description' => $this->input->post('g_description'),
				);
				
				if ($g_id)
				{
					// Update
					$g_id = $this->groups_model->update($g_id, $group_data);
					$success = lang('groups_update_success');
					$error = lang('groups_update_error');
					$event = 'group_update';
				}
				else
				{
					// Insert
					$g_id = $this->groups_model->insert($group_data);
					$success = lang('groups_insert_success');
					$error = lang('groups_insert_error');
					$event = 'group_insert';
				}
				
				if ($g_id)
				{
					// Success
					
					// Set LDAP groups membership
					$this->groups_model->set_ldap_groups($g_id, $this->input->post('ldap_groups'));
					
					Events::trigger($event, array(
						'g_id' => $g_id,
						'group' => $group_data,
					));
					
					$this->flash->set('success', $success, TRUE);
					redirect('groups');
				}
				else
				{
					$this->flash->set('error', $error);
				}
				
			}  // end validation->run()
			
		}  // end POST check
		
		$this->data['ldap_groups'] = $this->ldap_groups_model->ldap_groups_unassigned($g_id);
		
		$this->load->library('form');
	}
	
	
	
	
	/**
	 * Delete a user group
	 */
	function delete()
	{
		$this->auth->restrict('groups.delete');
		
		$id = $this->input->post('id');
		
		if ( ! $id)
		{
			redirect('groups/index');
		}
		
		$group = $this->groups_model->get($id);
		
		if ($this->groups_model->delete($id))
		{
			$this->flash->set('success', lang('groups_delete_success'), TRUE);
			
			Events::trigger('group_delete', array(
				'g_id' => $id,
				'group' => $group,
			));
		}
		else
		{
			$this->flash->set('error', lang('groups_delete_error'), TRUE);
		}
		
		redirect($this->input->post('redirect'));
	}
	
	
	
	
}

/* End of file ./application/controllers/groups.php */