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
		);
		$this->pagination->initialize($config);
		
		$this->users_model->set_filter($filter);
		$this->users_model->order_by('g_name', 'asc');
		$this->users_model->limit($config['per_page'], $page);
		
		$this->data['filter'] = $filter;
		$this->data['groups'] = $this->groups_model->get_all();
		
		$this->layout->set_js('views/groups/index');
		
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
			$title = lang('groups_add_new');
			$this->layout->add_breadcrumb(lang('groups_add_new'), 'groups/set');
			$this->data['subnav_active'] = 'groups/set';
			$this->data['group'] = array();
		}
		
		$this->data['ldap_groups'] = $this->ldap_groups_model->ldap_groups_unassigned($g_id);
		
		$this->load->library('form');
	}
	
	
	
	
	/**
	 * PAGE: Add a new group
	 */
	/*function add()
	{
		$this->auth->check('groups.add');
		$body['group'] = null;
		$body['group_id'] = null;
		$body['ldapgroups'] = $this->security_model->get_ldap_groups_unassigned();
		$data['title'] = 'Add group';
		$data['body'] = $this->load->view('groups/addedit', $body, true);
		$this->page($data);
	}
	*/
	
	
	
	/**
	 * PAGE: Edit a group
	 */
	/*
	function edit($group_id)
	{
		$this->auth->check('groups.edit');
		$body['group'] = $this->security_model->get_group($group_id);
		$body['group_id'] = $group_id;
		$body['ldapgroups'] = $this->security_model->get_ldap_groups_unassigned($group_id);
		
		$data['title'] = 'Edit group';
		
		if ($body['group'] != false)
		{
			$data['title'] = 'Edit ' . $body['group']->name . ' group';
			$data['body'] = $this->load->view('groups/addedit', $body, true);
		}
		else
		{
			$data['title'] = 'Error getting group';
			$data['body'] = $this->msg->err('Could not load the specified group. Please check the ID and try again.');
		}
		$this->page($data);
	
	*/
	
	
	
	/*
	function save()
	{
		$group_id = $this->input->post('group_id');
		
		if ($group_id == null)
		{
			$this->auth->check('groups.add');
		}
		else
		{
			$this->auth->check('groups.edit');
		}
		
		$this->form_validation->set_rules('group_id', 'Group ID');
		$this->form_validation->set_rules('name', 'Name', 'required|max_length[20]|trim');
		$this->form_validation->set_rules('description', 'Description', 'max_length[255]|trim');
		$this->form_validation->set_rules('ldapgroups[]', 'LDAP Groups');
		// $this->form_validation->set_rules('daysahead', 'Booking days ahead', 'max_length[3]|numeric');
		//$this->form_validation->set_rules('quota_num', 'Quota', 'max_length[5]|numeric');
		//$this->form_validation->set_rules('quota_type', 'Quota type');
		$this->form_validation->set_error_delimiters('<li>', '</li>');

		if($this->form_validation->run() == FALSE){
			
			// Validation failed - load required action depending on the state of user_id
			($group_id == NULL) ? $this->add() : $this->edit($group_id);
			
		} else {
		
			// Validation OK
			$data['name'] = $this->input->post('name');
			$data['description'] = $this->input->post('description');
			$data['ldapgroups'] = ($this->input->post('ldapgroups')) ? $this->input->post('ldapgroups') : array();
			//$data['bookahead'] = $this->input->post('bookahead');
			//$data['quota_num'] = $this->input->post('quota_num');
			//$data['quota_type'] = $this->input->post('quota_type'); 
			
			if($data['quota_type'] == 'unlimited'){
				$data['quota_type'] = NULL;
				$data['quota_num'] = NULL;
			}

			if($group_id == NULL){
			
				$add = $this->security->add_group($data);
				
				if($add == TRUE){
					$this->msg->add('info', sprintf($this->lang->line('SECURITY_GROUP_ADD_OK'), $data['name']));
					$this->msg->add('note', 'You can now configure the permissions for this group by '.anchor('security/permissions/forgroup/'.$add, 'clicking here.'));
				} else {
					$this->msg->add('err', sprintf($this->lang->line('SECURITY_GROUP_ADD_FAIL', $this->security->lasterr)));
				}
			
			} else {
			
				// Updating existing group
				$edit = $this->security->edit_group($group_id, $data);
				if($edit == TRUE){
					$this->msg->add('info', sprintf($this->lang->line('SECURITY_GROUP_EDIT_OK'), $data['name']));
				} else {
					$this->msg->add('err', sprintf($this->lang->line('SECURITY_GROUP_EDIT_FAIL', $this->security->lasterr)));
				}
				
			}
			
			// All done, redirect!
			redirect('security/groups');
			
		}
		
	}
	*/
	
	
	
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
		
		if ($this->groups_model->delete($id))
		{
			$this->flash->set('success', lang('groups_delete_success'), TRUE);
		}
		else
		{
			$this->flash->set('error', lang('groups_delete_error'), TRUE);
		}
		
		redirect($this->input->post('redirect'));
	}
	
	
	
	
}


/* End of file ./application/controllers/groups.php */