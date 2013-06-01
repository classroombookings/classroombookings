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
 * Class of functions to handle Auth events
 */

class Event_auth extends CI_Driver {
	
	
	/**
	 * Register listeners for events
	 */
	public function init()
	{
		Events::register('user_login', array($this, 'user_login'));
	}
	
	
	// ========================================================================
	// Event handler functions
	// ========================================================================
	
	
	public function user_login($data = array())
	{
		unset($data['user']['u_password']);
		$this->CI->logger->add('auth/user_login_' . $data['driver'], $data);
	}
	
	
}

/* End of file: ./application/libaries/Event/drivers/Event_auth.php */