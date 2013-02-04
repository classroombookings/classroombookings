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
		$this->data['nav_current'] = array('account');
		parent::__construct();
	}
	
	
	
	
	function index()
	{
		$this->auth->require_logged_in();
		
		$title = 'My Account';
		
		$u_id = $this->session->userdata('u_id');
		
		$body = "User ID: $u_id\n\n";
		
		$user_roles = $this->roles_model->for_user($u_id);
		
		$body .= var_export($user_roles, true);
		
		foreach ($user_roles as $r)
		{
			$roles[] = $r['r_id'];
		}
		
		$body .= var_export($roles, true);
		
		$permissions = $this->permissions_model->for_role($roles);
		
		$body .= var_export($permissions, true);
		
		//$body .= $this->permissions_model->lasterr;
		
		$data['body'] = $body;
		
		//print_r($data);
		
		//$this->page($data);
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
	 */
	function login()
	{
		if ($this->auth->is_logged_in())
		{
			redirect('account');
		}
		
		if ($this->input->post())
		{
			// Validation rules for login form
			$this->form_validation->set_rules('username', 'Username', 'required|max_length[104]');
			$this->form_validation->set_rules('password', 'Password', 'required|max_length[104]');
			
			$username = $this->input->post('username');
			$password = $this->input->post('password');
				
			if ($this->form_validation->run() && $this->auth->login($username, $password))
			{
				// Login successful
				$this->flash->set('success', lang('auth_login_success'), TRUE);
				redirect(($this->session->userdata('uri')) ?: 'home');
			}
			else
			{
				// Login failed
				if ( ! empty($this->auth->reason))
				{
					$this->flash->set('error', $this->auth->reason);
				}
			}
		}
		
		$this->layout->set_title(lang('login'));
	}
	
	
	
	
	/**
	 * Pre-authentication page to log users in
	 */
	function preauth()
	{
		// Gather data from query string
		$data = array(
			'username' => $this->input->get('u'),
			'timestamp' => (int) $this->input->get('ts'),
			'create' => (int) $this->input->get('create'),
			'preauth' => $this->input->get('preauth'),
		);
		
		// Run preauth function
		$compare = $this->auth->preauth($data);
		
		if ($compare)
		{
			// Login successful
			$this->flash->set('success', lang('auth_login_success'), TRUE);
			
			// Go here when logged in
			$uri = $this->input->get('uri') ? $this->input->get('uri') : 'home';
			redirect($uri);
		}
		else
		{
			$this->flash->set('error', lang($this->auth->reason), TRUE);
			redirect('account/login');
		}
		
		return;
		
	}
	
	
	

	function logout($status = 0)
	{
		$this->view = FALSE;
		
		if ($status == 1)
		{
			$this->flash->set('success', lang('auth_logout_success'), TRUE);
			redirect("account/login");
		}
		
		$this->auth->destroy_session();
		redirect('account/logout/1');
	}
	
	
	
	
	function view()
	{
		$user_id = $this->uri->segment(3);
		$tpl['title'] = 'View Account Profile';
		$tpl['pagetitle'] = $tpl['title'] . ' ('.$user_id.')';
		$this->load->view($this->tpl, $tpl);
	}
	
	
	
	
}




/* End of file app/controllers/account.php */