<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


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
		
		// Load cookie helper as required by this library
		$this->CI->load->helper('cookie');
		
		// Cookie salt for hash - can be any text string
		$this->cookiesalt = 'CL455R00Mb00k1ng5';
		
		// Redirect to this error page
		$this->errpage = 'dashboard/error';
		
		// Get LDAP settings
		#$this->CI->load->library('settings');	//
		
		#$this->settings['ldap'] = $this->CI->settings->getldap();
		
		

	}
	
	
	
	
	function check($action){
		
		// Get permission_id of the action
		$p_id = $this->get_permission_id($action);
		if($p_id == FALSE || $p_id == NULL){
			$this->lasterr = sprintf($this->CI->lang->line('AUTH_CHECK_NO_PID'), $action);
			$this->CI->msg->add('err', $this->lasterr);
			redirect($this->errpage);
		}
		
		//echo $p_id;
		
		// Get the user's group ID from the session
		$g_id = $this->CI->session->userdata('group_id');
		$g_id = ($g_id === FALSE) ? 0 : $g_id;
		
		//echo $g_id;
		
		// Query to select the exact permission available for this user
		$sql = 'SELECT p_id FROM permissions2groups WHERE p_id = ? AND g_id = ? OR g_id = 0 LIMIT 1';
		$query_pid = $this->CI->db->query($sql, array($p_id, $g_id));
		
		$sql = 'SELECT g_id FROM permissions2groups WHERE p_id = ?';
		$query_gids = $this->CI->db->query($sql, array($p_id));
		
		if($query_gids->num_rows() > 0){
			foreach($query_gids->result_array() as $g_id){
				$g_ids[] = $g_id['g_id'];
			}
		}
		
		//echo $query->num_rows();
		
		// Return true if user has this permission assigned to their group
		$check = ($query_pid->num_rows() == 1) ? TRUE : FALSE;
		
		
		
		if($check == FALSE){
		
			$uri = $this->CI->uri->uri_string();
			$this->CI->session->set_userdata('uri', $uri);
			
			if($this->logged_in() == FALSE){
				$this->lasterr = $this->CI->lang->line('AUTH_MUST_LOGIN');
				$uri = 'account/login';
			} else {
				$this->lasterr = $this->CI->lang->line('AUTH_NO_PRIVS');
				$uri = $this->errpage;
			}
			
			$this->CI->msg->add('err', $this->lasterr);
			redirect($uri);
		}
		
	}
	
	
	
	
	/**
	 * Check to see if our user (belonging to a group) can access this action
	 */
	/*function check($action){
		// Get user's group_id. If empty, they're anonymous.
		$group_id = $this->CI->session->userdata('group_id');
		$group_id = ($group_id === FALSE) ? 0 : $group_id;
		
		// Permissions that the user is allowed to access (out in sessdata in the hook)
		$permissions = $this->CI->session->userdata('permissions');
		
		// Get the permission action ID
		$action_id = $this->get_permission_id_by_action($action);
		
		// Now we have group and action_id they're trying to access.
		// Got to check if this ID is in the array of permission_ids that they can access
		if(!in_array($action_id, $permissions0)){
			redirect("account/login");
		}
		
	}*/
	
	
	
	
	function checklevel($required, $authlevel = NULL, $h = FALSE){
		// If no auth configured for this section, allow.
		#die(var_export($required,TRUE) . " - " . $authlevel);
		
		if(empty($authlevel)){ $authlevel = 'guest'; }
		
		
		$arrcheck = explode("/", $required);
		$offset = 0;
		$arrchecks = array();
		$arrposs = array();
		while($offset = strpos($required.'/', '/', $offset + 1)){
			$arrposs[] = $offset;
			$arrchecks[] = substr($required, 0, $offset);
		}
		
		#rsort($arrchecks);
		if(empty($arrchecks)){ return TRUE; }
		
		foreach($arrchecks as $required){
		
			// Return true if there is no array key for this page
			if( !array_key_exists($required, $this->levels) ){
				return TRUE;
			}
			
			// Return true if empty array (all users) for this
			if( isset($this->levels[$required]) && is_array($this->levels[$required]) && empty($this->levels[$required]) ){
				return TRUE;
			}
			
			
			// Check to see if authconf array is set for this section
			if(is_array($this->levels[$required]) && count($this->levels[$required]) > 0){
	
				// Yep, is set - now check the user's authlevel is actually in the array
				#echo "looking for '$authlevel' in " . var_export($this->levels[$required], true) . " ..";
			    $found = in_array($authlevel, $this->levels[$required]);
			    #echo (int)$found;
			    if($found){
			        // Yep, user OK!
			        return true;
				} else {
				    // Nope!
					return false;
				}
	
			} else {
	
				// No array!
				return false;
				
			}
		
		}
	}


	/*
	function checklevel($required = 0, $user = 0){
		$required = (isset($required)) ? $required : 0;
		$user = (isset($user)) ? $user : 0;
		$ret = ($user >= $required) ? TRUE : FALSE;
		return $ret;
	}*/
	
	
	
	
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
	
	
	
	
	/**
	 * Get a permission_id by it's name
	 */
	function get_permission_id($action){
		if($action == NULL){ return FALSE; }
		$sql = 'SELECT permission_id FROM permissions WHERE action = ? LIMIT 1';
		$query = $this->CI->db->query($sql, array($action));
		
		if($query->num_rows() == 1){
			$row = $query->row();
			return $row->permission_id;
		} else {
			return NULL;
		}
	}
	
	
	
	
	/**
	 * Get permission IDs for group
	 *
	 * @param int ID of group to find
	 * @return array Permission IDs that the group is allowed to access
	 */
	/*function get_group_permission_ids($group_id){
		if(!is_numeric($group_id)){ return FALSE; }
		
		// Get all permission IDs
		$sql = 'SELECT permission_id FROM permissions2groups WHERE group_id = ?';
		$query = $this->CI->db->query($sql, array($group_id));
		
		if($query->num_rows() > 0){
			// Put them in a simple 1D array
			$permissions = array();
			$result = $query->result();
			foreach($result as $row){
				$permissions[] = $row->permission_id;
			}
			return $permissions;
		} else {
			return FALSE;
		}
	}*/
	
	
	
	
	
	/*function get_permission_by_url($url){
		if($url == NULL){ return FALSE; }
		
		$sql = 'SELECT permission_id, action, menuname, `admin-title`
				FROM permissions WHERE url = ? LIMIT 1';
		$query = $this->CI->db->query($sql, array($url));
		
		if($query->num_rows() == 1){
			// OK!
			$row = $query->row();
			return $row;
		} else {
			return FALSE;
		}
	}*/
	
	
	
	
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