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
		$this->load->model('security_model');
		$this->load->helper('text');
	}
	
	
	
	
	/**
	 * PAGE: Main list of groups
	 */
	function index()
	{
		$this->auth->check('groups');
		// Get list of groups
		$body['groups'] = $this->security_model->get_group();
		if ($body['groups'] == false)
		{
			$data['body'] = $this->msg->err($this->security_model->lasterr);
		}
		else
		{
			$data['body'] = $this->load->view('groups/index', $body, true);
		}
		$data['title'] = 'Groups';
		$data['submenu'] = $this->menu_model->groups();
		$this->page($data);
	}
	
	
	
	
	function add(){
		$this->auth->check('groups.add');
		$body['group'] = NULL;
		$body['group_id'] = NULL;
		$body['ldapgroups'] = $this->security->get_ldap_groups_unassigned();
		$tpl['subnav'] = $this->security->subnav();
		$tpl['title'] = 'Add group';
		$tpl['pagetitle'] = 'Add a new group';
		$tpl['body'] = $this->load->view('security/groups.addedit.php', $body, TRUE);
		$this->load->view($this->tpl, $tpl);
	}
	
	
	
	
	function edit($group_id){
		$this->auth->check('groups.edit');
		$body['group'] = $this->security->get_group($group_id);
		$body['group_id'] = $group_id;
		$body['ldapgroups'] = $this->security->get_ldap_groups_unassigned($group_id);
		
		$tpl['subnav'] = $this->security->subnav();
		$tpl['title'] = 'Edit group';
		
		if($body['group'] != FALSE){
			$tpl['pagetitle'] = 'Edit ' . $body['group']->name . ' group';
			$tpl['body'] = $this->load->view('security/groups.addedit.php', $body, TRUE);
		} else {
			$tpl['pagetitle'] = 'Error getting group';
			$tpl['body'] = $this->msg->err('Could not load the specified group. Please check the ID and try again.');
		}
		
		$this->load->view($this->tpl, $tpl);
	}
	
	
	
	
	function save(){
		
		$group_id = $this->input->post('group_id');
		
		$this->form_validation->set_rules('group_id', 'Group ID');
		$this->form_validation->set_rules('name', 'Name', 'required|max_length[20]|trim');
		$this->form_validation->set_rules('description', 'Description', 'max_length[255]|trim');
		$this->form_validation->set_rules('ldapgroups[]', 'LDAP Groups');
		$this->form_validation->set_rules('daysahead', 'Booking days ahead', 'max_length[3]|numeric');
		$this->form_validation->set_rules('quota_num', 'Quota', 'max_length[5]|numeric');
		$this->form_validation->set_rules('quota_type', 'Quota type');
		$this->form_validation->set_error_delimiters('<li>', '</li>');

		if($this->form_validation->run() == FALSE){
			
			// Validation failed - load required action depending on the state of user_id
			($group_id == NULL) ? $this->add() : $this->edit($group_id);
			
		} else {
		
			// Validation OK
			$data['name'] = $this->input->post('name');
			$data['description'] = $this->input->post('description');
			$data['ldapgroups'] = ($this->input->post('ldapgroups')) ? $this->input->post('ldapgroups') : array();
			$data['bookahead'] = $this->input->post('bookahead');
			$data['quota_num'] = $this->input->post('quota_num');
			$data['quota_type'] = $this->input->post('quota_type');
			
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
	
	
	
	
	function delete($group_id = NULL){
		$this->auth->check('groups.delete');
		
		// Check if a form has been submitted; if not - show it to ask user confirmation
		if($this->input->post('id')){
		
			// Form has been submitted (so the POST value exists)
			// Call model function to delete user
			$delete = $this->security->delete_group($this->input->post('id'));
			if($delete == FALSE){
				$this->msg->add('err', $this->security->lasterr, 'An error occured');
			} else {
				$this->msg->add('info', 'The group has been deleted.');
			}
			// Redirect
			redirect('security/groups');
			
		} else {
		
			if( ($this->session->userdata('group_id')) && ($group_id == $this->session->userdata('group_id')) ){
				$this->msg->add(
					'warn',
					base64_decode('WW91IGNhbm5vdCBkZWxldGUgdGhlIGdyb3VwIHRoYXQgeW91IGFyZSBhIG1lbWJlciBvZiwgdGhlIHVuaXZlcnNlIGlzIGxpa2VseSB0byBpbXBsb2RlLg=='),
					base64_decode('RXJyb3IgSUQjMTBU')
				);
				redirect('security/groups');
			}
			
			if($group_id == NULL){
				
				$tpl['title'] = 'Delete group';
				$tpl['pagetitle'] = $tpl['title'];
				$tpl['body'] = $this->msg->err('Cannot find the group or no group ID given.');
				
			} else {
				
				// Get user info so we can present the confirmation page with a dsplayname/username
				$group = $this->security->get_group($group_id);
				
				if($group == FALSE){
				
					$tpl['title'] = 'Delete group';
					$tpl['pagetitle'] = $tpl['title'];
					$tpl['body'] = $this->msg->err('Could not find that group or no group ID given.');
					
				} else {
					
					// Initialise page
					$body['action'] = 'security/groups/delete';
					$body['id'] = $group_id;
					$body['cancel'] = 'security/groups';
					$body['text'] = 'If you delete this group, all of its users (if any) will be re-assigned to the Guests group.';
					$tpl['title'] = 'Delete group';
					$tpl['pagetitle'] = 'Delete ' . $group->name;
					$tpl['body'] = $this->load->view('parts/deleteconfirm', $body, TRUE);
					
				}
				
			}
			
			$tpl['subnav'] = $this->security->subnav();
			$this->load->view($this->tpl, $tpl);
			
		}
		
	}
	
	
	
	
}


/* End of file controllers/security/groups.php */