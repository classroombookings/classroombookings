<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Classroombookings. Hassle-free resource booking for schools. <http://classroombookings.com/>
 * Copyright (C) 2006-2011 Craig A Rodway <craig.rodway@gmail.com>
 *
 * This file is part of Classroombookings.
 * Classroombookings is licensed under the Affero GNU GPLv3 license.
 * Please see license-classroombookings.txt for the full license text.
 */

class Permission_model extends CI_Model
{


	var $lasterr;
	
	private $allowed_entity_types = array('E', 'D', 'G', 'U');


	function __construct()
	{
		parent::__construct();
	}
	
	
	
	
	/**
	 * Add a new permission entry
	 */
	function add($data)
	{
		// Ensure it's uppercase
		$data['entity_type'] = strtoupper($data['entity_type']);
		
		// Check it's a valid type
		if (!in_array($data['entity_type']))
		{
			$this->lasterr = 'Entity type not recognised';
			return false;
		}
		
		// Check there's an ID
		// TODO: Extra checks to make sure entity_id exists
		if (!is_numeric($data['entity_id']))
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
			$permission_id = sprintf("%s%d", $data['entity_type'], $data['entity_id']);
		}
		else
		{
			$permission_id = 'E';
		}
		
		
		
	}	
	
}