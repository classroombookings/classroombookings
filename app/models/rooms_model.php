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
	var $types;
	
	
	function Rooms_model(){
		parent::Model();
		
		// Object types for permissions
		$this->types['e'] = 'Everyone';
		$this->types['o'] = 'Owner';
		$this->types['u'] = 'User';
		$this->types['g'] = 'Group';
		$this->types['d'] = 'Department';
	}
	
	
	
	
	/**
	 * Link definitions of pages in this section
	 *
	 * @return	array
	 */
	function subnav(){
		$subnav = array();
		// Other pages in this parent section
		$subnav[] = array('rooms/manage', 'Rooms', 'rooms');
		$subnav[] = array('rooms/attributes', 'Attributes', 'rooms.attrs');
		return $subnav;
	}
	
	
	
	
	/**
	 * Get one or more room details along with booking permissions
	 *
	 * @param	int		room_id		ID of a room to get if only one is desired
	 * @param	int		page		Pagination values
	 * @return	mixed				Array object of items or 0 on failure
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
				// Got the room - get all fields
				$room = $query->row();
				// Get the permissions for this room
				#$room->permissions = $this->get_room_permissions($room_id);
				return $room;
			} else {
				return FALSE;
			}
			
		}
		
	}
	
	
	
	
	/**
	 * Get list of room categories in array format of cat_id => name
	 *
	 * @param bool $none	Add an entry of index -1 and name of None
	 * @return array		Array of categories in cat_id => name format
	 */
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
	
	
	
	
	/**
	 * Get the list of rooms arranged in categories
	 *
	 * @return mixed	Array of rooms in categories on success, 0 on failure
	 */
	function get_in_categories(){
		
		$sql = 'SELECT 
					rooms.*, 
					rcs.name AS cat_name, 
					IFNULL(users.displayname, users.username) AS owner_name
				FROM rooms
				LEFT JOIN roomcategories AS rcs ON rooms.category_id = rcs.category_id
				LEFT JOIN users ON rooms.user_id = users.user_id
				ORDER BY rcs.name ASC, rooms.name ASC';
		
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
	
	
	
	
	/**
	 * Add a room the database
	 *
	 * @param	array	data		Array of DB fields => values to insert into the database
	 * @return	mixed				ID of the new room on success, FALSE on failure
	 */
	function add($data){
		
		$data['created'] = date("Y-m-d");
		
		// Insert new room into DB
		$add = $this->db->insert('rooms', $data);
		$room_id = $this->db->insert_id();
		
		// Return the ID on success
		if($add != FALSE){	
			return $room_id;
		} else {
			return FALSE;
		}
		
	}
	
	
	
	
	/**
	 * Update a room
	 *
	 * @param	int		room_id		ID of the room to update
	 * @param	array	data		Array of DB fields => values to update with
	 * @return	bool				TRUE on success, FALSE on failure
	 */
	function edit($room_id = NULL, $data){
		
		if($room_id == NULL){
			$this->lasterr = 'Cannot update a room without its ID.';
			return FALSE;
		}
		
		// Update room info
		$this->db->where('room_id', $room_id);
		$edit = $this->db->update('rooms', $data);
		
		return $edit;
		
	}
	
	
	
	
	/**
	 * Delete a room from the database
	 *
	 * @param	room_id		Room ID to delete
	 * @return	bool
	 */
	function delete($room_id){
		
		$sql = 'DELETE FROM rooms WHERE room_id = ? LIMIT 1';
		$query = $this->db->query($sql, array($room_id));
		
		if($this->db->affected_rows() != 1){
			$this->lasterr = 'Could not delete room. Does it exist?';
			return FALSE;
		} else {
			return TRUE;
		}
		
	}
	
	
	
	
	/**
	 * Add a new room category to the database
	 *
	 * @param	str		name	Name of the category to add
	 * @return	mixed			ID of category on success, FALSE on failure
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
	
	
	
	
	// ---------- PERMISSIONS ---------- //
	
	
	
	
	/**
	 * Get room permission entries for a single room
	 *
	 * @param	int		room_id		Room ID to get permissions for
	 * @return	array
	 */
	function get_permissions($room_id){
		
		$sql = 'SELECT 
					`room-permissions`.entry_id, 
					`room-permissions`.type, 
					`room-permissions`.user_id, 
					`room-permissions`.group_id, 
					`room-permissions`.department_id, 
					`room-permissions`.permissions,
					IFNULL(users.displayname, users.username) AS user_name,
					departments.name AS department_name,
					groups.name AS group_name
				FROM `room-permissions`
				LEFT JOIN users ON `room-permissions`.user_id = users.user_id
				LEFT JOIN departments ON `room-permissions`.department_id = departments.department_id
				LEFT JOIN groups ON `room-permissions`.group_id = groups.group_id
				WHERE room_id = ?';
		
		$query = $this->db->query($sql, $room_id);
		
		if($query->num_rows() > 0){
			
			$entries = array();
			$result = $query->result();
			
			// Got rows
			foreach($result as $entry){
				
				#print_r($entry);
				
				$e['type'] = $entry->type;
				$e['permissions'] = unserialize($entry->permissions);
				
				switch($entry->type){
					case 'u':
						$e['object_id'] = $entry->user_id;
						$e['object_name'] = $entry->user_name;
					break;
					case 'g':
						$e['object_id'] = $entry->group_id;
						$e['object_name'] = $entry->group_name;
					break;
					case 'd':
						$e['object_id'] = $entry->department_id;
						$e['object_name'] = $entry->department_name;
					break;
				}
				
				$entries[$entry->entry_id] = $e;
				unset($e);
				
			}
			
			#print_r($entries);
			return $entries;
			
		} else {
			
			$this->lasterr = 'No permission entries have been defined for the given room.';
			return FALSE;
			
		}
		
	}
	
	
	
	
	/**
	 * Get a single room permission entry by ID
	 *
	 * @param	entry_id		Permission entry ID
	 * @return	array
	 */
	function get_permission_entry($entry_id){
		
		if(empty($entry_id)){
			$this->lasterr = 'No entry ID supplied';
			return FALSE;
		}
		
		$sql = 'SELECT 
					`room-permissions`.entry_id, 
					`room-permissions`.type, 
					`room-permissions`.user_id, 
					`room-permissions`.group_id, 
					`room-permissions`.department_id, 
					`room-permissions`.permissions,
					IFNULL(users.displayname, users.username) AS user_name,
					departments.name AS department_name,
					groups.name AS group_name,
					rooms.name AS room_name
				FROM `room-permissions`
				LEFT JOIN users ON `room-permissions`.user_id = users.user_id
				LEFT JOIN departments ON `room-permissions`.department_id = departments.department_id
				LEFT JOIN groups ON `room-permissions`.group_id = groups.group_id
				LEFT JOIN rooms ON `room-permissions`.room_id = rooms.room_id
				WHERE `room-permissions`.entry_id = ? LIMIT 1';
		
		$query = $this->db->query($sql, $entry_id);
		
		if($query->num_rows() == 1){
			// Got one entry
			$entry = $query->row();
			
			$typename = $this->types[$entry->type];
			$format = "%s '%s'";
			
			switch($entry->type){
				case 'e':
				case 'o':
					$entry->nicename = $typename;
				break;
				case 'u':
					$entry->nicename = sprintf($format, strtolower($typename), $entry->user_name);
				break;
				case 'g':
					$entry->nicename = sprintf($format, strtolower($typename), $entry->group_name);
				break;
				case 'd':
					$entry->nicename = sprintf($format, strtolower($typename), $entry->department_name);
				break;
			}
			
			return $entry;
			
		} else {
			$this->lasterr = 'No entries found for that ID';
			return FALSE;
		}
		
	}
	
	
	
	
	/**
	 * Add a permission entry to a room
	 *
	 * @param	data	Array of table cols => values
	 * @return	bool/int		ID of new permission entry on success
	 */
	function add_permission_entry($data){
		
		if(empty($data)){
			$this->lasterr = 'Data array empty!';
			return FALSE;
		}
		
		// Check if this combination already exists
		$sql = 'SELECT entry_id FROM `room-permissions` WHERE hash = ? LIMIT 1';
		$query = $this->db->query($sql, $data['hash']);
		if($query->num_rows() == 1){
			$this->lasterr = 'That combination of permissions already exists for this room.';
			return FALSE;
		}
		
		// Add to database
		$add = $this->db->insert('room-permissions', $data);
		$entry_id = $this->db->insert_id();
		
		// Check result of insert
		if($add != FALSE){
			return $entry_id;
		} else {
			$this->lasterr = 'Database error when adding entry.';
			return FALSE;
		}
		
	}
	
	
	
	
	/**
	 * Delete a room permission entry from the database
	 *
	 * @param	entry_id	Entry ID to delete
	 */
	function delete_permission($entry_id){
		
		$sql = 'DELETE FROM `room-permissions` WHERE entry_id = ? LIMIT 1';
		$query = $this->db->query($sql, array($entry_id));
		
		if($this->db->affected_rows() != 1){
			$this->lasterr = 'Could not delete room permission entry. Does it exist?';
			return FALSE;
		} else {
			return TRUE;
		}
		
	}
	
	
	
	
	
	// ---------- ATTRIBUTES ---------- //
	
	
	
	
	
	/**
	 * Get all attribute field names
	 */
	function get_all_attr_fields(){
	
	}
	
	
	
	
}

/* End of file: app/models/rooms_model.php */