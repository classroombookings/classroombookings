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


class Departments extends Controller {


	var $tpl;
	

	function Departments(){
		parent::Controller();
		$this->load->model('security');
		$this->load->model('departments_model');
		$this->load->helper('text');
		$this->tpl = $this->config->item('template');
		$this->output->enable_profiler($this->config->item('profiler'));
	}
	
	
	
	
	function index(){
		$this->auth->check('departments');
		$links[0] = array('departments/add', 'Add a new department');
		$tpl['links'] = $this->load->view('parts/linkbar', $links, TRUE);
		
		// Get list of departments
		$body['departments'] = $this->departments_model->get();
		if($body['departments'] == FALSE){
			$tpl['body'] = $this->msg->err($this->departments_model->lasterr);
		} else {
			$tpl['body'] = $this->load->view('departments/index', $body, TRUE);
		}
		
		$tpl['title'] = 'Departments';
		$tpl['pagetitle'] = $tpl['title'];
		$this->load->view($this->tpl, $tpl);
	}
	
	
	
	
	function add(){
		$this->auth->check('departments.add');
		$body['department'] = NULL;
		$body['department_id'] = NULL;
		$body['ldapgroups'] = $this->security->get_ldap_groups();
		$tpl['sidebar'] = $this->load->view('departments/addedit.sidebar.php', NULL, TRUE);
		$tpl['title'] = 'Add department';
		$tpl['pagetitle'] = 'Add a new department';
		$tpl['body'] = $this->load->view('departments/addedit', $body, TRUE);
		$this->load->view($this->tpl, $tpl);
	}
	
	
	
	
	function edit($department_id){
		$this->auth->check('departments.edit');
		$body['department'] = $this->departments_model->get($department_id);
		$body['department_id'] = $department_id;
		$body['ldapgroups'] = $this->security->get_ldap_groups();
		
		$tpl['title'] = 'Edit department';
		
		if($body['department'] != FALSE){
			$tpl['pagetitle'] = 'Edit department: ' . $body['department']->name;
			$tpl['body'] = $this->load->view('departments/addedit', $body, TRUE);
		} else {
			$tpl['pagetitle'] = 'Error getting department';
			$tpl['body'] = $this->msg->err('Could not load the specified department. Please check the ID and try again.');
		}
		
		$this->load->view($this->tpl, $tpl);
	}
	
	
	
	
	function save(){
		
		$department_id = $this->input->post('department_id');
		
		$this->form_validation->set_rules('department_id', 'Department ID');
		$this->form_validation->set_rules('name', 'Name', 'required|max_length[64]|trim');
		$this->form_validation->set_rules('description', 'Description', 'max_length[255]|trim');
		$this->form_validation->set_rules('colour', 'Colour', 'max_length[7]');
		$this->form_validation->set_rules('ldapgroups[]', 'LDAP Groups');
		$this->form_validation->set_error_delimiters('<li>', '</li>');
		
		if($this->form_validation->run() == FALSE){
			
			// Validation failed - load required action depending on the state of user_id
			($department_id == NULL) ? $this->add() : $this->edit($department_id);
			
		} else {
			
			// Validation OK
			$data['name'] = $this->input->post('name');
			$data['description'] = $this->input->post('description');
			$data['colour'] = $this->input->post('colour');
			$data['ldapgroups'] = ($this->input->post('ldapgroups')) ? $this->input->post('ldapgroups') : array();
			
			if($department_id == NULL){
			
				$add = $this->departments_model->add($data);
				
				if($add == TRUE){
					$this->msg->add('info', sprintf($this->lang->line('DEPARTMENTS_ADD_OK'), $data['name']));
				} else {
					$this->msg->add('err', sprintf($this->lang->line('DEPARTMENTS_ADD_FAIL', $this->departments_model->lasterr)));
				}
			
			} else {
			
				// Updating existing department
				$edit = $this->departments_model->edit($department_id, $data);
				if($edit == TRUE){
					$this->msg->add('info', sprintf($this->lang->line('DEPARTMENTS_EDIT_OK'), $data['name']));
				} else {
					$this->msg->add('err', sprintf($this->lang->line('DEPARTMENTS_EDIT_FAIL', $this->departments_model->lasterr)));
				}
				
			}
			
			// All done, redirect!
			redirect('departments');
			
		}
		
	}
	
	
	
	
	function delete($department_id = NULL){
		$this->auth->check('departments.delete');
		
		// Check if a form has been submitted; if not - show it to ask user confirmation
		if($this->input->post('id')){
		
			// Form has been submitted (so the POST value exists)
			// Call model function to delete department
			$delete = $this->departments_model->delete($this->input->post('id'));
			if($delete == FALSE){
				$this->msg->add('err', $this->departments_model->lasterr, 'An error occured');
			} else {
				$this->msg->add('info', 'The department has been deleted.');
			}
			// Redirect
			redirect('departments');
			
		} else {
			
			if($department_id == NULL){
				
				$tpl['title'] = 'Delete department';
				$tpl['pagetitle'] = $tpl['title'];
				$tpl['body'] = $this->msg->err('Cannot find the department or no department ID given.');
				
			} else {
				
				// Get department info so we can present the confirmation page with a name
				$department = $this->departments_model->get($department_id);
				
				if($department == FALSE){
				
					$tpl['title'] = 'Delete department';
					$tpl['pagetitle'] = $tpl['title'];
					$tpl['body'] = $this->msg->err('Could not find that department or no department ID given.');
					
				} else {
					
					// Initialise page
					$body['action'] = 'departments/delete';
					$body['id'] = $department_id;
					$body['cancel'] = 'departments';
					$body['text'] = 'If you delete this department, all people assigned to it will be removed.';
					$tpl['title'] = 'Delete department';
					$tpl['pagetitle'] = 'Delete ' . $department->name;
					$tpl['body'] = $this->load->view('parts/deleteconfirm', $body, TRUE);
					
				}
				
			}
			
			$this->load->view($this->tpl, $tpl);
			
		}
		
	}

	
	
	
	
}


/* End of file app/controllers/departments.php */