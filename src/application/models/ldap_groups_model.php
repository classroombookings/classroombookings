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

class Ldap_groups_model extends School_model
{
	
	
	protected $_table = 'ldap_groups';
	protected $_primary = 'lg_id';
	
	protected $_sch_key = 'lg_s_id';
	
	// Specify the lookup type - where or like or IN - for each filterable parameter/db col
	// If the db column isn't here, we can't filter on it.
	protected $_filter_types = array(
		'where' => array('lg_id'),
		'like' => array(),
		'in' => array(),
	);
	
	
	function __construct()
	{
		parent::__construct();
	}
	
	
	
	
	/**
	 * Clear existing LDAP groups table
	 *
	 * @return bool
	 */
	public function clear_groups()
	{
		$sql = 'DELETE FROM ldap_groups WHERE 1 = 1 ' . $this->sch_sql();
		return $this->db->query($sql);
	}
	
	
	
	
	/**
	 * Add new groups to the database
	 *
	 * @param array $ldap_groups		 Array of LDAP group info. Required keys: guid,name,desc
	 * @param bool $update		If TRUE, update existing entries with new data
	 * @return mixed		Number of total rows, or FALSE on insert failure
	 */
	public function set_groups($ldap_groups = array(), $update = FALSE)
	{
		foreach ($ldap_groups as $group)
		{
			$values[] = sprintf('(%d, %s, %s, %s)',
				$this->_s_id,
				$this->db->escape($group['guid']),
				$this->db->escape($group['name']),
				$this->db->escape($group['desc'])
			);
		}
		
		if (count($values) > 0)
		{
			$sql = 'INSERT INTO
						ldap_groups (lg_s_id, lg_guid, lg_name, lg_description)
					VALUES
						' . implode(',', $values);
			if ($update)
			{
				$sql .= ' ON DUPLICATE KEY UPDATE
							lg_s_id = VALUES(lg_s_id),
							lg_guid = VALUES(lg_guid),
							lg_name = VALUES(lg_name),
							lg_description = VALUES(lg_description)';
			}
			
			return $this->db->query($sql);
		}
		
		return FALSE;
	}
	
	
	
	
	/**
	 * Make the internal list of LDAP groups mirror the list retrieved from the LDAP server.
	 *
	 * @param array 		Array of data, each item must have guid,name,desc
	 * @return bool
	 */
	public function sync_groups($ldap_groups = array())
	{
		$errors = 0;
		
		// Get list of current groups to compare to
		$current_groups = $this->dropdown('lg_guid', 'lg_name');
		// ... but just the GUIDs
		$existing_guids = array_keys($current_groups);
		
		// Get the GUIDs of the incoming LDAP groups
		$ldap_guids = array();
		foreach ($ldap_groups as $group)
		{
			$ldap_guids[] = $group['guid'];
		}
		
		// Add AND UPDATE (TRUE) groups
		if ( ! $this->set_groups($ldap_groups, TRUE))
		{
			$errors++;
		}
		
		// Find any items that need deleting (present in local, not in $ldap_groups)
		$delete = array_diff($existing_guids, $ldap_guids);
		$to_delete = array();
		foreach ($delete as $ldap_group_guid)
		{
			$to_delete[] = $this->db->escape($ldap_group_guid);
		}
		
		if ($to_delete)
		{
			$sql = 'DELETE FROM ldap_groups
					WHERE lg_guid IN (' . implode(',', $to_delete) . ')
					' . $this->sch_sql();
			
			if ( ! $this->db->query($sql))
			{
				$errors++;
			}
		}
		
		return ($errors === 0);
	}
	
	
	
	
}

/* End of file: ./application/models/ldap_groups_model.php */