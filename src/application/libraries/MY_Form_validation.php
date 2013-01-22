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

// -----------------------------------------------------------------------------

/**
 * Extended Form Validation Class to include additional validation functions.
 *
 * @package		Classroombookings
 * @subpackage	Libraries
 * @category	Validation
 * @author		Craig A Rodway
 */

class MY_Form_validation extends CI_Form_validation {


	protected $CI;
	
	
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->CI =& get_instance(); 
	}
	
	
	
	
	/**
	 * Check that the provided value is a valid TCP/IP port number between 1 and 65535
	 *
	 * @param int 
	 */
	function valid_port($port = 0)
	{
		$port = (int) $port;
		$check = $port >= 1 && $port <= 65536;
		
		if ($check === FALSE)
		{
			$this->form_validation->set_message('valid_port', 'The %s must be between 1 and 65536.');
		}
		
		return $check;
	}
	
	
	
	
}

/* End of file: ./application/libraries/MY_Form_validation.php */