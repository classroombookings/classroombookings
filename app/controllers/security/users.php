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
		$this->load->model('Security');
		$this->tpl = $this->config->item('template');
		$this->output->enable_profiler(TRUE);
	}
	
	
	
	
	function index(){
		$icondata[0] = array('security/users/add', 'Add a new user', 'plus.gif' );
		$icondata[1] = array('security/groups', 'Manage groups', 'group.gif' );
		$icondata[2] = array('security/permissions', 'Change group permissions', 'key2.gif');
		$tpl['pretitle'] = $this->load->view('parts/iconbar', $icondata, TRUE);
		
		// Get list of users
		$body['users'] = $this->Security->get_user();
		if ($body['users'] == FALSE) {
			$tpl['body'] = $this->msg->err($this->Security->lasterr);
		} else {
			$tpl['body'] = $this->load->view('security/users.index.php', $body, TRUE);
		}
		
		$tpl['title'] = 'Users';
		$tpl['pagetitle'] = 'Manage users';
		
		$this->load->view($this->tpl, $tpl);
	}
	
	
	
	
	function ingroup($group_id){
		$icondata[0] = array('security/users/add', 'Add a new user', 'plus.gif' );
		$icondata[1] = array('security/groups', 'Manage groups', 'group.gif' );
		$icondata[2] = array('security/permissions', 'Change group permissions', 'key2.gif');
		$tpl['pretitle'] = $this->load->view('parts/iconbar', $icondata, TRUE);
		
		$tpl['title'] = 'Users';
		$groupname = $this->Security->get_group_name($group_id);
		if ($groupname == FALSE) {
			$tpl['body'] = $this->msg->err($this->Security->lasterr);
			$tpl['pagetitle'] = $tpl['title'];
		} else {
			$body['users'] = $this->Security->get_user(NULL, $group_id);
			if ($body['users'] === FALSE) {
				$tpl['body'] = $this->msg->err($this->Security->lasterr);
			} else {
				$tpl['body'] = $this->load->view('security/users.index.php', $body, TRUE);
			}
			$tpl['pagetitle'] = sprintf('Manage users in the %s group', $groupname);
		}
		
		$this->load->view($this->tpl, $tpl);
	}
	
	
	
	
	function add(){
	}
	
	
	
	
	function edit($user_id){
		echo $user_id;
	}
	
	
	
	
}


?>
