<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Classroombookings. Hassle-free resource booking for schools. <http://classroombookings.com/>
 * Copyright (C) 2006-2011 Craig A Rodway <craig.rodway@gmail.com>
 *
 * This file is part of Classroombookings.
 * Classroombookings is licensed under the Affero GNU GPLv3 license.
 * Please see license-classroombookings.txt for the full license text.
 */


class Log_model extends School_Model
{
	
	
	protected $_table = 'log';
	protected $_primary = 'l_id';
	
	protected $_sch_key = 'l_s_id';
	
	// Specify the lookup type - where or like or IN - for each filterable parameter/db col
	// If the db column isn't here, we can't filter on it.
	protected $_filter_types = array(
		'where' => array('l_id', 'l_s_id', 'l_u_id'),
		'like' => array('l_username', 'l_area', 'l_type', 'l_description', 'l_ip'),
		'in' => array(),
	);
	
	
	function __construct()
	{
		parent::__construct();
	}
	
	
	
	
}

/* End of file: ./application/models/log_model.php */