<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Classroombookings. Hassle-free resource booking for schools. <http://classroombookings.com/>
 * Copyright (C) 2006-2011 Craig A Rodway <craig.rodway@gmail.com>
 *
 * This file is part of Classroombookings.
 * Classroombookings is licensed under the Affero GNU GPLv3 license.
 * Please see license-classroombookings.txt for the full license text.
 */

class Permissions_model extends MY_Model
{

	protected $_table = 'permissions';		// DB table
	
	public $error;


	function __construct()
	{
		parent::__construct();
	}
	
	
	
	
	/**
	 * List all the available permissions in the DB ordered by name
	 *
	 * @param string $format		Array format ('id' or 'cat')
	 * @return array
	 */
	function get_available_permissions($format = 'id')
	{
		$sql = 'SELECT * FROM permissions';
		$result = $this->db->query($sql)->result_array();
		
		$_id = array();
		$_section = array();
		
		foreach ($result as $row)
		{
			$_section[$row['p_section']][$row['p_id']] = $row['p_name'];
			$_id[$row['p_id']] = $row['p_name'];
		}
		
		return ($format === 'id') ? $_id : $_section;
	}
	
	
	
	
	/**
	 * Set permissions for a role
	 *
	 * @param int $r_id		ID of role to update
	 * @param array 		2D array of permissions (p_id => p_name)
	 * @return bool
	 */
	function set_for_role($r_id = 0, $permissions = array())
	{
		log_message('debug', "Permissions_model: set_for_role(): Updating permissions for Role ID $r_id.");
		
		// Remove existing entries
		$sql = 'DELETE FROM p2r WHERE p2r_r_id = ?';
		$this->db->query($sql, array($r_id));
		
		// Add new entries
		$sql = 'INSERT INTO p2r (p2r_r_id, p2r_p_id) VALUES ';
		
		foreach ($permissions as $p_id)
		{
			$values[] = sprintf("(%d, %d)", $r_id, $p_id);
		}
		
		$sql .= implode(',', $values);
		
		return $this->db->query($sql);
	}
	
	
	
	
	/**
	 * Get the permission values for provided role(s)
	 * 
	 * Takes role weight into consideration - ask "what can these roles do"
	 *
	 * @param mixed $r_id		Integer or array of integers
	 * @return array 		Simple array of permission names
	 */
	function for_role($r_id = array())
	{
		if (is_numeric($r_id))
		{
			$r_id = (int) $r_id;
		}
		elseif (is_array($r_id))
		{
			$r_id = implode(',', $r_id);
		}
		
		$sql = 'SELECT
					p2r_p_id,
					p2r_r_id,
					p_name
				FROM
					p2r
				LEFT JOIN
					permissions
					ON p2r_p_id = p_id
				LEFT JOIN
					roles
					ON p2r_r_id = r_id
				WHERE
					p2r_r_id IN (' . $r_id . ')
				AND
					r_s_id = ' . config_item('s_id') . '';
		
		$query = $this->db->query($sql);
		
		// Array of permission names to be returned
		$permissions = array();
		
		if ($query->num_rows() > 0)
		{
			$result = $query->result_array();
			
			foreach ($result as $row)
			{
				log_message('debug', "Permissions_model: for_role(): {$row['p_name']} granted via Role ID {$row['p2r_r_id']}.");
				$permissions[$row['p2r_p_id']] = $row['p_name'];
			}
		}
		
		return $permissions;
	}
	
	
	
	
	/**
	 * Get actual permissions that user is permitted to do
	 *
	 * @param int $u_id		ID of user to get permissions for
	 * @return array 		Array of permissions (id => name)
	 */
	function for_user($u_id = 0)
	{	
		$user_roles = $this->roles_model->for_user($u_id);
		
		$permissions = array();
		
		if (is_array($user_roles))
		{
			foreach ($user_roles as $r)
			{
				$roles[] = $r['r_id'];
			}
			
			// Now get permissions for those roles (id => name);
			$permissions = $this->for_role($roles);
		}
		
		return $permissions;
	}
	
	
	
	
	/**
	 * Save a user's permissions to the cache table for quick retrieval
	 *
	 * @param int $u_id		ID of user to save permissions for
	 * @return bool
	 */
	function set_cache($u_id = 0)
	{	
		$sql = 'INSERT INTO
					permissions_cache
				SET
					pc_u_id = ?,
					pc_permissions = ?
				ON DUPLICATE KEY UPDATE
					pc_permissions = VALUES(pc_permissions)';
		
		log_message('debug', "Permissions model: set_cache(): saving permissions for user ID $u_id to cache.");
		
		$permissions = @serialize($this->for_user($u_id));
		return $this->db->query($sql, array($u_id, $permissions));
	}
	
	
	
	
	/**
	 * Get a user's permissions from the cache table.
	 *
	 * If not cached, will get permissions and set the cache
	 *
	 * @param int $u_id		ID of user to get permissions for
	 * @return array
	 */
	function get_cache($u_id = 0)
	{
		if ($u_id === 0) return array();
		
		$sql = 'SELECT pc_permissions
				FROM permissions_cache 
				WHERE pc_u_id = ?
				LIMIT 1';
		
		$row = $this->db->query($sql, array($u_id))->row_array();
		
		if ($row)
		{
			// Got permissions from the cache
			log_message('debug', "Permissions model: get_cache(): got permissions for user id $u_id from cache.");
			return @unserialize($row['pc_permissions']);
		}
		else
		{
			// No cache entry - make it!
			log_message('debug', "Permissions model: get_cache(): none found for user ID $u_id. Now caching...");
			
			if ($this->set_cache($u_id))
			{
				return $this->get_cache($u_id);
			}
			else
			{
				// Couldn't save them for some reason!
				return FALSE;
			}
		}
		
	}
	
	
	
	
	/**
	 * Clear permissions from the cache table (all, or for one user)
	 *
	 * Clear all permissions when:
	 *	1) any permissions are changed
	 *	2) on logout
	 *
	 * @param int $u_id		ID of user to clear permissions for
	 * @return bool
	 */
	function clear_cache($u_id = 0)
	{
		$sql = 'DELETE FROM permissions_cache
				WHERE pc_u_id = ?
				LIMIT 1';
		
		return $this->db->query($sql, array($u_id));
	}
	
	
	
	
}

/* End of file: ./application/models/permissions_model.php */