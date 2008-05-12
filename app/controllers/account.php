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


class Account extends Controller {


	var $tpl;
	

	function Account(){
		parent::Controller();
		$this->tpl = $this->config->item('template');
	}
	
	
	
	
	function index(){
		$tpl['title'] = 'My Account';
		$tpl['pagetitle'] = $tpl['title'];
		$this->load->view($this->tpl, $tpl);
	}
	
	
	
	function login(){
		$tpl['title'] = 'Login';
		$tpl['pagetitle'] = $tpl['title'];
		$tpl['body'] = $this->load->view('account/login', NULL, TRUE);
		$this->load->view($this->tpl, $tpl);
	}
	
	
	

	function logout(){
		$tpl['title'] = 'Logout';
		$tpl['pagetitle'] = $tpl['title'];
		$this->load->view($this->tpl, $tpl);
	}
	
	
	
	function view(){
		$user_id = $this->uri->segment(3);
		$tpl['title'] = 'View Account Profile';
		$tpl['pagetitle'] = $tpl['title'] . ' ('.$user_id.')';
		$this->load->view($this->tpl, $tpl);
	}
	
	
	
	
	function bookings(){
		$tpl['title'] = 'My Bookings';
		$tpl['pagetitle'] = $tpl['title'];
		$this->load->view($this->tpl, $tpl);
	}
	
	
	
	
}


?>