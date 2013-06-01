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
 * Class of functions to handle Department events
 */

class Event_department extends CI_Driver {
	
	
	/**
	 * Register listeners for events
	 */
	public function init()
	{
		// Department getting added
		Events::register('department_insert', array($this, 'department_insert'));
		Events::register('department_update', array($this, 'department_update'));
		Events::register('department_delete', array($this, 'department_delete'));
	}
	
	
	// ========================================================================
	// Event handler functions
	// ========================================================================
	
	
	public function department_insert($data = array())
	{
		$this->CI->logger->add('departments/department_added', $data);
	}
	
	
	public function department_update($data = array())
	{
		$this->CI->logger->add('departments/department_updated', $data);
	}
	
	
	public function department_delete($data = array())
	{
		$this->CI->logger->add('departments/department_deleted', $data);
	}
	
	
	
	
}

/* End of file: ./application/libaries/Event/drivers/Event_departments.php */