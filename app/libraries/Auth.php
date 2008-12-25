<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
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


class Auth{


	var $CI;
	var $dbuser;
	var $lasterr;
	var $cookiesalt;
	var $levels;
	var $settings;
	var $errpage;


	function Auth(){
		// Load original CI object to global CI variable
		$this->CI =& get_instance();
		
		// Load helpers/models required by the library
		$this->CI->load->helper('cookie');
		$this->CI->load->model('security');
		
		// Cookie salt for hash - can be any text string
		$this->cookiesalt = 'CL455R00Mb00k1ng5';
		
		// Redirect to this error page
		$this->errpage = 'dashboard/error';
		
		// Get LDAP settings
		#$this->CI->load->library('settings');	//
		
		#$this->settings['ldap'] = $this->CI->settings->getldap();
		
		

	}
	
	
	
	
	function check($action, $return = FALSE){
		// Get group ID
		$group_id = $this->CI->session->userdata('group_id');
		// If no group, then guest group (always 0)
		$group_id = (int)($group_id === FALSE) ? 0 : $group_id;
		
		// Hopefully speed up access by putting the group permissions into the session
		// instead of additional DB lookups each time we run the check() function.
		if(!$this->CI->session->userdata('group_permissions')){
			// Get the group permissions for the user's group
			$group_permissions = $this->CI->security->get_group_permissions($group_id);
			$this->CI->session->set_userdata('group_permissions', $group_permissions);
		} else {
			$group_permissions = $this->CI->session->userdata('group_permissions');
		}
		
		// See if this action is in the permissions array for the user
		if(is_array($group_permissions)){
			$check = in_array($action, $group_permissions);
		} else {
			$check = FALSE;
		}
		
		// Return true/false if we only want the return value
		if($return == TRUE){
			return ($check == FALSE) ? FALSE : TRUE;
		}
		// Otherwise, error if failed check ...
		
		if($check == FALSE){
			// User is not allowed for that action - do stuff
			
			// Get the URI string they requested so can redirect to it after successful login
			$this->CI->session->set_userdata('uri', $this->CI->uri->uri_string());
			
			$this->CI->load->library('user_agent');
			
			// User logged in? If not then they must at least login.
			// If yes, then they just don't have the necessary privileges.
			if($this->logged_in() == FALSE){
				$this->lasterr = $this->CI->lang->line('AUTH_MUST_LOGIN');
				$this->lasterr2 = anchor('account/login', 'Click here to login.');
			} else {
				$this->lasterr = $this->CI->lang->line('AUTH_NO_PRIVS');
				$this->lasterr2 = anchor($this->CI->agent->referrer(), 'Click here to go back.');
			}
			
			$error =& load_class('Exceptions');
			echo $error->show_error($this->lasterr, $this->lasterr2);
			exit;
			
		}
	}
	
	
	
	
	/**
	 * Login user via a cookie
	 *
	 * Cookie key is stored in DB against a user and is selected to retrieve the user info.
	 * It is then passed to the login() function
	 *
	 * @param	string	key		Cookie key which should be a SHA1 hash
	 * @return	bool
	 */
	function cookielogin($key = NULL){
		// Check to see if key was supplied
		if($key == NULL){
			
			// No cookie key supplied, fatal!
			$this->lasterr = $this->CI->load->view('msg/err', 'Error with login cookie.', TRUE);
			$ret = FALSE;
			
		} else {
		
			// Got a key, now to see if it is correct format.
			
			if(strlen($key) != 40){
				
				// Is not valid key
				$this->lasterr = $this->CI->load->view('msg/err', 'Cookie key has incorrect length.', TRUE);
				$ret = FALSE;
				
			} else {
				
				// Got cookie key! hopefully should be in the DB
				$sql = 'SELECT user_id,username,password,lastlogin 
						FROM users 
						WHERE cookiekey = ? 
						LIMIT 1';
				// Run query
				$query = $this->CI->db->query($sql, array($key));
				
				// Check to see how many rows we got from selecting via the cookie key
				if($query->num_rows() == 1){
				
					// Ok, got user!
					$userinfo = $query->row();
					
					#echo var_export($userinfo);
					
					// Generate original cookie key hash to compare to
					$cookiekey = sha1(implode("", array(
						$this->cookiesalt,
						$userinfo->user_id, 
						$userinfo->username,
						$userinfo->lastlogin,
					)));
					
					// Compare hash
					if($cookiekey == $key){
						
						// Matched! We can now log the user in and set the remember-me option again
						$login = $this->login($userinfo->username, $userinfo->password, TRUE);
						$ret = $login;
						
					} else {
					
						// Did not match!
						$this->lasterr = $this->CI->load->view('msg/err', 'Invalid cookie (did not match database entry). Did you log in from another computer?<br />Compare '.$cookiekey.' to '.$key.'.', TRUE);
						$ret = FALSE;
					
					}
					
				} else {
				
					// No rows returned from the DB with that cookie key
					$lasterr = $this->CI->load->view('msg/err', 'Could not find your cookie in the database. Did you log in from another computer?', TRUE);
					$ret = FALSE;
					
				}		// End of num_rows() check
				
			}		// End of strlen() check on cookie key
			
		}		// End of key == NULL check
		
		$this->CI->session->set_flashdata('msg', $this->lasterr);
		if($ret == FALSE){
			redirect($this->errpage);
		}
					
	}
	
	
	
	
	/**
	 * Function to authenticate a user in the database
	 * Also sets session data and cookie if required
	 *
	 * @param	string	username	Username
	 * @param	string	password	Password in either sha1 or plaintext
	 * @param	bool	remember	Whether or not to set the remember cookie (default is false)
	 * @return	bool
	 */
	function login($username, $password, $remember = FALSE){
	
		if($username != NULL && $password != NULL){
		
			// SHA1 password if not already (passwords are <= 30chars, SHA1 is == 40chars)
			if(strlen($password) != 40){
				$password = sha1($password);
			}
			
			// Query to pick the user
			$sql = 'SELECT user_id, group_id, username, displayname AS display
					FROM users
					WHERE username = ? 
					AND password = ? 
					AND enabled = 1 
					LIMIT 1';
			
			// Run query
			$query = $this->CI->db->query($sql, array($username, $password));
			
			// Number of rows returned from the query - 1 row success, 0 rows failure
			$rows = $query->num_rows();
			
			// Now to check if username and password matched
			if($rows == 1){
			
				// Username/password combination matched - first get user info
				$userinfo = $query->row();
				
				// Update the DB's last login time (now)..
				$timestamp = mdate('%Y-%m-%d %H:%i:%s');
				$sql = 'UPDATE users 
						SET lastlogin = ? 
						WHERE user_id = ?';
				$this->CI->db->query($sql, array($timestamp, $userinfo->user_id));
				
				// Create session data array
				$sessdata['user_id']		= $userinfo->user_id;
				$sessdata['group_id']		= $userinfo->group_id;
				$sessdata['username']		= $userinfo->username;
				$sessdata['display']		= ($userinfo->display == NULL) ? $userinfo->username : $userinfo->display;
				//$sessdata['authlevel']		= $userinfo->authlevel;
				
				// Set session 
				#$this->CI->session->set_userdata(array());
				#$this->CI->session->destroy();
				$this->CI->session->set_userdata($sessdata);
				
				// Now set remember-me cookie if we need to
				if($remember == TRUE){
					// Generate hash
					$cookiekey = sha1(implode("", array(
						$this->cookiesalt,
						$userinfo->user_id, 
						$userinfo->username,
						$timestamp,
					)));
					// Set cookie data
					$cookie['expire'] = 60 * 60 * 24 * 14;		// 14 days
					
					$cookie['name'] = 'key';
					$cookie['value'] = $cookiekey;
					set_cookie($cookie);
					$cookie['name'] = 'user_id';
					$cookie['value'] = $userinfo->user_id;
					set_cookie($cookie);
					
					// Update DB table with the hash that we check on return visit
					$sql = 'UPDATE users 
							SET cookiekey = ? 
							WHERE user_id = ?';
					$query = $this->CI->db->query($sql, array($cookiekey, $userinfo->uid));
				}
				
				// Return value
				$ret = TRUE;
			
			} else {
			
				// Username/password combination didnt match = wrong password
				$this->lasterr = "Incorrect username and/or password";
				$ret = FALSE;
				
			}
			
		} else {
		
			// No username and password supplied
			$this->lasterr = "No username and/or password supplied to Auth library.";
			$ret = FALSE;
			
		}
		
		// End of function, return value
		return $ret;
		
	}
	
	
	
	
	/**
	 * Logout function that clears all the session data and destroys it
	 *
	 * @return	bool
	 */	 	
	function logout(){
		
		// Set session data to NULL (include all fields!)
		$sessdata['user_id'] = NULL;
		$sessdata['group_id'] = NULL;
		$sessdata['username'] = NULL;
		$sessdata['display'] = NULL;
		$sessdata['group_permissions'] = NULL;
		
		// Set empty session data
		$this->CI->session->unset_userdata($sessdata);
		
		// Destroy session
		$this->CI->session->sess_destroy();
		
		// Remove cookies too
		delete_cookie("key");
		delete_cookie("user_id");
		
		// Verify session has been destroyed by retrieving info 
		return ($this->CI->session->userdata('user_id') == FALSE) ? TRUE : FALSE;
		
	}
	
	
	
	
	function ldap_auth($username, $password){
		//$servername = "ldap://bbs-svr-001";  //IP/Name to the LDAP server
		//$ldaprdn  = 'cn='.$username.',ou=teaching Staff,ou=bbs,ou=establishments,dc=bbarrington,dc=internal';
		
		$ldapconn = ldap_connect($servername);

		// Check connection first
		if($ldapconn){
		
			$ldapauth = FALSE;
			
			// Connection OK - loop through our possible DNs
			foreach($ldaprdns as $rdn){
				$ldapbind = ldap_bind($ldapconn, $rdn, $pass);// may have multiple bind statements depending on the tree structure of the LDAP OU
				if ($ldapbind){
					$ldapauth = TRUE;
				}
			}
			
			if($ldapauth == TRUE){
				$ret = TRUE;
			} else {
				$ret = FALSE;
				$this->err = "LDAP server rejected username and/or password";
			}

		} else {
			$ret = FALSE;
			$this->err = "LDAP connection failed";
		}
		
		return $ret;
		
	}
	
	
	
	
	function get_ldap_groups($search = "*", $sorted = TRUE){
		
		
		
		//perform the search and grab all their details
		$filter = "(&(objectCategory=group)(samaccounttype=". ADLDAP_SECURITY_GLOBAL_GROUP .")(cn=".$search."))";
		$fields=array("samaccountname","description");
		$sr=ldap_search($this->_conn,$this->_base_dn,$filter,$fields);
		$entries = ldap_get_entries($this->_conn, $sr);

		$groups_array = array();		
		for($i=0; $i<$entries["count"]; $i++)
		{
			if($include_desc && strlen($entries[$i]["description"][0]) > 0)
			{
				$groups_array[ $entries[$i]["samaccountname"][0] ] = $entries[$i]["description"][0];
			}
			elseif($include_desc)
			{
				$groups_array[ $entries[$i]["samaccountname"][0] ] = $entries[$i]["samaccountname"][0];
			}
			else
			{
				array_push($groups_array, $entries[$i]["samaccountname"][0]);
			}
		}
		if($sorted)
		{
			asort($groups_array);
		}
		return ($groups_array);
	}
	
	
	
	
	/**
	 * Check if a user exists
	 *
	 * @param string Username to check
	 * @return bool
	 */
	function userexists($username){
		$sql = 'SELECT uid FROM userinfo WHERE username = ? LIMIT 1';
		$query = $this->CI->db->query($sql, array($username));
		return ($query->num_rows() == 1) ? TRUE : FALSE;
	}
	
	
	
	
	/**
	 * Check if an email address is already used in the DB for any user
	 *
	 * @param string Email address to look up
	 * @return bool
	 */
	function emailexists($email){
		$sql = 'SELECT uid FROM userinfo WHERE email = ? LIMIT 1';
		$query = $this->dbuser->query($sql, array($email));
		return ($query->num_rows() == 1) ? TRUE : FALSE;
	}
	
	
	
	
	/**
	 * Return if user is logged in or not
	 */
	function logged_in(){
		return ($this->CI->session->userdata('user_id') && $this->CI->session->userdata('username'));
	}
	
	
	
	
}
?>