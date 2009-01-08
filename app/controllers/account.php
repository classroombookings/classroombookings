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
				$this->session->set_flashdata('flash', $this->msg->err($this->lang->line('AUTH_FAIL_USERPASS'), 'Authentication failure'));
				redirect("account/login");
				
			}
			
		}
		
	}
	
	
	
	
	function preauth(){
		
		/* $preauthkey = $this->settings->get('preauthkey', 'auth');
		
		$uri['username'] = $this->uri->segment(3);
		$uri['timestamp'] = $this->uri->segment(4);
		$uri['finalpreauth'] = $this->uri->segment(5);
		
		
		if(in_array(FALSE, $uri)){
			die("Fail. One or more values not present.");
		}
		
		$timestamp = now();
		$time_5before = strtotime("-5 minutes");
		$time_5after = strtotime("+5 minutes");
		
		if( ($uri['timestamp'] < $time_5before) OR ($uri['timestamp'] > $time_5after) ){
			die("Fail. Timestamp falls outside of 5 minutes.");
		}
		
		$expected_final = "{$uri['username']}|{$uri['timestamp']}|{$preauthkey}";
		$expected_final = sha1($expected_final);
		$compare = ($expected_final == $uri['finalpreauth']); */
		
		// Create data array for the preauth function
		$data['username'] = $this->uri->segment(3);
		$data['timestamp'] = $this->uri->segment(4);
		$data['preauth'] = $this->uri->segment(5);
		
		// Do we create the user aswell?
		$create = ($this->uri->segment(6) == 'create') ? TRUE : FALSE;
		
		// Run preauth function
		$compare = $this->auth->preauth($data);
		
		$errtitle = sprintf("Pre-authentication failure for %s", $data['username']);
		
		if($compare == TRUE){
			
			// Comparison is true, preauth is legitimate
			
			// See if user exists
			if($this->auth->userexists($data['username'])){
				
				// User exists - retrieve their password to login with
				$sql = 'SELECT password FROM users WHERE username = ? LIMIT 1';
				$query = $this->db->query($sql, array($data['username']));
				if($query->num_rows() == 1){
					// Got password
					$row = $query->row();
					$password = $row->password;
					
					// Attempt login with the password
					$login = $this->auth->login($data['username'], $password, FALSE);
					
					if($login == TRUE){
						// Login is successful, redirect to dashboard
						redirect('dashboard');
					} else {
						// Can't login. Most likely reason is that their account is disabled
						$this->msg->fail($errtitle, 'Could not login with the supplied username. Account disabled?');
					}
				} else {
					// No results from database for that user. Very odd, considering userexists() returned TRUE
					#die("Fail. Can't find that user in the DB even though they exist. Hmmmm.... ?");
					$this->msg->fail($errtitle, 'Could not find that username in the database even though they appear to exist.');
				}
				
			} elseif( $create == TRUE ){
				
				
				die("That user doesn't exist, but you asked for them to be created.");
				
			} else {
				// User doesn't exist, and they don't want accounts to be created automatically
				#die("Fail. That user doesn't even exist");
				$this->msg->fail($errtitle, 'The username does not exist.');
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


?>