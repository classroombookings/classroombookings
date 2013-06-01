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
	protected $_join = array('users', 'l_u_id = u_id');
	
	// Specify the lookup type - where or like or IN - for each filterable parameter/db col
	// If the db column isn't here, we can't filter on it.
	protected $_filter_types = array(
		'where' => array('l_id', 'l_s_id', 'l_u_id', 'l_username'),
		'like' => array('l_area', 'l_type', 'l_description', 'l_ip'),
		'in' => array(),
	);
	
	
	function __construct()
	{
		parent::__construct();
	}
	
	
	
	
	/**
	 * Get list of areas where events have been made for
	 *
	 * @return array 		2D array of areas (same value for key and value)
	 */ 
	public function get_areas()
	{
		$sql = 'SELECT DISTINCT l_area FROM log ORDER BY l_area ASC';
		$result = $this->db->query($sql)->result_array();
		
		foreach ($result as $row)
		{
			$areas[$row['l_area']] = $row['l_area'];
		}
		
		return $areas;
	}
	
	
	
	
	/**
	 * Get list of types that events have been made for
	 *
	 * @return array 		2D array of types (same value for key and value)
	 */ 
	public function get_types()
	{
		$sql = 'SELECT DISTINCT l_type FROM log ORDER BY l_area ASC';
		$result = $this->db->query($sql)->result_array();
		
		foreach ($result as $row)
		{
			$types[$row['l_type']] = $row['l_type'];
		}
		
		return $types;
	}
	
	
	
	
}

/* End of file: ./application/models/log_model.php */