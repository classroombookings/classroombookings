<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

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

/**
 * Class of functions to handle Settings events
 */

class Event_settings extends CI_Driver {
	
	
	/**
	 * Register listeners for events
	 */
	public function init()
	{
		Events::register('settings_general_update', array($this, 'general_update'));
		Events::register('settings_authentication_update', array($this, 'authentication_update'));
		Events::register('settings_ldap_update', array($this, 'ldap_update'));
		Events::register('settings_ldap_groups_update', array($this, 'ldap_groups_update'));
		Events::register('settings_preauth_update', array($this, 'preauth_update'));
	}
	
	
	// ========================================================================
	// Event handler functions
	// ========================================================================
	
	
	public function general_update($data = array())
	{
		$this->CI->logger->add('settings/general_update', $data);
	}
	
	
	public function authentication_update($data = array())
	{
		$this->CI->logger->add('settings/authentication_update', $data);
	}
	
	
	public function ldap_update($data = array())
	{
		$this->CI->logger->add('settings/ldap_update', $data);
	}
	
	
	public function ldap_groups_update($data = array())
	{
		$this->CI->logger->add('settings/ldap_groups_update', $data);
	}
	
	
	public function preauth_update($data = array())
	{
		$this->CI->logger->add('settings/preauth_update', $data);
	}
	
	
	
	
	
}

/* End of file: ./application/libaries/Event/drivers/Event_settings.php */