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
	var $fieldtypes;
	
	
	function Rooms_model(){
		parent::Model();
		
		// Object types for permissions
		$this->types['e'] = 'Everyone';
		$this->types['o'] = 'Owner';
		$this->types['u'] = 'User';
		$this->types['g'] = 'Group';
		$this->types['d'] = 'Department';
		
		// Types for atrribute fields
		$this->fieldtypes['text'] = 'Text';
		$this->fieldtypes['select'] = 'Drop-down list';
		$this->fieldtypes['check'] = 'Tick box';
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
				// Get the field values for this room
				$room->attrs = $this->get_attr_values($room_id);
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
	 * Get attribute field data
	 *
	 * @param	int		Field ID (if retrieving a single one. Ignore if wanting all fields)
	 * @return	array object
	 */
	function get_attr_field($field_id = NULL){
		
		if ($field_id == NULL){
			
			$this->db->orderby('name ASC, type ASC');
			$query = $this->db->get('roomattrs-fields');
			
			if($query->num_rows() > 0){
				$result = $query->result();
				foreach($result as &$field){
					if($field->type == 'select'){
						$field->options = $this->_get_attr_options($field->field_id);
					}
				}
				return $result;
			} else {
				$this->lasterr = 'No fields have been created yet.';
				return FALSE;
			}
			
		} else {
			
			if(!is_numeric($field_id)){
				return FALSE;
			}
			
			// Getting one field
			$sql = 'SELECT * FROM `roomattrs-fields` WHERE field_id = ? LIMIT 1';
			$query = $this->db->query($sql, array($field_id));
			
			if($query->num_rows() == 1){
				// Got the room - get all fields
				$field = $query->row();
				
				if($field->type == 'select'){
					// Get the options [array] for this drop-down list
					$field->options = $this->_get_attr_options($field_id);
				}
				return $field;
			} else {
				return FALSE;
			}
			
		}
		
	}
	
	
	
	
	/**
	 * Add a field
	 *
	 * @param	array	Array of data
	 * @return	int		ID of new field on success
	 */
	function add_field($data){
		
		// Move the drop-down options into separate array if exists
		if(isset($data['options']) && $data['type'] == 'select'){
			// Move the options into another variable
			$options = $data['options'];
			// Remove it from the main data array so it isn't put in a column in this table
			unset($data['options']);
			// Make an md5 of the options being supplied
			$data['options_md5'] = md5(serialize($options));
		}
		
		// Insert new field into DB
		$add = $this->db->insert('roomattrs-fields', $data);
		$field_id = $this->db->insert_id();
		
		if($add != FALSE){	
			
			// Now we have the field_id - add options if we have them
			if(isset($options) && $data['type'] == 'select'){
				$sql = 'INSERT INTO `roomattrs-options` (option_id, field_id, value) VALUES ';
				foreach($options as $option){
					$sql .= sprintf("(NULL, %d, '%s'),", $field_id, $option);
				}
				$sql = preg_replace('/,$/', '', $sql);
				$query = $this->db->query($sql);
				if($query == FALSE){
					$this->lasterr = 'Could not insert values of drop-down box.';
				}
			}
			
			// Return the ID on success
			return $field_id;
			
		} else {
			
			// Something went wrong
			return FALSE;
			
		}
		
	}
	
	
	
	
	/**
	 * Update a field
	 *
	 * It is not appropriate to change a field's type once it's been created, because 
	 * strange things can happen if they have already been applied to rooms and have values.
	 *
	 * We also only need to update the options for 'select' types if the md5 hash is different
	 * from what is already in the DB.
	 *
	 * @param	field_id	Field ID to update
	 * @param	array		Data to update
	 */
	function edit_field($field_id = NULL, $data){
		
		if($field_id == NULL){
			$this->lasterr = 'Cannot update a field without its ID.';
			return FALSE;
		}
		
		// Get current info about the field
		$field = $this->get_attr_field($field_id);
		
		// If current field is of type 'select', then we need to check the md5s
		if($field->type == 'select'){
			
			log_message('debug', 'Field that is being edited exists as a select type');
			
			// Make an md5 of the options being supplied
			$data['options_md5'] = md5(serialize($data['options']));
			
			log_message('debug', 'MD5 of current options: ' . $field->options_md5);
			log_message('debug', 'MD5 of new options: ' . $data['options_md5']);
			
			if($data['options_md5'] == $field->options_md5){
				
				// Options are the same, no need to update
				log_message('debug', 'The md5 hash of the old field options matches the new md5');
				
			} else {
				
				/*
					Uh-oh - options have changed!
					1. Need to remove value assignments from rooms that have values from this field
					2. Delete 'old' field options
					3. Insert new field options
					4. Notification that old values have been removed (hmmm... how to implement?)
					
					Point (1) can probably be done with SQL's ON DELETE CASCADE
				*/
				
				// Remove values assigned to rooms
				$sql = 'DELETE FROM `roomattrs-values` WHERE field_id = ?';
				$query = $this->db->query($sql, array($field_id));
				
				// Remove options
				$sql = 'DELETE FROM `roomattrs-options` WHERE field_id = ?';
				$query = $this->db->query($sql, array($field_id));
				
				// Insert new options
				$sql = 'INSERT INTO `roomattrs-options` (option_id, field_id, value) VALUES ';
				foreach($data['options'] as $option){
					$sql .= sprintf("(NULL, %d, '%s'),", $field_id, $option);
				}
				$sql = preg_replace('/,$/', '', $sql);
				$query = $this->db->query($sql);
				if($query == FALSE){
					$this->lasterr = 'Could not insert values of drop-down box.';
				}
				
			}
			
		}
		
		// We will not change a field type
		unset($data['type']);
		// Remove options (if any) from main data array
		unset($data['options']);
		
		// Update room info
		$this->db->where('field_id', $field_id);
		$edit = $this->db->update('roomattrs-fields', $data);
		
		return $edit;
		
	}
	
	
	
	
	/**
	 * Delete a room from the database
	 *
	 * @param	field_id		Field ID to delete
	 * @return	bool
	 */
	function delete_field($field_id){
		
		$sql = 'DELETE FROM `roomattrs-fields` WHERE field_id = ? LIMIT 1';
		$query = $this->db->query($sql, array($field_id));
		
		if($this->db->affected_rows() != 1){
			$this->lasterr = 'Could not delete field. Does it exist?';
			return FALSE;
		} else {
			return TRUE;
		}
		
	}
	
	
	
	
	/**
	 * Get the attribute values for a given room
	 *
	 * @param	int		room_id		Room ID
	 * @return	array
	 */
	function get_attr_values($room_id){
		
		if(empty($room_id) OR !is_numeric($room_id)){
			$this->lasterr = 'Invalid Room ID';
			return FALSE;
		}
		
		$sql = 'SELECT field_id, value FROM `roomattrs-values` WHERE room_id = ?';
		$query = $this->db->query($sql, array($room_id));
		
		if($query->num_rows() > 0){
			
			$values = array();
			$row = $query->result();
			foreach($row as $item){
				$values[$item->field_id] = $item->value;
			}
			return $values;
			
		} else {
			
			$this->lasterr = 'No values exist for the supplied room';
			return array();
			
		}
		
	}
	
	
	
	
	/**
	 * Save room attribute values
	 *
	 * @param	int		room_id		Room ID that the values belong to
	 * @param	array	fields		Array of field_id => value
	 * @return	bool
	 */
	function save_attr_values($room_id, $fields){
		
		#die($room_id . print_r($values));
		
		if(empty($room_id) OR !is_numeric($room_id)){
			$this->lasterr = 'No Room ID supplied.';
			return FALSE;
		}
		
		if(empty($fields)){
			$this->lasterr = 'No values to update attributes with.';
			return FALSE;
		}
		
		$sql = 'REPLACE INTO `roomattrs-values` (room_id, field_id, value) VALUES';
		foreach($fields as $field_id => $value){
			$sql .= sprintf("(%d, %d, '%s'),", $room_id, $field_id, $value);
		}
		$sql = preg_replace('/,$/', '', $sql);
		
		$query = $this->db->query($sql);
		if($this->db->affected_rows() > 0){
			return TRUE;
		} else {
			$this->lasterr = 'Failed to update room attribute values.';
			return FALSE;
		}
		
		#die($sql);
		
	}
	
	
	
	
	/**
	 * Get an array of options for the given field
	 *
	 * @param	field_id	Field ID of the field to get the options for
	 * @return	array		Array (empty, or otherwise containing the options)
	 */
	private function _get_attr_options($field_id){
		
		$sql = 'SELECT option_id, value FROM `roomattrs-options` WHERE field_id = ? ORDER BY field_id ASC';
		$query = $this->db->query($sql, array($field_id));
		
		if($query->num_rows() > 0){
			
			$options = array();
			$result = $query->result();
			foreach($result as $row){
				#array_push($options, $row->value);
				$options[$row->option_id] = $row->value;
			}
			
			return $options;
			
		} else {
			
			$this->lasterr = 'No options exist for the supplied field.';
			return array();
			
		}
		
	}
	
	
	
	
}

/* End of file: app/models/rooms_model.php */