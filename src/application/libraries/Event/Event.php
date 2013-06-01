<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

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
 
class Event extends CI_Driver_Library
{
	
	public $valid_drivers = array('event_department', 'event_group');
	
	public $CI;
	
	
	
	
	public function __construct()
	{
		$this->CI =& get_instance();
		
		// Initialise all the events
		foreach ($this->valid_drivers as $driver)
		{
			$class = str_replace('event_', '', $driver);
			if (method_exists($this->{$class}, 'init'))
			{
				call_user_func(array($this->{$class}, 'init'));
			}
		}
	}
	
	
	
	
}


/* End of file ./application/libraries/Event/Event.php */