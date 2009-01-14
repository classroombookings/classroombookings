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
		$this->output->enable_profiler($this->config->item('profiler'));
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
			$remember = ($this->input->post('remember') == '1') ? TRUE : FALSE;
			
			$login = $this->auth->login($username, $password, $remember);
			
			if($login == TRUE){
				
				// Login successful, going to page
				$this->session->set_flashdata('flash', $this->msg->note($this->lang->line('AUTH_OK')));
				$uri = $this->session->userdata('uri');
				redirect(($uri != NULL) ? $uri : 'dashboard');
				
			} else {
				
				// Login failed
				//print $this->msg->err($this->lang->line('AUTH_FAIL_USERPASS'));
				$this->session->set_flashdata('flash', $this->msg->err($this->auth->lasterr, 'Authentication failure'));
				redirect("account/login");
				
			}
			
		}
		
	}
	
	
	
	
	function preauth(){
		
		// Retrive authentication settings
		$auth = $this->settings->get_all('auth');
		$ldap = ($auth->ldap == 1);
		
		// Create data array for the preauth function from URL data
		$url['username'] = $this->uri->segment(3);
		$url['timestamp'] = $this->uri->segment(4);
		$url['preauth'] = $this->uri->segment(5);
		
		// Do we create the user aswell?
		$create = ($this->uri->segment(6) == 'create') ? TRUE : FALSE;
		
		// Run preauth function
		$compare = $this->auth->preauth($url);
		
		$errtitle = sprintf("Pre-authentication failure for %s", $url['username']);
		
		if($compare == TRUE){
			
			// Comparison is true, preauth is legitimate
			
			// See if user exists
			if($this->auth->userexists($url['username'])){
				
				$session = $this->auth->session_create($url['username'], FALSE);
				
				if($session == TRUE){
					// Login is successful, redirect to dashboard
					redirect('dashboard');
				} else {
					// Can't login. Most likely reason is that their account is disabled
					$this->msg->fail($errtitle, 'Could not login with the supplied username. Account disabled? <br />' . $this->auth->lasterr);
				}
				
			} else {
				
				// User does not exist!
				// Now we have two possibilities - we create them (thus allowing them access), or we don't.
				
				if($create == TRUE){
					
					// Going to create user
					
					#die("That user doesn't exist, but you asked for them to be created.");
					$data = array();
					$data['username'] = $url['username'];
					$data['displayname'] = $url['username'];
					$data['group_id'] = $auth->preauthgroup_id;
					$data['enabled'] = 1;
					$data['ldap'] = $auth->ldap;
					$data['password'] = NULL;
					
					// Add user
					$add = $this->security->add_user($data);
					
					if($add == TRUE){
						
						// Added the user, now we can log them in
						$session = $this->auth->session_create($data['username'], FALSE);
						if($session == TRUE){
							// Login is successful, redirect to dashboard
							redirect('dashboard');
						} else {
							// Can't login. Most likely reason is that their account is disabled
							$this->msg->fail($errtitle, 'Could not login with the supplied username. Account disabled? <br />' . $this->auth->lasterr);
						}
						
					} else {
						
						// Failed to add the user
						$this->lasterr = $this->CI->security->lasterr;
						return FALSE;
						
					}
					
				} else {
					
					// User doesn't exist, and they don't want accounts to be created automatically
					// This means the username we're preauthing with does not exist
					$this->msg->fail($errtitle, 'The username does not exist.');
					
				}
				
			}
			
		} else {
			
			// Fail if preauth is a load of crap
			#echo "Preauth failed for user {$uri['username']}";
			$this->msg->fail($errtitle, $this->auth->lasterr);
			
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




/* End of file app/controllers/account.php */