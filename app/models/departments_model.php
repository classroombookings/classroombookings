<?php
/*
	This file is part of Classroombookings.

	Classroombookings is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	Classroombookings is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with Classroombookings.  If not, see <http://www.gnu.org/licenses/>.
*/


class Departments_model extends Model{


	var $lasterr;
	
	
	function Departments_model(){
		parent::Model();
	}
	
	
	
	
	/**
	 * get one or more departments
	 *
	 * @param int department_id
	 * @param arr pagination limit,start
	 * @return mixed (object on success, false on failure)
	 */
	function get($department_id = NULL, $page = NULL){
		
		if ($department_id == NULL) {
		
			// Getting all departments
			$this->db->select('*', FALSE);
			$this->db->from('departments');
			
			$this->db->orderby('name ASC');
			
			if (isset($page) && is_array($page)) {
				$this->db->limit($page[0], $page[1]);
			}
			
			$query = $this->db->get();
			if ($query->num_rows() > 0){
				return $query->result();
			} else {
				$this->lasterr = 'There are no departments.';
				return 0;
			}
			
		} else {
			
			if (!is_numeric($department_id)) {
				return FALSE;
			}
			
			// Getting one department
			$sql = 'SELECT * FROM departments WHERE department_id = ? LIMIT 1';
			$query = $this->db->query($sql, array($department_id));
			
			if($query->num_rows() == 1){
				
				// Got the department
				$department = $query->row();
				$department->ldapgroups = array();
				
				// Fetch the LDAP groups that are mapped (if any)
				$sql = 'SELECT ldapgroup_id FROM departments2ldapgroups WHERE department_id = ?';
				$query = $this->db->query($sql, array($department_id));
				if($query->num_rows() > 0){
					$ldapgroups = array();
					foreach($query->result() as $row){
						array_push($ldapgroups, $row->ldapgroup_id);
					}
					// Assign array of LDAP groups to main group object that is to be returned
					$department->ldapgroups = $ldapgroups;
					unset($ldapgroups);
				}
				
				return $department;
			} else {
				return FALSE;
			}
			
		}
		
	}
	
	
	
	
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
		
		// Update department info
		$this->db->where('department_id', $department_id);
		$edit = $this->db->update('departments', $data);
		
		// Now remove LDAP group assignments (don't panic - will now re-insert if they are specified)
		$sql = 'DELETE FROM departments2ldapgroups WHERE department_id = ?';
		$query = $this->db->query($sql, array($group_id));
		
		// If LDAP groups were assigned then insert into DB
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
	
	
	
	
	function delete_department($department_id){
		
		$sql = 'DELETE FROM departments WHERE department_id = ? LIMIT 1';
		$query = $this->db->query($sql, array($user_id));
		
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
	}
	
	
	
	
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
	
	
	
	
}

/* End of file: app/models/departments.php */