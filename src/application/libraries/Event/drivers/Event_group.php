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

class Event_group extends CI_Driver {
	
	
	/**
	 * Register listeners for events
	 */
	public function init()
	{
		// Department getting added
		Events::register('group_insert', array($this, 'group_insert'));
		Events::register('group_update', array($this, 'group_update'));
		Events::register('group_delete', array($this, 'group_delete'));
	}
	
	
	// ========================================================================
	// Event handler functions
	// ========================================================================
	
	
	public function group_insert($data = array())
	{
		$this->CI->logger->add('groups/group_added', $data);
	}
	
	
	public function group_update($data = array())
	{
		$this->CI->logger->add('groups/group_updated', $data);
	}
	
	
	public function group_delete($data = array())
	{
		$this->CI->logger->add('groups/group_deleted', $data);
	}
	
	
	
	
}

/* End of file: ./application/libaries/Event/drivers/Event_groups.php */