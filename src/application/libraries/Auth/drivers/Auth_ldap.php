<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH . '/third_party/adLDAP/adLDAP.php');


class Auth_ldap extends CI_Driver {
	
	
	public $reason;
	
	private $adldap;		// reference to adLDAP instance
	
	
	
	
	public function __construct()
	{
		try {
			$this->adldap = new adLDAP(array(
				'base_dn' => option('auth_ldap_base'),
				'account_suffix' => option('auth_ldap_account_suffix'),
				'domain_controllers' => array(option('auth_ldap_host')),
			));
		} catch (adLDAPException $e) {
			log_message('error', "Auth->ldap->__construct(): LDAP Exception: " . $e->getMessage());
		}
	}
	
	
	
	
	public function auth($username = '', $password = '')
	{
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
		
		try {
			$auth_user = $this->adldap->user()->authenticate($username, $password);
		} catch (Exception $e) {
			log_message('debug', "Auth->ldap->auth($username): LDAP exception: " . $e->getMessage());
			$this->reason = $e->getMessage();
			return FALSE;
		}
		
		if ($auth_user === TRUE)
		{
			log_message('debug', "Auth->ldap->auth($username): User authenticated!");
		}
		else
		{
			$this->adldap->close();
			log_message('debug', "Auth->ldap->auth($username): Bad username/password.");
			$this->reason = 'Invalid username/password or LDAP server error.';
			return FALSE;
		}
	}
	
	
	
	
}

/* End of file: ./application/libaries/Auth/drivers/Auth_ldap.php */