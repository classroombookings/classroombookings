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
		$this->CI =& get_instance();
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
	
	
	
	
	// =======================================================================
	// Get groups
	// =======================================================================
	
	
	
	
	/**
	 * Get all LDAP groups from the server and clear existing groups before inserting new ones
	 *
	 * @param string $username		Username to authenticate with
	 * @param string $password		Password to authenticate with
	 * @return bool
	 */
	public function reload_groups($username = '', $password = '')
	{
		$this->CI->load->model('ldap_groups_model');
		
		$groups = $this->_get_groups($username, $password);
		
		if ($groups)
		{
			$clear = $this->CI->ldap_groups_model->clear_groups();
			$add = $this->CI->ldap_groups_model->set_groups($groups);
			return ($clear && $add !== FALSE) ? $add : FALSE;
		}
		else
		{
			return FALSE;
		}
	}
	
	
	
	
	/**
	 * Get all LDAP groups from the server and update existing ones if they exist
	 *
	 * @param string $username		Username to authenticate with
	 * @param string $password		Password to authenticate with
	 * @return bool
	 */
	public function sync_groups($username = '', $password = '')
	{
		$this->CI->load->model('ldap_groups_model');
		
		$groups = $this->_get_groups($username, $password);
		
		if ($groups)
		{
			return $this->CI->ldap_groups_model->sync_groups($groups);
		}
		else
		{
			return FALSE;
		}
	}
	
	
	
	
	/**
	 * Retrieve a list of groups from the LDAP server as an array
	 */
	private function _get_groups($username = '', $password = '')
	{	
		// Group types
		$security = 268435456;
		$distribution = 268435457;
		
		$account_type = $security;
		
		// Generate filter
		$filter = '(&(objectCategory=group)';
		if ($account_type !== NULL)
		{
			$filter .= '(samaccounttype='. $account_type .')';
		}
		$filter .= '(cn=*))';
		
		// Define which fields are needed
		$fields = array('description', 'cn', 'objectguid');
		
		// Attempt connection to server
		$connect = ldap_connect($this->_host, $this->_port);
		
		if ( ! $connect)
		{
			$this->reason = "Failed to connect to LDAP server {$this->_host} on port {$this->_port}.";
			return FALSE;
		}
		
		ldap_set_option($connect, LDAP_OPT_NETWORK_TIMEOUT, 5);
		ldap_set_option($connect, LDAP_OPT_PROTOCOL_VERSION, 3);
		
		$bind = ldap_bind($connect, $username, $password);
		
		if ( ! $bind)
		{
			$this->reason = 'Unable to bind to LDAP server with the provided settings.';
			return FALSE;
		}
		
		$search = @ldap_search($connect, $this->_base, $filter, $fields);
		
		if ( ! $search)
		{
			$this->reason = 'Could not complete LDAP search.';
			return FALSE;
		}
		
		$entries = @ldap_get_entries($connect, $search);
		
		if ( ! isset($entries['count']) || $entries['count'] == 0)
		{
			$this->reason = 'No groups found.';
			return FALSE;
		}
		
		$groups = array(); 
		
		for ($i = 0; $i < $entries['count']; $i++)
		{
			$name = $entries[$i]['cn'][0];
			
			if (isset($entries[$i]['objectguid']))
			{
				$guid = bin2hex($entries[$i]['objectguid'][0]);
			}
			else
			{
				$guid = md5($name);
			}
			
			if (isset($entries[$i]['description']))
			{
				$desc = $entries[$i]['description'][0];
			}
			else
			{
				$desc = $name;
			}
			
			$groups[] = array(
				'guid' => $guid,
				'name' => $name,
				'desc' => $desc,
			);
		}
		
		return $groups;
		
	}
	
	
	
	
	// =======================================================================
	// Utility methods
	// =======================================================================
	
	
	
	
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