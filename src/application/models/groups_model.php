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

class Groups_model extends School_model
{
	
	
	protected $_table = 'groups';
	protected $_primary = 'g_id';
	protected $_sch_key = 'g_s_id';
	
	
	function __construct()
	{
		parent::__construct();
	}
	
	
	
	
	/**
	 * Get all rows from the table with additional school ID clause
	 *
	 * @return array
	 */
	public function get_all()
	{
		$sql = 'SELECT
					g.*,
					COUNT(u_id) AS user_count
				FROM
					groups g
				LEFT JOIN
					users ON g_id = u_g_id
				WHERE 1 = 1
				' . $this->sch_sql() . '
				' . $this->filter_sql() . '
				' . $this->order_sql() . '
				' . $this->limit_sql() . '
				GROUP BY g_id';
		
		return $this->db->query($sql)->result_array();
	}
	
	
	
	
}

/* End of file: ./application/models/groups_model.php */