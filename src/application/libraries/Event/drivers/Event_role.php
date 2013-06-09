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
 * Class of functions to handle Role events
 */

class Event_role extends CI_Driver {
	
	
	/**
	 * Register listeners for events
	 */
	public function init()
	{
		Events::register('role_insert', array($this, 'role_insert'));
		Events::register('role_update', array($this, 'role_update'));
		Events::register('role_delete', array($this, 'role_delete'));
		Events::register('role_assign', array($this, 'role_assign'));
		Events::register('role_unassign', array($this, 'role_unassign'));
	}
	
	
	// ========================================================================
	// Event handler functions
	// ========================================================================
	
	
	public function role_insert($data = array())
	{
		$this->CI->logger->add('roles/role_insert', $data);
	}
	
	
	public function role_update($data = array())
	{
		$this->CI->logger->add('roles/role_update', $data);
	}
	
	
	public function role_delete($data = array())
	{
		$this->CI->logger->add('roles/role_delete', $data);
	}
	
	
	public function role_assign($data = array())
	{
		$this->CI->logger->add('roles/role_assign', $data);
	}
	
	
	public function role_unassign($data = array())
	{
		$this->CI->logger->add('roles/role_unassign', $data);
	}
	
	
	
	
}

/* End of file: ./application/libaries/Event/drivers/Event_groups.php */