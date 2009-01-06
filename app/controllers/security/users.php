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


class Users extends Controller {


	var $tpl;
	
	
	function Users(){
		parent::Controller();
		$this->load->model('security');
		$this->tpl = $this->config->item('template');
		$this->output->enable_profiler($this->config->item('profiler'));
	}
	
	
	
	
	function index(){
		$this->auth->check('users');
		$links[] = array('security/users/add', 'Add a new user');
		$links[] = array('security/users/import', 'Import from file');
		$links[] = array('security/groups', 'Manage groups');
		$links[] = array('security/permissions', 'Change group permissions');
		$tpl['links'] = $this->load->view('parts/linkbar', $links, TRUE);
		
		// Get list of users
		$body['users'] = $this->security->get_user();
		if ($body['users'] == FALSE) {
			$tpl['body'] = $this->msg->err($this->security->lasterr);
		} else {
			$tpl['body'] = $this->load->view('security/users.index.php', $body, TRUE);
		}
		
		$tpl['title'] = 'Users';
		$tpl['pagetitle'] = 'Manage users';
		
		$this->load->view($this->tpl, $tpl);
	}
	
	
	
	
	function ingroup($group_id){
		$this->auth->check('users');
		$links[] = array('security/users/add', 'Add a new user');
		$links[] = array('security/users/import', 'Import from file');
		$links[] = array('security/groups', 'Manage groups');
		$links[] = array('security/permissions', 'Change group permissions');
		$tpl['links'] = $this->load->view('parts/linkbar', $links, TRUE);
		
		$tpl['title'] = 'Users';
		$groupname = $this->security->get_group_name($group_id);
		if ($groupname == FALSE) {
			$tpl['body'] = $this->msg->err($this->security->lasterr);
			$tpl['pagetitle'] = $tpl['title'];
		} else {
			$body['users'] = $this->security->get_user(NULL, $group_id);
			if ($body['users'] === FALSE) {
				$tpl['body'] = $this->msg->err($this->security->lasterr);
			} else {
				$tpl['body'] = $this->load->view('security/users.index.php', $body, TRUE);
			}
			$tpl['pagetitle'] = sprintf('Manage users in the %s group', $groupname);
		}
		
		$this->load->view($this->tpl, $tpl);
	}
	
	
	
	
	function add(){
		$this->auth->check('users.add');
		$body['user'] = NULL;
		$body['user_id'] = NULL;
		$body['groups'] = $this->security->get_groups_dropdown();
		$tpl['sidebar'] = $this->load->view('security/users.addedit.side.php', NULL, TRUE);
		$tpl['title'] = 'Add user';
		$tpl['pagetitle'] = 'Add a new user';
		$tpl['body'] = $this->load->view('security/users.addedit.php', $body, TRUE);
		$this->load->view($this->tpl, $tpl);
	}
	
	
	
	
	function edit($user_id){
		$this->auth->check('users.edit');
		$body['user'] = $this->security->get_user($user_id);
		$body['user_id'] = $user_id;
		$body['groups'] = $this->security->get_groups_dropdown();
		
		$tpl['title'] = 'Edit user';
		$tpl['pagetitle'] = ($body['user']->displayname == FALSE) ? 'Edit ' . $body['user']->username : 'Edit ' . $body['user']->displayname;
		$tpl['body'] = $this->load->view('security/users.addedit.php', $body, TRUE);
		
		$this->load->view($this->tpl, $tpl);
	}
	
	
	
	
	function save(){
		$user_id = $this->input->post('user_id');
		
		$this->form_validation->set_rules('user_id', 'User ID');
		$this->form_validation->set_rules('username', 'Username', 'required|max_length[64]|trim');
		if(!$user_id){
			$this->form_validation->set_rules('password1', 'Password', 'max_length[104]|required');
			$this->form_validation->set_rules('password2', 'Password (confirmation)', 'max_length[104]|required|matches[password1]');
		}
		$this->form_validation->set_rules('group_id', 'Group', 'required|integer');
		$this->form_validation->set_rules('enabled', 'Enabled', 'exact_length[1]');
		$this->form_validation->set_rules('email', 'Email address', 'max_length[256]|valid_email|trim');
		$this->form_validation->set_rules('displayname', 'Display name', 'max_length[64]|trim');
		//$this->form_validation->set_rules('department_id', 'Department', 'integer');
		$this->form_validation->set_error_delimiters('<li>', '</li>');
		
		if($this->form_validation->run() == FALSE){
			
			// Validation failed - load required action depending on the state of user_id
			($user_id == NULL) ? $this->add() : $this->edit($user_id);
			
		} else {
		
			// Validation OK
			$data['username'] = $this->input->post('username');
			$data['displayname'] = $this->input->post('displayname');
			$data['email'] = $this->input->post('email');
			$data['group_id'] = $this->input->post('group_id');
			//$data['department_id'] = $this->input->post('department_id');
			$data['enabled'] = ($this->input->post('enabled') == '1') ? 1 : 0;
			// Only set password if supplied.
			if($this->input->post('password1')){
				$data['password'] = sha1($this->input->post('password1'));
			}
			
			if($user_id == NULL){
			
				// Adding user
				$data['ldap'] = 0;
				
				#die(var_export($data, true));
				$add = $this->security->add_user($data);
				
				if($add == TRUE){
					$message = ($data['enabled'] == 1) ? 'SECURITY_USER_ADD_OK_ENABLED' : 'SECURITY_USER_ADD_OK_DISABLED';
					$this->msg->add('info', $this->lang->line($message));
				} else {
					$this->msg->add('err', sprintf($this->lang->line('SECURITY_USER_ADD_FAIL', $this->security->lasterr)));
				}
			
			} else {
			
				// Updating existing user
				$edit = $this->security->edit_user($user_id, $data);
				if($edit == TRUE){
					$message = ($data['enabled'] == 1) ? 'SECURITY_USER_EDIT_OK_ENABLED' : 'SECURITY_USER_EDIT_OK_DISABLED';
					$this->msg->add('info', $this->lang->line($message));
				} else {
					$this->msg->add('err', sprintf($this->lang->line('SECURITY_USER_EDIT_FAIL', $this->security->lasterr)));
				}
				
			}
			
			// All done, redirect!
			redirect('security/users');
			
		}
		
	}
	
	
	
	
	function delete($user_id = NULL){
		$this->auth->check('users.delete');
		
		// Check if a form has been submitted; if not - show it to ask user confirmation
		if($this->input->post('id')){
		
			// Form has been submitted (so the POST value exists)
			// Call model function to delete user
			$delete = $this->security->delete_user($this->input->post('id'));
			if($delete == FALSE){
				$this->msg->add('err', $this->security->lasterr, 'An error occured');
			} else {
				$this->msg->add('info', 'The user has been deleted.');
			}
			// Redirect
			redirect('security/users');
			
		} else {
		
			if( ($this->session->userdata('user_id')) && ($user_id == $this->session->userdata('user_id')) ){
				$this->msg->add(
					'warn',
					base64_decode('WW91IGNhbm5vdCBkZWxldGUgeW91cnNlbGYsIHRoZSB1bml2ZXJzZSB3aWxsIGltcGxvZGUu'),
					base64_decode('RXJyb3IgSUQjMTBU')
				);
				redirect('security/users');
			}
			
			if($user_id == NULL){
				
				$tpl['title'] = 'Delete user';
				$tpl['pagetitle'] = $tpl['title'];
				$tpl['body'] = $this->msg->err('Cannot find the user or no user ID given.');
				
			} else {
				
				// Get user info so we can present the confirmation page with a dsplayname/username
				$user = $this->security->get_user($user_id);
				
				if($user == FALSE){
				
					$tpl['title'] = 'Delete user';
					$tpl['pagetitle'] = $tpl['title'];
					$tpl['body'] = $this->msg->err('Could not find that user or no user ID given.');
					
				} else {
					
					// Initialise page
					$body['action'] = 'security/users/delete';
					$body['id'] = $user_id;
					$body['cancel'] = 'security/users';
					$body['text'] = 'If you delete this user, all of their bookings and room owenership information will also be deleted.';
					$tpl['title'] = 'Delete user';
					$tpl['pagetitle'] = 'Delete ' . $user->display2;
					$tpl['body'] = $this->load->view('parts/deleteconfirm', $body, TRUE);
					
				}
				
			}
			
			$this->load->view($this->tpl, $tpl);
			
		}
		
	}
	
	
	
	
}


/* End of file app/controllers/security/users.php */