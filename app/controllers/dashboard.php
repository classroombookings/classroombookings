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


class Dashboard extends Controller {


	var $tpl;
	

	function Dashboard(){
		parent::Controller();
		$this->tpl = $this->config->item('template');
	}
	
	
	
	
	function index(){
		#$this->auth->check('dashboard');
		$tpl['title'] = 'Dashboard';
		$tpl['pagetitle'] = $tpl['title'];
		if($this->auth->logged_in()){
			$tpl['body'] = $this->load->view('dashboard/index', NULL, TRUE);
		} else {
			$tpl['body'] = 'You are not currently logged in.';
		}
		$this->load->view($this->tpl, $tpl);
	}
	
	
	
	
	function error(){
		$tpl['title'] = 'An error occured';
		$tpl['body'] = '';
		$this->load->view($this->tpl, $tpl);
	}
	
	
	
	
}


?>