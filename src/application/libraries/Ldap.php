<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

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

class Ldap
{
	
	
	public $reason;
	
	
	protected $CI;		// CodeIgntier object
	
	
	private $_host;
	private $_port;
	private $_base;
	private $_filter;
	
	
	public function __construct($settings = array())
	{
		$this->initialise($settings);
	}
	
	
	
	
	public function initialise($settings = array())
	{
		$this->_host = element('auth_ldap_host', $settings, NULL);
		$this->_port = element('auth_ldap_port', $settings, NULL);
		$this->_base = element('auth_ldap_base', $settings, NULL);
		$this->_filter = element('auth_ldap_filter', $settings, NULL);
		
		log_message('debug', 'LDAP: initialise(): Settings - ' . var_export($settings, TRUE));
	}
	
	
	
	
	public function authenticate($username = '', $password = '', $info = FALSE)
	{
		$filter = str_replace('%u', $username, $this->_filter);
		$ldap_username = 'cn=' . $username;
		
		// Attempt connection to server
		$connect = ldap_connect($this->_host, $this->_port);
		
		if ( ! $connect)
		{
			$this->reason = "Failed to connect to LDAP server {$this->_host} on port {$this->_port}.";
			return FALSE;
		}
		
		ldap_set_option($connect, LDAP_OPT_NETWORK_TIMEOUT, 2);
		
		$found = FALSE;
		
		// Iterate over the DNs and attempt to bind as the user in them
		$dns = explode(";", $this->_base);
		
		foreach ($dns as $dn)
		{
			if ($found === TRUE) continue;
			
			$this_dn = trim($dn);
			
			log_message('debug', "LDAP: authenticate(): DN - $ldap_username,$this_dn");
			
			$bind = @ldap_bind($connect, "$ldap_username,$this_dn", $password);
			
			if ($bind)
			{
				$user_dn = $this_dn;
				$found = TRUE;
			}
		}
		
		// Check if user in a DN has been found
		if ($found === FALSE)
		{
			// Password could be incorrect
			$this->reason = 'LDAP authentication failure. Check details and try again.';
			return FALSE;
		}
		
		log_message('debug', 'LDAP: authenticate: User DN: ' . $user_dn);
		log_message('debug', 'LDAP: authenticate: Filter: ' . $filter);
		
		// search for details
		$search = @ldap_search($connect, $user_dn, $filter);
		
		if ( ! $search)
		{
			// LDAP query filter is probably incorrect
			$this->reason = "LDAP authentication failure. Query filter did not return any results.";
			return FALSE;
		}
		
		if ($info === TRUE)
		{
			// Get user info
			$info = ldap_get_entries($connect, $search);
			
			return array(
				'username' => $username,
				'displayname' => @$info[0]['displayname'][0],
				'email' => (array_key_exists('mail', $info[0])) ? $info[0]['mail'][0] : NULL,
				'memberof' => @$info[0]['memberof'],
			);
		}
		else
		{
			return TRUE;
		}
	}
	
	
	
	
	public function get_groups($username = '', $password = '')
	{
		// @TODO
	}
	
	
	
	
	public function is_supported()
	{
		if ( ! function_exists('ldap_bind'))
		{
			log_message('error', 'LDAP library: ldap functionality not enabled in PHP.');
			return FALSE;
		}
		
		return TRUE;
	}
	
	
	
}

/* End of file: ./application/libaries/Flash.php */