<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Auth_ldap extends CI_Driver {
	
	
	public $reason;
	
	
	
	
	/**
	 * Authenticate a username and password with the configured LDAP server settings
	 *
	 * @param string $username		Username of user
	 * @param string $password		Password to authenticate user with
	 * @return bool
	 */
	public function auth($username = '', $password = '')
	{
		$this->CI->load->library('ldap');
		
		// Try to get existing user first
		$user = $this->CI->users_model->get_by_username($username);
		
		// Don't create a user (yet)
		$create_user = FALSE;
		
		if ($user)
		{
			// User found
			log_message('debug', "Auth->ldap->auth($username): User found.");
			
			// Check auth method is LDAP
			if ($user['u_auth_method'] !== 'ldap')
			{
				log_message('debug', "Auth->ldap->auth($username): Auth method is: " . $user['u_auth_method']);
				$this->reason = 'Account not configured for LDAP.';
				return FALSE;
			}
			
			// User is found and they are LDAP - do not create them
			$create_user = FALSE;
		}
		else
		{
			// User is NOT found - we should create them on LDAP auth success.
			log_message('debug', "Auth->ldap->auth($username): User not found.");
			$create_user = TRUE;
		}
		
		// Initialise the LDAP library with the system settings
		$this->_init_ldap();
		
		// Try to authenticate the user, and return the user details
		$ldap_user = $this->CI->ldap->authenticate($username, $password, TRUE);
		
		
		if ($ldap_user === FALSE)
		{
			// Auth failed
			log_message('debug', "Auth->ldap->auth($username): Bad username/password or LDAP server error - " . $this->CI->ldap->reason);
			$this->reason = $this->CI->ldap->reason;	//'Invalid username/password or LDAP server error.';
			return FALSE;
		}
		
		
		// User auth successful!
		log_message('debug', "Auth->ldap->auth($username): User authenticated!");
		
		
		// User does not need updating and they already exist - nothing more to do.
		if (option('auth_ldap_loginupdate') === FALSE && $create_user === FALSE)
		{
			return TRUE;
		}
		
		
		// Existing user should be updated because the option is turned on for updating.
		if (option('auth_ldap_loginupdate') && $create_user === FALSE)
		{
			$update = $this->CI->users_model->update($user['u_id'], array(
				'u_display' => element('displayname', $ldap_user, $user['u_display']),
				'u_email' => element('email', $ldap_user, $user['u_email']),
			));
			
			// Update the user group & department memberships for LDAP groups
			$this->update_user_membership($user['u_id'], $ldap_user['memberof']);
			
			// Done all the updates needed - return TRUE for succesful auth
			return TRUE;
		}
		
		
		// Create a new user based on the details from LDAP
		if ($create_user)
		{
			$user_data = array(
				'u_username' => $ldap_user['username'],
				'u_display' => element('displayname', $ldap_user, $ldap_user['username']),
				'u_email' => element('email', $ldap_user, NULL),
				'u_auth_method' => 'ldap',
				'u_enabled' => 1,
				'u_g_id' => option('auth_ldap_g_id'),
			);
			
			$u_id = $this->CI->users_model->insert($user_data);
			
			if ($u_id)
			{
				Events::trigger('user_insert_ldap', array(
					'u_id' => $u_id,
					'user' => $user_data,
				));
				
				$this->update_user_membership($u_id, $ldap_user['memberof']);
				
				return TRUE;
			}
			else
			{
				// User creation failed
				$this->reason = 'Unable to create new user account';
				return FALSE;
			}
			
		}
		
		
		// All options exhausted.
		return FALSE;
		
	}
	
	
	
	
	/**
	 * Update a user membership of CRBS groups & departments depending on their LDAP group memberships
	 */
	public function update_user_membership($u_id = 0, $memberof = array())
	{
		// If not a member of any groups, don't need to do anything
		if (element('count', $memberof, 0) == 0)
		{
			//return FALSE;
		}
		
		unset($memberof['count']);
		
		// Array of group names that the user is a member of
		$group_names = array();
		
		foreach ($memberof as $g)
		{
			// Do some string stuff to get just the group name, minus the CN= prefix
			$name = current(explode(',', $g));
			$group_names[] = str_replace('CN=', '', $name);
		}
		
		$this->CI->load->model('users_model');
		$this->CI->users_model->update_ldap_membership($u_id, $group_names);
		
		return TRUE;
	}
	
	
	
	
	/**
	 * Initialise the LDAP library with the system settings
	 */
	private function _init_ldap()
	{
		$settings = array(
			'auth_ldap_host' => option('auth_ldap_host'),
			'auth_ldap_port' => option('auth_ldap_port'),
			'auth_ldap_base' => option('auth_ldap_base'),
			'auth_ldap_filter' => option('auth_ldap_filter'),
		);
		
		$this->CI->load->library('ldap');
		$this->CI->ldap->initialise($settings);
	}
	
	
	
	
}

/* End of file: ./application/libaries/Auth/drivers/Auth_ldap.php */