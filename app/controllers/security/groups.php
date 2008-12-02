<?php
/*
	This file is part of Classroombookings.

	Classroombookings is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	Classroombookings is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with Classroombookings.  If not, see <http://www.gnu.org/licenses/>.
*/


class Groups extends Controller {


	var $tpl;
	

	function Groups(){
		parent::Controller();
		$this->load->model('security');
		$this->tpl = $this->config->item('template');
		$this->load->helper('text');
	}
	
	
	
	
	function index(){
		$icondata[0] = array('security/groups/add', 'Add a new group', 'plus.gif' );
		$icondata[1] = array('security/users', 'Manage users', 'user_orange.gif' );
		$icondata[2] = array('security/permissions', 'Change group permissions', 'key2.gif');
		$tpl['pretitle'] = $this->load->view('parts/iconbar', $icondata, TRUE);
		
		// Get list of users
		$body['groups'] = $this->security->get_group();
		if ($body['groups'] == FALSE) {
			$tpl['body'] = $this->msg->err($this->security->lasterr);
		} else {
			$tpl['body'] = $this->load->view('security/groups.index.php', $body, TRUE);
		}
		
		$tpl['title'] = 'Groups';
		$tpl['pagetitle'] = 'Manage user groups';
		
		$this->load->view($this->tpl, $tpl);
	}
	
	
	
	
	function add(){
		$body['group'] = NULL;
		$body['group_id'] = NULL;
		#$body['groups'] = $this->security->get_groups_dropdown();
		$tpl['title'] = 'Add group';
		$tpl['pagetitle'] = 'Add a new group';
		$tpl['body'] = $this->load->view('security/groups.addedit.php', $body, TRUE);
		$this->load->view($this->tpl, $tpl);
	}
	
	
	
	
	function edit($group_id){
		$body['group'] = $this->security->get_group($group_id);
		$body['group_id'] = $group_id;
		#$body['groups'] = $this->security->get_groups_dropdown();
		
		$tpl['title'] = 'Edit group';
		$tpl['pagetitle'] = 'Edit ' . $body['group']->name . ' group';
		$tpl['body'] = $this->load->view('security/groups.addedit.php', $body, TRUE);
		
		$this->load->view($this->tpl, $tpl);
	}
	
	
	
	
	function save(){
		
		$group_id = $this->input->post('group_id');
		
		$this->form_validation->set_rules('group_id', 'Group ID');
		$this->form_validation->set_rules('name', 'Name', 'required|max_length[20]|trim');
		$this->form_validation->set_rules('description', 'Description', 'max_length[255]|trim');
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
				} else {
					$this->msg->add('err', sprintf($this->lang->line('SECURITY_GROUP_ADD_FAIL', $this->security->lasterr)));
				}
			
			} else {
			
				// Updating existing user
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
	
	
	
	
}


?>
