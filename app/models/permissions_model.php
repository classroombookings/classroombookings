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
	 * Add a new permission entry
	 */
	function add($data)
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
		
	}
	
	
	
	
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