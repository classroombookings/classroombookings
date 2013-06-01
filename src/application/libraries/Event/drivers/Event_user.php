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
 * Class of functions to handle Group events
 */

class Event_user extends CI_Driver {
	
	
	/**
	 * Register listeners for events
	 */
	public function init()
	{
		Events::register('user_insert', array($this, 'user_insert'));
		Events::register('user_update', array($this, 'user_update'));
		Events::register('user_delete', array($this, 'user_delete'));
		Events::register('users_import', array($this, 'users_import'));
	}
	
	
	// ========================================================================
	// Event handler functions
	// ========================================================================
	
	
	public function user_insert($data = array())
	{
		// Hashed password should not get logged
		unset($data['user']['u_password']);
		
		$this->CI->logger->add('users/user_insert', $data);
	}
	
	
	public function user_update($data = array())
	{
		// Hashed password should not get logged
		unset($data['user']['u_password']);
		
		$this->CI->logger->add('users/user_update', $data);
	}
	
	
	public function user_delete($data = array())
	{
		// Hashed password should not get logged
		unset($data['user']['u_password']);
		
		$this->CI->logger->add('users/user_delete', $data);
	}
	
	
	public function users_import($data = array())
	{
		$this->CI->logger->add('users/users_import', $data);
	}
	
	
	
	
}

/* End of file: ./application/libaries/Event/drivers/Event_groups.php */