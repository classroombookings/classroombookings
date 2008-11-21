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
		if($this->auth->logged_in()){
			$tpl['body'] = 'You are already logged in.';
		} else {
			$tpl['body'] = $this->load->view('account/login', NULL, TRUE);
		}
		$this->load->view($this->tpl, $tpl);
	}
	
	
	
	
	/*
	 * Process login form
	 */
	function loginsubmit(){
		// Validation rules for login form
		$this->form_validation->set_rules('username', 'Username', 'required|max_length[30]');
		$this->form_validation->set_rules('password', 'Password', 'required|max_length[30]');
		$this->form_validation->set_error_delimiters('<li>', '</li>');
		
		// Check validation first
		if ($this->form_validation->run() == FALSE){
			// Failed validation - send back to login page to show errors
			return $this->login();
		} else {
			// Get form values
			$username = $this->input->post('username');
			$password = $this->input->post('password');
			
			$login = $this->auth->login($username, $password, FALSE);
			if($login == TRUE){
			
				// Login successful, going to page
				$this->session->set_flashdata('flash', $this->msg->info($this->lang->line('AUTH_OK')));
				redirect("account");
			
			} else {
				
				// Login failed
				//print $this->msg->err($this->lang->line('AUTH_FAIL_USERPASS'));
				$this->session->set_flashdata('flash', $this->msg->err($this->lang->line('AUTH_FAIL_USERPASS'), 'Authentication failure'));
				redirect("account/login");
				
			}
			
		}
		
	}
	
	
	

	function logout(){
		/*$tpl['title'] = 'Logout';
		$tpl['pagetitle'] = $tpl['title'];
		$this->load->view($this->tpl, $tpl);*/
		$logout = $this->auth->logout();
		if($logout == TRUE){
			$this->session->set_flashdata('flash', $this->msg->info($this->lang->line('AUTH_LOGOUT_OK')));
			redirect("account/login");
		} else {
			$this->session->set_flashdata('flash', $this->msg->err($this->lang->line('AUTH_LOGOUT_FAIL')));
			redirect("account/login");
		}
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