<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Classroombookings. Hassle-free resource booking for schools. <http://classroombookings.com/>
 * Copyright (C) Craig A Rodway <craig.rodway@gmail.com>
 *
 * Licensed under the Open Software License version 3.0
 * 
 * This source file is subject to the Open Software License (OSL 3.0) that is
 * bundled with this package in the files license.txt. It is also available 
 * through the world wide web at this URL:
 * http://opensource.org/licenses/OSL-3.0
 */

class Account extends MY_Controller
{
	
	
	function __construct()
	{
		parent::__construct();
		$this->data['nav_current'] = array('account');
	}
	
	
	
	
	function index()
	{
		$title = 'My Account';
		
		$user_id = $this->session->userdata('user_id');
		
		$body = "User ID: $user_id\n\n";
		
		$user_roles = $this->permissions_model->get_user_roles($user_id);
		
		$body .= var_export($user_roles, true);
		
		foreach ($user_roles as $r)
		{
			$roles[] = $r->role_id;
		}
		
		$body .= var_export($roles, true);
		
		$permissions = $this->permissions_model->get_role_permissions($roles);
		
		$body .= var_export($permissions, true);
		
		$body .= $this->permissions_model->lasterr;
		
		$data['body'] = $body;
		
		
		$this->page($data);
	}
	
	
	
	
	function activebookings()
	{
		$data['title'] = 'My Active Bookings';
		$this->page($data);
	}
	
	
	
	
	function previousbookings()
	{
		$data['title'] = 'My Active Bookings';
		$this->page($data);
	}
	
	
	
	
	function changepassword()
	{
		$data['title'] = 'Change password';
		$this->page($data);
	}
	
	
	
	
	/**
	 * Account login page
	 *
	 * @param string $logged_out Set to ok|fail to show status of logout action
	 */
	function login($logged_out = FALSE)
	{
		if ($this->auth->logged_in())
		{
			redirect('account');
		}
		
		if ($this->input->post())
		{
			// Validation rules for login form
			$this->form_validation->set_rules('username', 'Username', 'required|max_length[104]');
			$this->form_validation->set_rules('password', 'Password', 'required|max_length[104]');
			$this->form_validation->set_error_delimiters('<li>', '</li>');
			
			$username = $this->input->post('username');
			$password = $this->input->post('password');
				
			if ($this->form_validation->run() && $this->auth->login($username, $password))
			{
				// Login successful
				$this->msg->flash('notice', lang('AUTH_OK'));
				$uri = ($this->session->userdata('uri')) ?: 'home';
				redirect($uri);
			}
			else
			{
				// Login failed
				flash('error', 'Error: ' . $this->auth->error_msg);
			}
		}
		
		$this->data['title'] = lang('W_LOGIN');
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
				
				if($this->auth->loggedin() == TRUE){
					$this->auth->logout();
				}
				
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
	
	
	

	function logout()
	{
		$this->session->keep_flashdata('flash');
		$logout = $this->auth->logout();
		$logged_out = ($logout == true) ? 'ok' : 'fail';
		redirect("account/login/$logged_out");
	}
	
	
	
	
	function view(){
		$user_id = $this->uri->segment(3);
		$tpl['title'] = 'View Account Profile';
		$tpl['pagetitle'] = $tpl['title'] . ' ('.$user_id.')';
		$this->load->view($this->tpl, $tpl);
	}
	
	
	
	
}




/* End of file app/controllers/account.php */