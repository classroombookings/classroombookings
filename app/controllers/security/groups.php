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
		$this->tpl = $this->config->item('template');
	}
	
	
	
	
	function index(){
		$icondata[0] = array('security/groups/add', 'Add a new group', 'plus.gif' );
		$icondata[1] = array('security/users', 'Manage users', 'user_orange.gif' );
		$icondata[2] = array('security/permissions', 'Change group permissions', 'key2.gif');
		$tpl['pretitle'] = $this->load->view('parts/iconbar', $icondata, TRUE);
		
		$tpl['title'] = 'Groups';
		$tpl['pagetitle'] = 'Manage user groups';
		$tpl['body'] = $this->load->view('security/groups.index.php', NULL, TRUE);
		$this->load->view($this->tpl, $tpl);
	}
	
	
	
	
}


?>
