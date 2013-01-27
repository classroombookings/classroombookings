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
	 * Pre-authentication handling feature
	 *
	 * @param array Data array. Must contain keys and values of: username, timestamp, preauth
	 */
	function preauth($data)
	{
		
		// Check for username
		if (!isset($data['username']))
		{
			$this->lasterr = 'No username supplied.';
			return FALSE;
		}
		if (!isset($data['timestamp']))
		{
			$this->lasterr = 'No timestamp supplied.';
			return FALSE;
		}
		if (!isset($data['preauth']))
		{
			$this->lasterr = 'No computed preauth supplied.';
			return FALSE;
		}
		
		// Work out current time and the tolerances/threshold
		$timestamp = now();
		$time_lower = strtotime("-5 minutes");
		$time_upper = strtotime("+5 minutes");
		
		// Check if the supplied timestamp is within the allowed threshold
		if ( ($data['timestamp'] < $time_lower) OR ($data['timestamp'] > $time_upper) )
		{
			$this->lasterr = 'Supplied timestamp falls outside of the allowed threshold of 5 minutes.';
			return FALSE;
		}
		
		// Get the current key from the database
		$preauthkey = $this->CI->settings->get('auth_preauth_key');
		
		// Work out what we *should* get based on their info + our preauthkey
		$expected_final = sha1("{$data['username']}|{$data['timestamp']}|{$preauthkey}");
		
		// Finally we compare our correct result with their result
		$compare = ($expected_final == $data['preauth']);
		
		if ($compare == false){ $this->lasterr = 'Key did not match the expected value.'; }
		
		return $compare;
	}
	
	
	
	
	/**
	 * Local authentication function
	 *
	 * Simply checks if a local user's password is valid
	 *
	 * @param	string	username
	 * @param	string	password
	 * @return	bool
	 */
	/*
	function auth_local($username, $password){
		
		$sql = 'SELECT password 
				FROM users
				WHERE username = ?
				AND enabled = 1
				AND ldap = 0
				LIMIT 1';
				
		$query = $this->CI->db->query($sql, array($username));
		
		if ($query->num_rows() == 1)
		{
			
			$user = $query->row();
			$match = $this->check_password($password, $user->password);
			if ($match == true)
			{
				return true;
			}
			else
			{
				$this->lasterr = 'Local authentication failure. Incorrect username and/or password.';
				return false;
			}
			
		}
		else
		{
			// Fail
			$this->lasterr = 'Local authentication failure. Incorrect username and/or password.';
		}
		
	}
	*/
	
	
	
	
	/**
	 * LDAP authenticate function
	 *
	 * Checks the configured LDAP server for valid supplied credentiales.
	 * Optionally will update local DB with LDAP display/email info.
	 * This should not be called for users who authenticate locally.
	 *
	 * @param	string	username
	 * @param	string	password
	 * @param	bool	updateinfo		Update the local DB with info from LDAP or not
	 * @return	mixed	local user_id on success, FALSE on failure
	 */
	/*
	function auth_ldap($username, $password){
		
		if(!function_exists('ldap_bind')){
			$this->lasterr = 'It appears that the PHP LDAP module is not installed - cannot continue.';
			return FALSE;
		}
		
		// Retrieve auth settings
		$auth = $this->_CI->settings->get('auth.');
		
		// See if the user exists at all
		$userexists = $this->userexists($username);
		
		// Set values
		$ldaphost = $auth->ldaphost;
		$ldapport = $auth->ldapport;
		$ldapbase = $auth->ldapbase;
		$ldapfilter = str_replace("%u", $username, $auth->ldapfilter);
		$ldaploginupdate = ($auth->ldaploginupdate == 1) ? TRUE : FALSE;
		$ldapusername = 'cn=' . $username;
		
		// Attempt connection to server
		$connect = ldap_connect($ldaphost, $ldapport);
		if(!$connect){
			$this->lasterr = sprintf('Failed to connect to LDAP server %s on port %d.', $ldaphost, $ldapport);
			return FALSE;
		}
		
		// Now go through the DNs and see if we can bind as the user in them
		$dns = explode(";", $ldapbase);
		$found = FALSE;
		foreach($dns as $dn){
			if($found == FALSE){
				$thisdn = trim($dn);
				$bind = @ldap_bind($connect, "$ldapusername,$thisdn", $password);
				if($bind){ 
					$correctdn = $thisdn;
					$found = TRUE;
				}
			}
		}
		
		// Check if user in a DN has been found
		if($found == FALSE){
			// Password could be wrong.
			$this->lasterr = 'LDAP authentication failure. Check details and try again.';
			return FALSE;
		}
		
		// search for details
		$search = ldap_search($connect, $correctdn, $ldapfilter);
		if(!$search){
			// LDAP query filter is probably incorrect.
			$this->lasterr = "LDAP authentication failure. Query filter did not return any results.";
			return FALSE;
		}
		
		// Get info
		$info = ldap_get_entries($connect, $search); 
		$user['username'] = $username;
		$user['displayname'] = $info[0]['displayname'][0];
		$user['email'] = $info[0]['mail'][0];
		$user['memberof'] = $info[0]['memberof'];
		$user['group_ids'] = array();
		
		// Succeeded with all info
		
		// If user already exists and we don't want to update at login, complete the auth now.
		if($userexists == TRUE && $ldaploginupdate == FALSE){
			return TRUE;
		}
		
		
		//
		//	... otherwise, add if they dont exist; and update if they do.
		//	... either way, we need to fetch some data. Do that now...
		//
		
		// Get group mappings
		unset($info[0]['memberof']['count']);
		// Mapping of ldapgroupnames => localgroupid
		$groupmap = $this->_CI->security->ldap_groupname_to_group();
		// Make new array to hold the group names that the user belongs to
		$groups = array();		
		
		// iterate the groups they are member of to find potential local group
		foreach($info[0]['memberof'] as $group){
			// We only need the CN= part
			$grouparray = explode(',', $group);
			$group = str_replace('CN=', '', $grouparray[0]);
			if(array_key_exists($group, $groupmap)){
				// Put possible group IDs into an array
				array_push($user['group_ids'], $groupmap[$group]);
			}
			// Stick this group into the group array
			array_push($groups, $group);
		}
		
		// Remove any duplicates (not sure this actually has a purpose as they should all be unique anyway)
		$user['group_ids'] = array_unique($user['group_ids']);	#, SORT_NUMERIC);
		
		// LDAP-TO-LOCAL: Find departments (using the previously-populated array)
		$user['department_ids'] = $this->_CI->security->ldap_groupnames_to_departments($groups);
		
		// Now the data array that has all correct info for sending to the DB
		
		// Set group ID of user (to the ldap mapping if unique, otherwise the default)
		$data['group_id'] = (count($user['group_ids']) == 1) ? $user['group_ids'][0] : $auth->ldapgroup_id;
		// Find departments we should assign the user to
		$data['departments'] = $user['department_ids'];		
		// Now the array of info for the user adding function
		$data['username'] = $user['username'];
		$data['displayname'] = (isset($user['displayname']) OR $user['displayname'] != '') ? $user['displayname'] : $user['username'];
		$data['email'] = $user['email'];
		$data['ldap'] = 1;
		$data['password'] = NULL;
		
		
		//
		//	At this point, we have authenticated and we need to know if we should update
		//	user details or add them as a new user.
		//
		
		if($userexists == TRUE){
			
			// Already in
			
			$sql = 'SELECT user_id FROM users WHERE username = ? LIMIT 1';
			$query = $this->_CI->db->query($sql, array($username));
			$row = $query->row();
			$user_id = $row->user_id;
			
			// We should only get here if loginupdate is true anyway, but here goes...
			if($ldaploginupdate == TRUE){
				$edit = $this->_CI->security->edit_user($user_id, $data);
				if($edit == FALSE){
					$this->lasterr = $this->_CI->security->lasterr;
					return FALSE;
				}
			} else {
				$this->lasterr = 'Expected ldaploginupdate to be TRUE but got FALSE instead';
				return FALSE;
			}
			
		} elseif($userexists == FALSE){
			
			// Add
			$data['enabled'] = 1;
			$add = $this->_CI->security->add_user($data);
			if($add == FALSE){
				$this->lasterr = $this->_CI->security->lasterr;
				return FALSE;
			}
			
		}
		
		return TRUE;
		
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
	
	
	
	/*
	function active_users()
	{
		
		$sql = 'SELECT users.user_id, users.username, users.displayname, usersactive.timestamp
				FROM users
				RIGHT JOIN usersactive ON users.user_id = usersactive.user_id';
		$query = $this->CI->db->query($sql);
		
		$result = $query->result();
		$activeusers = array();
		
		foreach($result as $user){
			$display = ($user->displayname != '' OR $user->displayname != NULL) ? $user->displayname : $user->username;
			//array_push($activeusers, $display);
			$activeusers[$user->user_id] = $display;
		}
		
		return $activeusers;
		
	}
	*/
	
	
	
	
}


/* End of file app/libraries/Auth.php */