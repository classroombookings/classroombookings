<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Classroombookings. Hassle-free resource booking for schools. <http://classroombookings.com/>
 * Copyright (C) 2006-2011 Craig A Rodway <craig.rodway@gmail.com>
 *
 * This file is part of Classroombookings.
 * Classroombookings is licensed under the Affero GNU GPLv3 license.
 * Please see license-classroombookings.txt for the full license text.
 */

class Permissions_model extends CI_Model
{


	var $lasterr;
	
	private $allowed_entity_types = array('E', 'D', 'G', 'U');


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
	 * Add a new permission entry
	 */
	/* function add($data)
	{
		// Ensure it's uppercase
		$data['entity_type'] = strtoupper($data['entity_type']);
		
		// Check it's a valid type
		if (!in_array($data['entity_type'], $this->allowed_entity_types))
		{
			$this->lasterr = 'Entity type not recognised';
			return false;
		}
		
		// Check there's an ID
		// TODO: Extra checks to make sure entity_id exists
		if ($data['entity_type'] != 'E' && !is_numeric($data['entity_id']))
		{
			$this->lasterr = 'Invalid entity ID';
			return false;
		}
		
		// Check for permissions
		if (!is_array($data['permissions']) OR empty($data['permissions']))
		{
			$this->lasterr = 'No permissions to save';
			return false;
		}
		
		// Generate the ID string
		if ($data['entity_type'] != 'E')
		{
			$data['permission_id'] = sprintf("%s%d", 
				$data['entity_type'], $data['entity_id']);
		}
		else
		{
			$data['permission_id'] = 'E';
		}
		
		// Now we have an ID, check it doesn't already exist. It *shouldn't*...
		if ($this->exists($data['permission_id']))
		{
			$this->lasterr = 'Permission already exists!';
			return false;
		}
		
		// Create an array for each row to be inserted
		$entries = array();
		// Loop through each permission and make a new row
		foreach ($data['permissions'] as $k => $v)
		{
			$item = array();
			$item['permission_id'] = $data['permission_id'];
			$item['entity_type'] = $data['entity_type'];
			$item['entity_id'] = $data['entity_id'];
			$item['name'] = $k;
			$item['value'] = trim($v);
			$entries[] = $item;
		}
		// Insert those rows!
		$ret = $this->db->insert_batch('permissions', $entries);
		
		return $ret;
		
	} */
	
	
	
	
	/**
	 * Get all the permission values for a given ID
	 */
	/* function get_values($permission_id)
	{
		$sql = 'SELECT name, value FROM permissions WHERE permission_id = ?';
		$query = $this->db->query($sql, array($permission_id));
		
		$vals = array();
		
		if ($query->num_rows() > 0)
		{
			$items = $query->result();
			foreach ($items as $item)
			{
				$vals[$item->name] = $item->value;
			}
			return $vals;
		}
		else
		{
			$this->lasterr = "Could not find any entries for permission ID $permission_id";
			return false;
		}
	}
	*/
	
	
	
	/**
	 * Get a list of all the defined permissions
	 */
	/*function get_list()
	{
		$query = $this->db->get('v_permissions_list');
		$permissions_list = $query->result();
		return $permissions_list;
	}
	*/
	
	
	
	
	/**
	 * Check if a permission entry exists
	 */
	// TODO: Code it up.
	/*function exists($permission_id)
	{
		return false;
	}
	*/
	
	
	
	/*
	function entity_name($entity_type)
	{
		$types['E'] = 'Everyone';
		$types['D'] = 'Department';
		$types['G'] = 'Groups';
		$types['U'] = 'User';
		if (array_key_exists($entity_type, $types))
		{
			return $types[$entity_type];
		}
		else
		{
			return false;
		}
	}
	*/
	
	
	
}