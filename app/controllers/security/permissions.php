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


class Permissions extends Controller {


	var $tpl;
	

	function Permissions(){
		parent::Controller();
		$this->load->model('security');
		$this->tpl = $this->config->item('template');
		$this->output->enable_profiler($this->config->item('profiler'));
	}
	
	
	
	
	function index($tab = NULL){
		
		/* $links[] = array('security/users', 'Manage users');
		$links[] = array('security/groups', 'Manage groups');
		$links[] = array('security/permissions', 'Change group permissions', TRUE); */
		
		$body['sidebar'] = $this->load->view('security/permissions.side.php', NULL, TRUE);
		$body['tab'] = ($tab == NULL) ? $this->session->flashdata('tab') : $tab;
		$body['groups'] = $this->security->get_groups_dropdown();
		$body['permissions'] = $this->config->item('permissions');
		$body['group_permissions'] = $this->security->get_group_permissions();
		#print_r($body['permissions']);
		#$tpl['links'] = $this->load->view('parts/linkbar', $links, TRUE);
		$tpl['subnav'] = $this->security->subnav();
		$tpl['title'] = 'Permissions';
		$tpl['pagetitle'] = 'Manage group permissions';
		$tpl['body'] = $this->load->view('security/permissions.index.php', $body, TRUE);
		$this->load->view($this->tpl, $tpl);
		
	}
	
	
	
	
	function forgroup($group_id){
		$this->index($group_id);
	}
	
	
	
	
	function save(){
		
		$this->form_validation->set_rules('group_id', 'Group ID');
		$this->form_validation->set_rules('permissions[]', 'Permissions');
		$this->form_validation->set_rules('daysahead', 'days ahead');
		$this->form_validation->set_error_delimiters('<li>', '</li>');
		
		if($this->form_validation->run() == FALSE){
			
			// Validation failed - load required action depending on the state of user_id
			$this->index($this->input->post('group_id'));
			
		} else {
			
			// Validation OK
			$group_id = $this->input->post('group_id');
			$group_permissions = $this->input->post("permissions_{$group_id}");
			$save = $this->security->save_group_permissions($group_id, $group_permissions);
			
			if($save == FALSE){
				$this->msg->add('err', $this->security->lasterr, 'Error saving details');
			} else {
				$this->msg->add('info', 'Saved successfully');
			}
			$this->session->set_flashdata('tab', 'g'.$group_id);
			
			// Unset existing group permissions array in session - it will get re-filled on next page load
			if($group_id == $this->session->userdata('group_id')){
				$this->session->set_userdata('group_permissions', NULL);
			}
			
			redirect('security/permissions');
			
		}
		
	}
	
	
	
	
	/**
	 * Show effective permissions on a user
	 *
	 * @param	int		user_id		ID of user to find info on
	 * @param	bool	ajax		Whether the request is via ajax or a normal page
	 */
	function effective($user_id = NULL){
	
		$ajax = (array_key_exists('HTTP_X_REQUESTED_WITH', $_SERVER));
		
		$tpl['title'] = 'Effective user permissions';
		
		if($user_id == NULL){
			$tpl['pagetitle'] = $tpl['title'];
			$tpl['body'] = $this->msg->err($this->lang->line('PERMISSIONS_EFFECTIVE_USER_FAIL'));
		} else {
			$user = $this->security->get_user($user_id);
			$body['user_permissions'] = $this->security->get_user_permissions($user_id);
			$tpl['pagetitle'] = 'Effective permissions for ' . $user->displayname;
			$tpl['body'] = $this->load->view('security/permissions.effective.php', $body, TRUE);
		}
		
		if($ajax == FALSE){
			$this->load->view($this->tpl, $tpl);
		} else {
			$this->output->enable_profiler(FALSE);
			$this->load->view('security/permissions.effective.php', $body);
		}
		
	}
	
	
	
	
}


?>
