<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Classroombookings. Hassle-free resource booking for schools. <http://classroombookings.com/>
 * Copyright (C) 2006-2011 Craig A Rodway <craig.rodway@gmail.com>
 *
 * This file is part of Classroombookings.
 * Classroombookings is licensed under the Affero GNU GPLv3 license.
 * Please see license-classroombookings.txt for the full license text.
 */

class Permissions_model extends School_Model
{

	protected $_table = 'permissions';		// DB table
	protected $_sch_key = 'o_s_id';		// Foreign key for school
	
	public $error;
	
	private $allowed_entity_types = array('E', 'D', 'G', 'U', 'R');


	function __construct()
	{
		parent::__construct();
	}
	
	
	
	
	/**
	 * Get all roles in the database ordered by weight
	 *
	 * @return object Roles
	 */
	function get_roles()
	{
		$this->db->order_by('weight', 'asc');
		$this->db->order_by('name', 'asc');
		$query = $this->db->get('roles');
		if ($query->num_rows() > 0)
		{
			return $query->result();
		}
		else
		{
			$this->lasterr = 'No roles defined';
			return false;
		}
	}
	
	
	
	
	/**
	 * Get list of roles as array in id => name format
	 *
	 * @return array
	 */
	function get_roles_dropdown()
	{
		$sql = 'SELECT role_id, name FROM roles ORDER BY name ASC';
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0)
		{
			$result = $query->result();
			$roles = array();
			foreach ($result as $row)
			{
				$roles[$row->role_id] = $row->name;
			}
			return $roles;
		}
		else
		{
			$this->lasterr = 'No roles found';
			return false;
		}
	}
	
	
	
	
	/**
	 * Retrieve one role from the database
	 *
	 * @param int role_id Role ID of role to retrieve
	 * @return object The role
	 */
	function get_role($role_id = null)
	{
		if (!$role_id) return false;
		$sql = 'SELECT * FROM roles WHERE role_id = ? LIMIT 1';
		$query = $this->db->query($sql, array($role_id));
		if ($query->num_rows() == 1)
		{
			return $query->row();
		}
		else
		{
			return false;
		}
	}
	
	
	
	
	/**
	 * Add a role to the database
	 *
	 * @param array data Array of data for the role
	 * @return bool
	 */
	function add_role($data = array())
	{
		if (empty($data))
		{
			$this->lasterr = 'Empty data';
			return false;
		}
		
		// Get weight for new role
		$data['weight'] = $this->get_role_weight('max') + 1;
		
		return $this->db->insert('roles', $data);
	}
	
	
	
	
	/**
	 * Get the current min or max weight of the roles
	 *
	 * @param string which Minimum or Maximum number to get
	 * @return int The weight as requested
	 */
	function get_role_weight($which = 'max')
	{
		$sql['max'] = 'SELECT MAX(weight) AS weight FROM roles';
		$sql['min'] = 'SELECT MIN(weight) AS weight FROM roles';
		
		if (!array_key_exists($which, $sql))
		{
			return false;
		}
		
		$sql_to_run = $sql[$which];
		
		$query = $this->db->query($sql_to_run);
		$row = $query->row();
		return (int) $row->weight;
	}
	
	
	
	
	/**
	 * Delete a role from the database
	 *
	 * @param int role_id Role ID to delete
	 * @return bool on successful deletion
	 */
	function delete_role($role_id = null)
	{
		if (!$role_id) return false;
		
		$sql = 'DELETE FROM roles WHERE role_id = ? LIMIT 1';
		$query = $this->db->query($sql, array($role_id));
		
		return ($this->db->affected_rows() == 1);
	}
	
	
	
	
	/**
	 * Assign an existing role to a user, group or department
	 *
	 * @param int role_id Role ID to assign
	 * @param string entity_type Type of entity to assign (U: user, D: department, G: group)
	 * @param int entity_id ID of the entity that the role is being assigned to
	 */
	function assign_role($role_id = null, $entity_type = null, $entity_id = null)
	{
		if (!$role_id) return false;
		if (!$entity_type) return false;
		if (!$entity_id) return false;
		
		$table = null;
		
		switch ($entity_type)
		{
			case 'U': $table = 'roles2users'; break;
			case 'G': $table = 'roles2groups'; break;
			case 'D': $table = 'roles2departments'; break;
		}
		
		if (!$table) return false;
		
		$msg = "Assigning role ID $role_id to $entity_type ID $entity_id.";
		log_message('debug', $msg);
		
		$sql = "INSERT INTO $table VALUES (?, ?) 
				ON DUPLICATE KEY UPDATE role_id = ?";
		$query = $this->db->query($sql, array($role_id, $entity_id, $role_id));
		
		return $query;
	}
	
	
	
	
	/**
	 * Unassign a role from a user, group or department
	 *
	 * @param int role_id Role ID to unassign
	 * @param string entity_type Type of entity to be unassign (U: user, D: department, G: group)
	 * @param int entity_id ID of the entity that the role is being unassigned from
	 */
	function unassign_role($role_id = null, $entity_type = null, $entity_id = null)
	{
		if (!$role_id) return false;
		if (!$entity_type) return false;
		if (!$entity_id) return false;
		
		$table = null;
		$key = null;
		
		switch ($entity_type)
		{
			case 'U':
				$table = 'roles2users';
				$key = 'user_id';
			break;
			case 'G':
				$table = 'roles2groups';
				$key = 'group_id';
			break;
			case 'D':
				$table = 'roles2departments';
				$key = 'department_id';
			break;
		}
		
		if (!$table) return false;
		
		$msg = "*UN*assigning role ID $role_id from $entity_type ID $entity_id.";
		log_message('debug', $msg);

		$sql = "DELETE FROM $table WHERE role_id = ? AND $key = ? LIMIT 1";
		$query = $this->db->query($sql, array($role_id, $entity_id));
		
		return ($this->db->affected_rows() == 1);
	}
	
	
	
	
	/**
	 * Get the things that roles are assigned to
	 *
	 * @param int role_id Optionally specify a single Role ID to look up
	 * @return array
	 */
	function get_role_assignments($role_id = null)
	{
		$where = null;
		
		if ($role_id != null && is_numeric($role_id))
		{
			$where = 'WHERE r2e.role_id = ?';
		}
		
		if ($role_id != null && !is_numeric($role_id))
		{
			$this->lasterr = 'Invalid Role ID format';
			return false;
		}
		
		$sql = "SELECT
					r2e.role_id,
					r2e.entity_id,
					r2e.entity_type,
					roles.weight,
					CASE
						WHEN d.name IS NOT NULL THEN d.name
						WHEN g.name IS NOT NULL THEN g.name
						WHEN u.username IS NOT NULL THEN IFNULL(u.displayname, u.username)
					END AS name
				FROM v_roles2entities AS r2e
				LEFT JOIN departments d ON r2e.entity_id = d.department_id AND r2e.entity_type = 'D'
				LEFT JOIN groups g ON r2e.entity_id = g.group_id AND r2e.entity_type = 'G'
				LEFT JOIN users u ON r2e.entity_id = u.user_id AND r2e.entity_type = 'U'
				LEFT JOIN roles ON r2e.role_id = roles.role_id
				$where
				ORDER BY roles.weight ASC, entity_type DESC";
		
		if ($role_id == null)
		{
			$query = $this->db->query($sql);
		}
		else
		{
			$query = $this->db->query($sql, array($role_id));
		}
		
		if ($query->num_rows() > 0)
		{
			$roles = array();
			$result = $query->result();
			foreach ($result as $row)
			{
				$roles[$row->role_id][] = $row;
			}
			if ($role_id == null)
			{
				return $roles;
			}
			else
			{
				return $roles[$role_id];
			}
		}
		else
		{
			$this->lasterr = 'No assignments returned';
			return false;
		}
	}
	
	
	
	
	/**
	 * Get all assigned roles for a user (via group/department membership or direct)
	 *
	 * @param int user_id User ID to get roles for
	 * @return array Array of roles in weight order (highest first)
	 */
	function get_user_roles($user_id = null)
	{
		if (!is_numeric($user_id))
		{
			$this->lasterr = 'Invalid format for User ID.';
			return false;
		}
		
		$sql = "SELECT
					r2u.role_id AS role_id,
					roles.name AS role_name,
					roles.weight AS role_weight,
					r2u.user_id AS entity_id,
					'U' AS entity_type,
					IFNULL(users.displayname, users.username) AS entity_name
				FROM
				roles2users r2u
				LEFT JOIN roles ON r2u.role_id = roles.role_id
				LEFT JOIN users ON r2u.user_id = users.user_id
				WHERE r2u.user_id = '%d'
				UNION
				SELECT
					r2d.role_id AS role_id,
					r.name AS role_name,
					r.weight AS role_weight,
					r2d.department_id AS entity_id,
					'D' AS entity_type,
					d.name AS entity_name
				FROM
				roles2departments r2d, users2departments u2d, roles r, departments d
				WHERE r2d.department_id = u2d.department_id
				AND r2d.role_id = r.role_id
				AND d.department_id = r2d.department_id
				AND u2d.user_id = '%d'
				UNION
				SELECT
					r2g.role_id AS role_id,
					roles.name AS role_name,
					roles.weight AS role_weight,
					r2g.group_id AS entity_id,
					'G' AS entity_type,
					g.name AS entity_name
				FROM
				roles2groups r2g
				LEFT JOIN roles ON r2g.role_id = roles.role_id
				LEFT JOIN groups g ON r2g.group_id = g.group_id
				WHERE r2g.group_id = (SELECT group_id FROM users WHERE user_id = '%d')
				ORDER BY role_weight ASC";
		
		$sql = sprintf($sql, $user_id, $user_id, $user_id);
		$query = $this->db->query($sql);
		
		if ($query->num_rows() > 0)
		{
			$result = $query->result();
			$roles = array();
			foreach ($result as $row)
			{
				$roles[$row->role_id] = $row;
			}
			return $roles;
		}
		else
		{
			$this->lasterr = 'No roles assigned.';
			return false;
		}
	}
	
	
	
	
	/**
	 * Get the permission values for provided role(s)
	 * 
	 * Takes role weight into consideration - ask "what can these roles do"
	 *
	 * @param mixed role_ids Integer or array of integers
	 * @return array Simple array of permission names
	 */
	function get_role_permissions($role_ids = array())
	{
		$sql_out = null;
		$sql = 'SELECT
					permissions2roles.val,
					permissions2roles.permission_id,
					permissions2roles.role_id,
					permissions.name
				FROM
				permissions2roles
				LEFT JOIN permissions ON permissions2roles.permission_id = permissions.permission_id
				LEFT JOIN roles ON permissions2roles.role_id = roles.role_id
				WHERE permissions2roles.role_id IN(%s)
				ORDER BY roles.weight DESC';
		
		if (is_numeric($role_ids))
		{
			// Single role ID
			$sql_out = sprintf($sql, (int) $role_ids);
		}
		elseif (is_array($role_ids))
		{
			// Multiple role IDs
			$sql_out = sprintf($sql, implode(',', $role_ids));
		}
		
		if ($sql_out == null)
		{
			$this->lasterr = 'Invalid format for Role ID.';
			return false;
		}
		
		$query = $this->db->query($sql_out);
		
		if ($query->num_rows() > 0)
		{
			$result = $query->result();
			// Array of permission names to be returned
			$permissions = array();
			foreach ($result as $row)
			{
				//echo "Permission {$row->name} value: {$row->val}\n";
				if ($row->val == '1')
				{
					// echo "Permission $row->name granted via role ID $row->role_id ...\n";
					$permissions[$row->permission_id] = $row->name;
				}
				if ($row->val === '0')
				{
					// echo "Permission $row->name being revoked via role ID $row->role_id...\n";
					unset($permissions[$row->permission_id]);
				}
			}
			return $permissions;
		}
		else
		{
			$this->lasterr = 'No permissions returned for supplied role(s).';
			return array();
		}
		
	}
	
	
	
	
	/**
	 * Permissions
	 * ===========
	 */
	
	
	
	
	/**
	 * List all the available permissions in the DB ordered by name
	 *
	 * @param string format	Desired array format for returned values
	 * @return array
	 */
	function get_available_permissions($format = 'ids')
	{
		$sql = 'SELECT * FROM permissions ORDER BY name ASC';
		$query = $this->db->query($sql);
		$res = $query->result();
		
		$_ids = array();
		$_sections = array();
		
		foreach ($res as $p)
		{
			$parts = explode(".", $p->name);
			$_sections[$parts[0]][$p->permission_id] = $p->name;
			$_ids[$p->permission_id] = $p->name;
		}
		
		return ($format == 'sections') ? $_sections : $_ids;
	}
	
	
	
	
	/**
	 * Set permissions for a role
	 *
	 * @param int role_id Role ID to update
	 * @param array permissions 2D array of permission_id => value
	 * @return bool
	 */
	function set_permissions($role_id = null, $permissions = array())
	{
		if (!is_numeric($role_id))
		{
			$this->lasterr = 'Invalid role ID.';
			return false;
		}
		
		if (count($permissions) == 0)
		{
			$this->lasterr = 'Permissions array is empty.';
			return false;
		}
		
		log_message('debug', "Updating permissions for Role ID $role_id.");
		
		$sql = 'INSERT INTO permissions2roles (role_id, permission_id, val) VALUES ';
		
		foreach ($permissions as $permission_id => $value)
		{
			$value = (!is_numeric($value)) ? 'NULL' : "'$value'";
			$sql .= sprintf("('%d', '%d', %s),", $role_id, $permission_id, $value);
		}
		
		$sql = preg_replace('/,$/', '', $sql);
		
		$sql .= ' ON DUPLICATE KEY UPDATE val = VALUES(val)';
		
		$query = $this->db->query($sql);
		return $query;
	}
	
	
	
	
	/**
	 * Get the available permissions for 1+ roles
	 *
	 * @param mixed role_id null: all roles. int: one role. array: multiple roles
	 * @return mixed array
	 */
	function get_permission_values($role_id = null)
	{
		// Determine what is being requested
		if ($role_id == null)
		{
			$action = 'all';
		}
		elseif (is_numeric($role_id))
		{
			$action = 'one';
		}
		elseif (is_array($role_id))
		{
			$action = 'multiple';
		}
		
		
		switch ($action)
		{
			
			case 'all':
				$sql = 'SELECT * FROM permissions2roles';
				$query = $this->db->query($sql);
				$result = $query->result();
				if ($query->num_rows() > 0)
				{
					$permissions = array();
					foreach ($result as $row)
					{
						$permissions[$row->role_id][$row->permission_id] = $row->val;
					}
				}
			break;
			
			case 'one':
				$sql = 'SELECT * FROM permissions2roles WHERE role_id = ?';
				$query = $this->db->query($sql, array($role_id));
				$result = $query->result();
				if ($query->num_rows() > 0)
				{
					$permissions = array();
					foreach ($result as $row)
					{
						$permissions[$row->permission_id] = $row->val;
					}
				}
			break;
			
			case 'multiple':
				$instr = implode(",", $role_id);
				$sql = "SELECT * FROM permissions2roles WHERE role_id IN ($instr)";
				$query = $this->db->query($sql);
				$result = $query->result();
				if ($query->num_rows() > 0)
				{
					$permissions = array();
					foreach ($result as $row)
					{
						$permissions[$row->role_id][] = array(
							$row->permission_id => $row->val
						);
					}
				}
			break;
			
		}		// end switch
		
		if (!is_array($permissions))
		{
			$this->lasterr = 'Could not find requested permissions';
			return false;
		}
		else
		{
			return $permissions;
		}
		
	}
	
	
	
	
	/**
	 * Get actual permissions that user is permitted to do
	 *
	 * @param int user_id User ID to get permissions for
	 * @return array Array of permissions (id => name)
	 */
	function get_permissions_for_user($user_id = null)
	{
		if (!is_numeric($user_id))
		{
			$this->lasterr = 'User ID not in expected format or not supplied.';
			return false;
		}
		
		$user_roles = $this->get_user_roles($user_id);
		if (is_array($user_roles))
		{
			foreach ($user_roles as $r)
			{
				$roles[] = $r->role_id;
			}
			// Now get permissions for those roles (id => name);
			$permissions = $this->get_role_permissions($roles);
			return $permissions;
		}
		else
		{
			$this->lasterr = 'User does not have any roles.';
			return false;
		}
	}
	
	
	
	
	/**
	 * Save a user's permissions to the cache table for quick retrieval
	 *
	 * @param int user_id ID of user to save permissions for
	 * @return bool
	 */
	function save_to_cache($user_id = null)
	{
		if (!is_numeric($user_id))
		{
			log_message('debug', 'save_to_cache(): Invalid User ID: $user_id');
			$this->lasterr = 'User ID not in expected format.';
			return false;
		}
		
		$permissions = $this->get_permissions_for_user($user_id);
		
		if (!is_array($permissions))
		{
			return false;
		}
		
		$serialised = serialize($permissions);
		
		$sql = 'INSERT INTO permission_cache (user_id, permissions)
				VALUES (?, ?)
				ON DUPLICATE KEY UPDATE permissions = VALUES(permissions)';
		$query = $this->db->query($sql, array($user_id, $serialised));
		
		log_message('debug', "Permissions model: save_to_cache(): permissions for user ID $user_id saved to cache.");
		
		return ($this->db->affected_rows() == 1);
	}
	
	
	
	
	/**
	 * Get a user's permissions from the cache table.
	 *
	 * If not cached, will get permissions and set the cache
	 *
	 * @param int user_id ID of user to get permissions for
	 * @return array
	 */
	function get_from_cache($user_id = null)
	{
		if (!is_numeric($user_id))
		{
			$this->lasterr = 'User ID not in expected format.';
			return false;
		}
		
		$sql = 'SELECT permissions FROM permission_cache 
				WHERE user_id = ? LIMIT 1';
		$query = $this->db->query($sql, array($user_id));
		
		if ($query->num_rows() == 1)
		{
			// Got permissions from the cache
			log_message('debug', "Permissions model: get_from_cache(): got permissions for user id $user_id from cache.");
			$row = $query->row();
			$permissions = unserialize($row->permissions);
			return $permissions;
		}
		else
		{
			// No cache entry - make it!
			log_message('debug', "Permissions model: get_from_cache(): none found for user ID $user_id. Now caching...");
			$save = $this->save_to_cache($user_id);
			if ($save == true)
			{
				return $this->get_from_cache($user_id);
			}
			else
			{
				// Couldn't save them for some reason!
				return false;
			}
		}
		
	}
	
	
	
	
	/**
	 * Clear permissions from the cache table (all, or for one user)
	 *
	 * Clear all permissions when any permissions are changed.
	 * Clear a user's permissions on logout
	 *
	 * @param int user_id ID of user to clear permissions for
	 * @return bool True on successful removal
	 */
	function clear_cache($user_id = null)
	{
		if ($user_id == null)
		{
			// Clear all permissions
			$sql = 'DELETE FROM permission_cache';
			$query = $this->db->query($sql);
		}
		else
		{
			if (!is_numeric($user_id))
			{
				$this->lasterr = 'Invalid User ID $user_id.';
				return false;
			}
			$sql = 'DELETE FROM permission_cache WHERE user_id = ? LIMIT 1';
			$query = $this->db->query($sql, array($user_id));
		}
		return $query;
	}
	
	
	
	
}