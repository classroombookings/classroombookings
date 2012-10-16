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

class Schools_model extends MY_Model
{
	
	
	protected $_table = 'schools';
	protected $_primary = 'sch_id';
	
	
	function __construct()
	{
		parent::__construct();
	}
	
	
}

/* End of file: ./application/models/schools_model.php */