<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Classroombookings. Hassle-free resource booking for schools. <http://classroombookings.com/>
 * Copyright (C) 2006-2011 Craig A Rodway <craig.rodway@gmail.com>
 *
 * This file is part of Classroombookings.
 * Classroombookings is licensed under the Affero GNU GPLv3 license.
 * Please see license-classroombookings.txt for the full license text.
 */


class Departments_model extends CI_Model
{
	
	
	function __construct()
	{
		parent::__construct();
		$this->lasterr = null;		
	}
	
	
	
	
	/**
	 * get one or more departments
	 *
	 * @param int department_id
	 * @return mixed (object on success, false on failure)
	 */
	function get($department_id = null)
	{
		if ($department_id == null)
		{
			// Getting all departments
			$sql = "SELECT
						d.*,
						GROUP_CONCAT(ldapgroups.name SEPARATOR ', ') AS ldap_groups,
						(SELECT COUNT(user_id) FROM users2departments WHERE department_id = d.department_id) AS user_count
					FROM departments d
					LEFT JOIN departments2ldapgroups d2ldap USING(department_id)
					LEFT JOIN ldapgroups USING(ldapgroup_id)
					GROUP BY department_id";
			
			$query = $this->db->query($sql);
			if ($query->num_rows() > 0)
			{
				return $query->result();
			}
			else
			{
				$this->lasterr = 'There are no departments.';
				return 0;
			}
		}
		else
		{
			if (!is_numeric($department_id))
			{
				$this->lasterr = 'Invalid Department ID';
				return FALSE;
			}
			
			// Getting one department
			$sql = 'SELECT * FROM departments WHERE department_id = ? LIMIT 1';
			$query = $this->db->query($sql, array($department_id));
			
			if ($query->num_rows() == 1)
			{
				// Got the department
				$department = $query->row();
				$department->ldapgroups = array();
				
				// Fetch the LDAP groups that are mapped (if any)
				$sql = 'SELECT ldapgroup_id FROM departments2ldapgroups 
						WHERE department_id = ?';
				$query = $this->db->query($sql, array($department_id));
				
				if ($query->num_rows() > 0)
				{
					$ldapgroups = array();
					foreach ($query->result() as $row)
					{
						array_push($ldapgroups, $row->ldapgroup_id);
					}
					
					// Assign array of LDAP groups to main group object that is to be returned
					$department->ldapgroups = $ldapgroups;
					unset($ldapgroups);
				}
				return $department;
			}
			else
			{
				return false;
			}		// query->num_rows == 1
		}		// department_id == null 
	}		// function
	
	
	
	
	function add($data){
		$data['created'] = date("Y-m-d");
		
		// If no LDAP groups, set empty array. Otherwise assign to new array for itself
		if(in_array(-1, $data['ldapgroups'])){
			$ldapgroups = array();
		} else {
			$ldapgroups = $data['ldapgroups'];
		}
		
		// Remove ldapgroups from the main data array (no 'ldapgroups' column)
		unset($data['ldapgroups']);
		
		$add = $this->db->insert('departments', $data);
		
		$department_id = $this->db->insert_id();
		
		// If LDAP groups were assigned then insert into DB now we have the group ID
		if(count($ldapgroups) > 0){
			$sql = 'INSERT INTO departments2ldapgroups (department_id, ldapgroup_id) VALUES ';
			foreach($ldapgroups as $ldapgroup_id){
				$sql .= sprintf("(%d,%d),", $department_id, $ldapgroup_id);
			}
			// Remove last comma
			$sql = preg_replace('/,$/', '', $sql);
			$query = $this->db->query($sql);
			if($query == FALSE){
				$this->lasterr = 'Could not assign LDAP groups to department';
			}
		}
		
		return $department_id;
	}
	
	
	
	
	function edit($department_id = NULL, $data){
		
		if($department_id == NULL){
			$this->lasterr = 'Cannot update a department without its ID.';
			return FALSE;
		}
		
		
		
		// If no LDAP groups, set empty array. Otherwise assign to new array for itself
		if(in_array(-1, $data['ldapgroups'])){
			$ldapgroups = array();
		} else {
			$ldapgroups = $data['ldapgroups'];
		}
		// Remove 'column' from data array
		unset($data['ldapgroups']);
		
		#die(print_r($ldapgroups));
		#die();
		
		// Update department info
		$this->db->where('department_id', $department_id);
		$edit = $this->db->update('departments', $data);
		
		// Now remove LDAP group assignments (don't panic - will now re-insert if they are specified)
		$sql = 'DELETE FROM departments2ldapgroups WHERE department_id = ?';
		$query = $this->db->query($sql, array($department_id));
		
		// If LDAP groups were assigned then insert into DB
		#die(count($ldapgroups));
		if(count($ldapgroups) > 0){
			$sql = 'INSERT INTO departments2ldapgroups (department_id, ldapgroup_id) VALUES ';
			foreach($ldapgroups as $ldapgroup_id){
				$sql .= sprintf("(%d,%d),", $department_id, $ldapgroup_id);
			}
			// Remove last comma
			$sql = preg_replace('/,$/', '', $sql);
			$query = $this->db->query($sql);
			if($query == FALSE){
				$this->lasterr = 'Could not assign LDAP groups';
			}
		}
		
		return $edit;
	}
	
	
	
	
	function delete($department_id){
		
		$sql = 'DELETE FROM departments WHERE department_id = ? LIMIT 1';
		$query = $this->db->query($sql, array($department_id));
		
		if($query == FALSE){
			
			$this->lasterr = 'Could not delete department. Does it exist?';
			return FALSE;
			
		} else {
			
			/* $sql = 'DELETE FROM bookings WHERE user_id = ?';
			$query = $this->db->query($sql, array($user_id));
			if($query == FALSE){ $failed[] = 'bookings'; }*/
			
			/*$sql = 'UPDATE rooms SET user_id = NULL WHERE user_id = ?';
			$query = $this->db->query($sql, array($user_id));
			if($query == FALSE){ $failed[] = 'rooms'; }
			
			if(isset($failed)){
				$this->lasterr = 'The user was deleted successfully, but an error occured while removing their bookings and/or updating any rooms they owned.';
			}*/
			
			return TRUE;
			
		}
		
	}
	
	
	
	
	// CR 2009-06-25: Redundant?
	/*
	function get_groups_dropdown(){
		$sql = 'SELECT group_id, name FROM groups ORDER BY name ASC';
		$query = $this->db->query($sql);
		if($query->num_rows() > 0){
			$result = $query->result();
			$groups = array();
			foreach($result as $group){
				$groups[$group->group_id] = $group->name;
			}
			return $groups;
		} else {
			$this->lasterr = 'No groups found';
			return FALSE;
		}
	}*/
	
	
	
	
	function get_department_name($department_id){
		if($department_id == NULL || !is_numeric($department_id)){
			$this->lasterr = 'No department ID given or invalid data type.';
			return FALSE;
		}
		
		$sql = 'SELECT name FROM departments WHERE department_id = ? LIMIT 1';
		$query = $this->db->query($sql, array($department_id));
		
		if($query->num_rows() == 1){
			$row = $query->row();
			return $row->name;
		} else {
			$this->lasterr = sprintf('The department supplied (ID: %d) does not exist.', $department_id);
			return FALSE;
		}
	}
	
	
	
	
	function get_dropdown($none = FALSE){
		$sql = 'SELECT department_id, name FROM departments ORDER BY name ASC';
		$query = $this->db->query($sql);
		if($query->num_rows() > 0){
			$result = $query->result();
			$departments = array();
			if($none == TRUE){
				$departments[-1] = '(None)';
			}
			foreach($result as $department){
				$departments[$department->department_id] = $department->name;
			}
			return $departments;
		} else {
			$this->lasterr = 'No departmentss found';
			return FALSE;
		}
	}
	
	
	
	
}

/* End of file: app/models/departments_model.php */