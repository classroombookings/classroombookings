<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Classroombookings. Hassle-free resource booking for schools. <http://classroombookings.com/>
 * Copyright (C) 2006-2011 Craig A Rodway <craig.rodway@gmail.com>
 *
 * This file is part of Classroombookings.
 * Classroombookings is licensed under the Affero GNU GPLv3 license.
 * Please see license-classroombookings.txt for the full license text.
 */


class Departments_model extends School_Model
{
	
	
	protected $_table = 'departments';
	protected $_primary = 'd_id';
	
	protected $_sch_key = 'd_s_id';
	
	// Specify the lookup type - where or like or IN - for each filterable parameter/db col
	// If the db column isn't here, we can't filter on it.
	protected $_filter_types = array(
		'where' => array('d_id', 'u_g_id'),
		'like' => array('d_name', 'd_description'),
		'in' => array(),
	);
	
	
	function __construct()
	{
		parent::__construct();
	}
	
	
	
	
	public function get_all()
	{
		$sql = 'SELECT
					d.*,
					COUNT(DISTINCT u2d_u_id) AS user_count
				FROM
					departments d
				LEFT JOIN
					u2d
					ON d_id = u2d_d_id
				WHERE 1 = 1
				' . $this->sch_sql() . '
				' . $this->filter_sql() . '
				GROUP BY d_id
				' . $this->order_sql() . '
				' . $this->limit_sql();
		
		return $this->db->query($sql)->result_array();
	}
	
	
	
	
	public function get($d_id = 0)
	{
		$department = parent::get($d_id);
		
		if ($department)
		{
			$department['ldap_groups'] = $this->get_ldap_groups($d_id);
		}
		
		return $department;
	}
	
	
	
	
	// ========================================================================
	// LDAP groups
	// ========================================================================
	
	
	
	
	/**
	 * Get simple list of LDAP group IDs => names that are assigned to this department
	 */
	public function get_ldap_groups($d_id = 0)
	{
		$sql = 'SELECT
					lg_id,
					lg_name
				FROM
					d2lg
				LEFT JOIN
					ldap_groups lg ON d2lg_lg_id = lg_id
				WHERE
					d2lg_d_id = ?
				AND
					lg_s_id = ?
				ORDER BY
					lg_name ASC';
		
		$result = $this->db->query($sql, array($d_id, $this->config->item('s_id')))->result_array();
		
		$ldap_groups = array();
		
		if ($result)
		{
			foreach ($result as $row)
			{
				$ldap_groups[$row['lg_id']] = $row['lg_name'];
			}
		}
		
		return $ldap_groups;
	}
	
	
	
	
	/**
	 * Sets a department's LDAP group assignments
	 *
	 * @param int $d_id		ID of department to update LDAP group assignments for
	 * @param array $lg_ids		1D array of LDAP group IDs to set for department
	 * @return bool
	 */
	public function set_ldap_groups($d_id = 0, $lg_ids = array())
	{
		$sql = 'DELETE FROM d2lg WHERE d2lg_d_id = ?';
		$this->db->query($sql, array($d_id));
		
		if ( ! empty($lg_ids))
		{
			$values = array();
			
			foreach ($lg_ids as $lg_id)
			{
				if (empty($lg_id)) continue;
				$values[] = '(' . (int) $d_id . ', ' . (int) $lg_id . ')';
			}
			
			if (empty($values)) return TRUE;
			
			$sql = 'INSERT INTO d2lg (d2lg_d_id, d2lg_lg_id) VALUES ' . implode(',', $values);
			
			return $this->db->query($sql);
		}
		
		return TRUE;
	}
	
	
	
	
		
}

/* End of file: ./application/models/departments_model.php */