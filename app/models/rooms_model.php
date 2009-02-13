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
			return $week_id;
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
	 */
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
	
	
	
	
}

/* End of file: app/models/rooms_model.php */