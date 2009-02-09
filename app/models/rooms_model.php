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


class Rooms_model extends Model{


	var $lasterr;
	
	
	function Rooms_model(){
		parent::Model();
		
	}
	
	
	
	
	/**
	 * get one or more room
	 */
	function get($room_id = NULL, $page = NULL){
		if ($room_id == NULL){
			// Getting all rooms
			/*$this->db->select('rooms.*, users.user_id, users.username, roomcategories');
			$this->db->from('rooms');
			
			$this->db->join('
			
			$this->db->orderby('category_id ASC, name ASC');*/
			
			$limit = 0;
			if(isset($page) && is_array($page)){
				#$this->db->limit($page[0], $page[1]);
			}
			
			$query = $this->db->get();
			
			if($query->num_rows() > 0){
				return $query->result();
			} else {
				$this->lasterr = 'There are no rooms.';
				return 0;
			}
		} else {
			if(!is_numeric($room_id)){
				return FALSE;
			}
			
			// Getting one room
			$sql = 'SELECT * FROM rooms WHERE room_id = ? LIMIT 1';
			$query = $this->db->query($sql, array($room_id));
			
			if($query->num_rows() == 1){
				// Got the room
				$room = $query->row();
				return $room;
			} else {
				return FALSE;
			}
			
		}
		
	}
	
	
	
	
	function get_categories_dropdown($none = FALSE){
		$sql = 'SELECT category_id, name FROM roomcategories ORDER BY name ASC';
		$query = $this->db->query($sql);
		if($query->num_rows() > 0){
			$result = $query->result();
			$cats = array();
			if($none == TRUE){
				$cats[-1] = '(None)';
			}
			foreach($result as $cat){
				$cats[$cat->category_id] = $cat->name;
			}
			return $cats;
		} else {
			$this->lasterr = 'No room categories found';
			return FALSE;
		}
	}
	
	
	
	
	function get_in_categories(){
		
		$sql = 'SELECT 
					rooms.*, 
					rcs.name AS cat_name, 
					IFNULL(users.displayname, users.username) AS owner_name
				FROM rooms
				LEFT JOIN roomcategories AS rcs ON rooms.category_id = rcs.category_id
				LEFT JOIN users ON rooms.user_id = users.user_id
				ORDER BY rooms.category_id ASC, rooms.name ASC';
		
		$query = $this->db->query($sql);
		
		if($query->num_rows() > 0){
			$rooms = array();
			$result = $query->result();
			foreach($result as $row){
				if($row->category_id == NULL){ $row->category_id = -1; }
				if(!array_key_exists($row->category_id, $rooms)){
					$rooms[$row->category_id] = array();
				}
				array_push($rooms[$row->category_id], $row);
			}
			return $rooms;
		} else {
			$this->lasterr  = 'No rooms have been added yet.';
			return 0;
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
	
	
	
	
	function delete($room_id){
		
		$sql = 'DELETE FROM rooms WHERE room_id = ? LIMIT 1';
		$query = $this->db->query($sql, array($room_id));
		
		if($query == FALSE){
			
			$this->lasterr = 'Could not delete room. Does it exist?';
			return FALSE;
			
		} else {
			
			return TRUE;
			
		}
		
	}
	
	
	
	
	/**
	 * Add a new room category to the database
	 */
	function add_category($name){
		$sql = 'INSERT INTO roomcategories 
				(category_id, name) VALUES 
				(NULL, ?) 
				ON DUPLICATE KEY UPDATE category_id = LAST_INSERT_ID(category_id), name = name';
		$query = $this->db->query($sql, array($name));
		
		if($this->db->affected_rows() == 1){
			return $this->db->insert_id();
		} else {
			return FALSE;
		}
	}
	
	
	
	
}

/* End of file: app/models/rooms_model.php */