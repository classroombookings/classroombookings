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
 
class Auth extends CI_Driver_Library
{
	
	public $valid_drivers = array('auth_local', 'auth_ldap', 'auth_preauth');
	
	public $CI;
	
	//private $_db_user;
	//private $_cookie_salt;
	//private $_levels;
	//private $_settings;
	//private $_error_page;
	
	private $_permission_cache = NULL;
	
	public $reason;		// Reason for return failure from functions
	
	
	
	
	public function __construct()
	{
		$this->CI =& get_instance();
		
		// Load helpers/models required by the library
		$this->CI->load->helper('cookie');
		$this->CI->load->library('user_agent');
		$this->CI->lang->load('users');
	}
	
	
	
	
	/**
	 * Auth check function to see if a user has the appropriate privileges
	 *
	 * @param action The action the user is wanting to perform
	 */
	function check($action = '')
	{
		$u_id = (int) $this->CI->session->userdata('u_id');
		
		log_message('debug', "Auth: check($action): checking for User ID $u_id...");
		
		//print_r($this->_permission_cache);
		
		if ($this->_permission_cache === NULL)
		{
			// Permission cache on object for this request is empty.
			// Get/set DB cache
			$permissions = $this->CI->permissions_model->get_cache($u_id);
			
			log_message('debug', "Auth: check($action): permissions loaded from DB cache. Storing locally.");
			
			$this->_permission_cache = $permissions;
		}
		
		if (is_array($this->_permission_cache))
		{
			log_message('debug', "Auth: check($action): _permission_cache IS an array!");
			$check = in_array($action, $this->_permission_cache);
		}
		else
		{
			log_message('debug', "Auth: check($action): _permission_cache is not an array.");
			$check = FALSE;
		}
		
		log_message('debug', "Auth: check($action): Result: " . var_export($check, TRUE) . ".");
		
		return ($check === TRUE);
	}
	
	
	
	
	/**
	 * Restrict access to the page by checking user permission for the action
	 * and stopping execution if the check fails.
	 *
	 * @param int 
	 */
	public function restrict($action = '')
	{
		if ( ! $this->check($action))
		{
			// User not allowed for that action - do stuff
			
			// Get URI string requested so can redirect to it after successful login
			$this->CI->session->set_userdata('uri', $this->CI->uri->uri_string());
			
			$this->CI->load->library('user_agent');
			
			// User logged in? If not then they must at least login.
			// If yes, then they just don't have the necessary privileges.
			if ($this->is_logged_in() === FALSE)
			{
				$this->CI->flash->set('error', lang('AUTH_MUST_LOGIN'), TRUE);
				redirect('account/login');
			}
			else
			{
				$err1 = lang('AUTH_NO_PRIVS');
				$err2 = anchor($this->CI->agent->referrer(), 'Click here to go back.');
			}
			
			$error =& load_class('Exceptions', 'core');
			echo $error->show_error($err1, $err2);
			exit;
		}
	}
	
	
	
	
	/**
	 * Check a room permission for set user and room ID. (set_user(), set_room())
	 */
	function check_room($action = '', $rm_id = 0, $u_id = 0)
	{
		// Get permissions on given room for the user. TRUE if user is exempt, otherwise array.
		$perms = $this->CI->rooms_model->permission_check($u_id, $rm_id);
		
		// Allowed
		$can_do = array();
		
		if (is_array($perms))
		{
			foreach ($perms as $p)
			{
				$can_do[] = $p[1];
			}
			$perms = $can_do;
		}
		
		// Finally complete the check
		$check = (is_array($perms)) ? in_array($permission, $perms) : $perms;
		
		return $check;
		
	}
	
	
	
	
	/**
	 * Return array of all permissions that the user has on the set room
	 */
	function room_permissions($rm_id = 0, $u_id)
	{
		die("Move room_permissions() to permissions_model or something!");
		
		$perms = $this->CI->rooms_model->permission_check($rm_id, $u_id);
		
		// Allowed
		$can_do = array();
		
		if (is_array($perms))
		{
			foreach ($perms as $p)
			{
				$can_do[] = $p[1];
			}
			$perms = $can_do;
		}
		
		return $perms;
		
	}
	
	
	
	
	/**
	 * Function to authenticate a user in the database
	 * Also sets session data and cookie if required
	 *
	 * @param	string	username	Username
	 * @param	string	password	Password in either sha1 or plaintext
	 * @param	bool	remember	Whether or not to set the remember cookie (default is false)
	 * @param	bool	is_sha1		Is password already sha1	
	 * @return	bool
	 */
	function login($username = '', $password = '')
	{
		// Retrieve auth settings
		$ldap_enable = option('auth_ldap_enable');
		
		log_message('debug', "Auth: login($username): LDAP enabled: $ldap_enable");
		
		if (empty($username) || empty($password))
		{
			log_message('debug', "Auth: login($username): Empty username and password");
			$this->reason = 'Empty username and/or password.';
			return FALSE;
		}
		
		$user = $this->CI->users_model->get_by_username($username);
		
		log_message('debug', "Auth: login($username): User: " . var_export($user, TRUE));
		log_message('debug', "Auth: login($username): Query: " . $this->CI->db->last_query());
		
		
		// Early enough to check if their account is disabled. If it is, exit here.
		if ($user && $user['u_enabled'] == 0)
		{
			log_message('debug', "Auth: login($username): Account disabled.");
			$this->reason = 'Account not enabled';
			return FALSE;
		}
		
		// Default places to try to authenticate the user
		$try_local = TRUE;
		$try_ldap = FALSE;
		
		if ( ! $user || $user['u_auth_method'] === 'ldap')
		{
			// If no user, or their auth method is LDAP, then we should be checking LDAP.
			log_message('debug', "Auth: login($username): No user or their auth method is LDAP (setting try_ldap = true)");			
			$try_ldap = TRUE;
		}
			
		// If LDAP is enabled, and should try that for this user, do it.
		if ($ldap_enable === TRUE && $try_ldap === TRUE)
		{
			log_message('debug', "Auth: login($username): LDAP is enabled and trying LDAP for this user");
			
			// Don't try local auth unless this fails
			$try_local = FALSE;
			
			// We are using LDAP. First, send the supplied user and password to the ldap function.
			$auth_response = $this->ldap->auth($username, $password);
			
			if ($auth_response === TRUE)
			{
				
				log_message('debug', "Auth: login($username): LDAP response successful.");
				
				// Potentially, a new user could have been created here. Get latest info
				$user = $this->CI->users_model->get_by_username($username);
				$session = $this->create_session($user['u_id']);
				
				Events::trigger('user_login', array(
					'driver' => 'ldap',
					'user' => $user,
				));
				
				if ($session)
				{
					// Update last login datetime
					$this->CI->users_model->set_last_login($user['u_id']);
					
					// Set as an active user and get token that will identify this session
					$active_token = $this->CI->users_model->set_active($user['u_id']);
					log_message('debug', "Auth: login($username): New active entry - " . $this->CI->db->last_query());
					$this->CI->session->set_userdata('active_token', $active_token);
				}
				
				return $session;
			}
			else
			{
				log_message('debug', "Auth: login($username): LDAP response failure.");
				$this->reason = $this->ldap->reason;
				$try_local = TRUE;
			}
		}
		elseif($ldap_enable === FALSE && $try_ldap === TRUE)
		{
			log_message('debug', "Auth: login($username): User auth method is LDAP but LDAP not enabled.");
			$this->reason = 'LDAP is not enabled, but account settings require LDAP.';
			return FALSE;
		}
		
		// Not using LDAP or LDAP auth failed, so we look up a local user in the DB (trylocal should be TRUE now)
		
		if ($try_local === TRUE)
		{
			log_message('debug', "Auth: login($username): Local authentication attempt");
			
			$auth_response = $this->local->auth($username, $password);
			
			if ($auth_response === TRUE)
			{
				$session = $this->create_session($user['u_id']);
				
				Events::trigger('user_login', array(
					'driver' => 'local',
					'user' => $user,
				));
				
				if ($session)
				{
					// Update last login datetime
					$this->CI->users_model->set_last_login($user['u_id']);
					
					// Set as an active user and get token that will identify this session
					$active_token = $this->CI->users_model->set_active($user['u_id']);
					log_message('debug', "Auth: login($username): New active entry - " . $this->CI->db->last_query());
					$this->CI->session->set_userdata('active_token', $active_token);
				}
				
				return $session;
			}
			else
			{
				$this->reason = ($this->reason) ?: "Incorrect username and/or password";
				return FALSE;
			}
		}
		
		log_message('debug', "Auth: login($username): All options exhausted.");
		
		return FALSE;
	}
	
	
	
	
	public function preauth($data = array())
	{
		if ( ! $this->preauth->auth($data))
		{
			$this->reason = $this->preauth->reason;
			return FALSE;
		}
		
		// Pre-auth was successful!
		
		// Does the user exist?
		if ( ! $user = $this->CI->users_model->get_by_username($data['username']))
		{
			// Does not exist!
			
			// Should we create them?
			if ($data['create'] === 0)
			{
				$this->reason = 'auth_preauth_no_create';
				return FALSE;
			}
			
			// Yes create.
			$u_id = $this->CI->users_model->insert(array(
				'u_username' => $data['username'],
				'u_display' => $data['username'],
				'u_email' => $data['username'] . '@' . option('auth_preauth_email_domain'),
				'u_auth_method' => 'local',
				'u_enabled' => 1,
				'u_g_id' => option('auth_preauth_g_id'),
			));
			
			$user = $this->CI->users_model->get($u_id);
		}
		else
		{
			// They DO exist! o_O
			$u_id = $user['u_id'];
		}
		
		// Got a user now. Give them a session.
		
		$session = $this->create_session($u_id);
		
		Events::trigger('user_login', array(
			'driver' => 'preauth',
			'user' => $user,
		));
				
		if ($session)
		{
			// Update last login datetime
			$this->CI->users_model->set_last_login($user['u_id']);
			
			// Set as an active user and get token that will identify this session
			$active_token = $this->CI->users_model->set_active($user['u_id']);
			log_message('debug', "Auth: preauth($username): New active entry - " . $this->CI->db->last_query());
			$this->CI->session->set_userdata('active_token', $active_token);
		}
		
		return $session;
	}
	
	
	
	
	/**
	 * Create login session
	 *
	 * This function should only be called once the user has been validated via ldap/local/preauth.
	 * The user MUST exist - and it is presumed that they are enabled.
	 *
	 * @param string username
	 * @return bool
	 */
	function create_session($u_id = 0)
	{
		log_message('debug', 'Auth: create_session(): Creating session for User ID ' . $u_id);
		$this->CI->load->model('users_model');
		$user = $this->CI->users_model->get($u_id);
		
		if ($user)
		{
			// Academic year data for session
			$this->CI->load->model('years_model');
			$year = $this->CI->years_model->get_current();
			
			// All session data
			$session_data = array(
				'u_id' => $user['u_id'],
				'u_username' => $user['u_username'],
				'u_email' => $user['u_email'],
				'u_display' => $user['u_display'],
				'year_active' => $year['y_id'],
				'year_working' => $year['y_id'],
			);
			
			$this->CI->session->set_userdata($session_data);
			
			// Save permissions for this user to cache
			$this->CI->permissions_model->set_cache($u_id);
			
			// Delete some cookies that might have been left over that we don't want
			delete_cookie("cal_month");
			delete_cookie("cal_year");
			
			return TRUE;
		}
		else
		{
			// FAIL! User account is *probably*: 1) LDAP, but 2) Disabled
			log_message('debug', 'Auth: create_session(): Could not get user by ID');
			return FALSE;
		}
	}
	
	
	
	
	/**
	 * Logout function that clears all the session data and destroys it
	 *
	 * @return	bool
	 */	 	
	function destroy_session()
	{
		$u_id = $this->CI->session->userdata('u_id');
		$active_token = $this->CI->session->userdata('active_token');
		
		$this->CI->load->model(array('users_model', 'permissions_model'));
		
		// Housekeeping
		$this->CI->users_model->remove_active($u_id, $active_token);
		$this->CI->permissions_model->clear_cache($u_id);
		
		// Destroy session
		$this->CI->session->unset_userdata('u_id');
		$this->CI->session->sess_destroy();
		
		return ( ! $this->CI->session->userdata('u_id'));
	}
	
	
	
	
	/**
	 * Return if user is logged in or not
	 *
	 * @return bool
	 */
	function is_logged_in()
	{
		return ($this->CI->session->userdata('u_id'));
	}
	
	
	
	
	/**
	 * Require the user to be logged in.
	 *
	 * @param bool $redirect		Redirect to login page if not logged in
	 * @return void
	 */
	function require_logged_in($redirect = TRUE)
	{
		if ( ! $this->is_logged_in())
		{
			$this->CI->flash->set('error', lang('ERR_NOT_LOGGED_IN'));
			if ( $redirect ) redirect('account/login');
			return;
		}
	}
	
	
	
	
}


/* End of file ./application/libraries/Auth/Auth.php */