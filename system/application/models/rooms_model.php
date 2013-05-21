<?php
class Rooms_model extends Model{





	function Rooms_model(){
		parent::Model();
		$options['CHECKBOX']	= 'Checkbox';
		$options['SELECT']		= 'Drop-down list';
		$options['TEXT']			= 'Text field';
		$this->options = $options;
		#$this->CI =& get_instance();
  }
  
  
  
  
  function Test(){
  	$query = $this->db->get('rooms');
  	return var_export($query->row(), true);
  }
  
  
  
	/**
	 * Retrieve all rooms
	 * 
	 * @return				array				All items in table
	 */	 	 	 	
	function Get($room_id = NULL, $school_id = NULL){
		if($school_id == NULL){ $school_id = $this->session->userdata('school_id'); }
													/* .'schools.school_id,'
											.'schools.code AS schoolcode'
											#.'x' */
		
		$this->db->select(
											 'rooms.*,'
											.'users.user_id,'
											.'users.username,'
											.'users.displayname'
											);
		$this->db->from('rooms');
		#$this->db->join('schools', 'schools.school_id = rooms.school_id');
		$this->db->join('users', 'users.user_id = rooms.user_id', 'left');
		$this->db->where('rooms.school_id', $school_id);
		
		if( $room_id != NULL ){
			// Getting one specific room
			$this->db->where('room_id', $room_id);
			$this->db->limit('1');
			$query = $this->db->get();
			if( $query->num_rows() == 1 ){
				// One row, match!
				return $query->row();
			} else {
				// None
				return false;
			}
		} else {
			// Getting all
			$this->db->order_by('name asc');
			$query = $this->db->get();
			if( $query->num_rows() > 0 ){
				// Got some rooms, return result
				return $query->result();
			} else {
				// No rooms!
				return false;
			}
		}

	}
	
	
	
	/*
		Gets all information on the room - joins all the fields as well
	*/
	function GetInfo($room_id, $school_id = NULL ){
		if($school_id == NULL){ $school_id = $this->session->userdata('school_id'); }
		
		$this->db->select(
											 'rooms.*,'
											/*.'roomfields.*,'
											.'roomoptions.*,'
											.'roomvalues.*,'*/
											.'users.user_id,'
											.'users.username,'
											.'users.displayname'
											);
		$this->db->from('rooms');	//,roomfields,roomoptions,roomvalues');
		$this->db->join('users', 'users.user_id = rooms.user_id', 'left');
		$this->db->where('rooms.school_id', $school_id);
		$this->db->where('rooms.room_id', $room_id);
	
		$query = $this->db->get();
		
		$data['room'] = $query->row();
		
		$this->db->select('roomfields.*, roomoptions.*, roomvalues.*');
		$this->db->from('roomvalues');
		
		$this->db->join('roomoptions', 'roomoptions.field_id = roomvalues.field_id', 'left');
		$this->db->join('roomfields', 'roomfields.field_id = roomvalues.field_id');
		#$this->db->join('roomvalues', 'roomvalues.value = roomoptions.option_id');
		#$this->db->join('roomoptions','roomoptions.option_id=roomvalues.value');
		#$this->db->where('roomoptions.option_id=roomvalues.value');
		$this->db->where('roomvalues.room_id', $room_id);
		$this->db->where('roomfields.school_id', $school_id);
		
		$query = $this->db->get();
		
		$data['fields'] = $query->result();
		#$data['fields'] = $this->db->last_query();

		
				#$this->db->join('roomfields', 'roomfields.school_id = rooms.school_id');
		#$this->db->join('roomvalues', 'roomvalues.room_id = rooms.room_id');
		#$this->db->join('roomoptions', 'roomoptions.field_id = roomvalues.field_id');
		
		return $data;
	} 
	
	
	
	
	/**
	 * Gets room ID and name of one room owned by the given user id
	 * 
	 * @param	int	$school_id	School ID
	 * @param	int	$user_id	ID of user to lookup
	 * @return	mixed	object if result, false on no results	 	 	 	 
	 */	 	
	function GetByUser($user_id, $school_id = NULL ){
		if($school_id == NULL){ $school_id = $this->session->userdata('school_id'); }
		$query_str = "SELECT room_id,name FROM rooms "
								."WHERE school_id=$school_id AND user_id=$user_id "
								."ORDER BY name LIMIT 1";
		$query = $this->db->query($query_str);
		if($query->num_rows() == 1){
			return $query->row();
		} else {
			return false;
		}
	}
	
	
	
	
	function add($data){
		// Run query to insert blank row
		$this->db->insert('rooms', array('room_id' => NULL) );
		// Get id of inserted record
		$room_id = $this->db->insert_id();
		// Now call the edit function to update the actual data for this new row now we have the ID
		$this->edit($room_id, $data);
		return $room_id;
	}
	
	
	
	
	
	function edit($room_id, $data){
		$this->db->where('room_id', $room_id);
		$this->db->set('school_id', $this->school_id);
		$result = $this->db->update('rooms', $data);
		// Return bool on success
		if($result){
			// Clear the cache file for this room info page
			$this->clear_room_cache($this->school_id, $room_id);
			return true;
		} else {
			return false;
		}
	}
	
	
	
	
	
	function clear_room_cache($room_id, $school_id = NULL){
		if($school_id == NULL){ $school_id = $this->session->userdata('school_id'); }
		$path = 0;
		$cache_path = ($path == '') ? BASEPATH.'cache/' : $path;
		$uri = $this->config->item('base_url')
					.$this->config->item('index_page')
					."/rooms/info/$school_id/$room_id";
		$cache_path .= md5($uri);
		if(file_exists($cache_path)){
			return @unlink($cache_path);
		} else {
			return false;
		}
	}





	/**
	 * Deletes a room with the given ID
	 *
	 * @param   int   $id   ID of room to delete
	 *
	 */
	function delete($id){
		$this->delete_photo($id);
    $this->db->where('room_id', $id);
    $this->db->delete('rooms');
	}
	
	
	
	
	
	/**
	 * Deletes a photo
	 */	 	
	function delete_photo($room_id){
		$row = $this->Get($room_id, NULL);
		$photo = $row->photo;
		#echo $room_id;
		@unlink('webroot/images/roomphotos/160/'.$photo);
		@unlink('webroot/images/roomphotos/320/'.$photo);
		@unlink('webroot/images/roomphotos/640/'.$photo);
		$this->db->where('room_id', $room_id);
		$this->db->update('rooms', array('photo' => '')); 
	}
	
	
	
	
	
	/**
	 * Get room fields
	 */	 	
	function GetFields($field_id = NULL, $school_id = NULL){
		if($school_id == NULL){ $school_id = $this->session->userdata('school_id'); }
		$this->db->select(
											 '*'
											#.'roomoptions.*,'
											#.'roomoptions.option_id,'
											#.'roomoptions.value,'
											#.'x.,'
											#.'school.school_id,'
											#.'school.code AS schoolcode'
											);
		$this->db->from('roomfields');
		#$this->db->join('roomoptions', 'roomfields.field_id = roomoptions.field_id', 'left outer');
		#$this->db->join('schools', 'schools.school_id = roomfields.school_id');
		$this->db->where('roomfields.school_id', $school_id);
		#$this->db->where('schools.code', $schoolcode);
		#$this->db->where('roomfields.type', 'SELECT');

		
		if( $field_id != NULL ){
			// Getting one specific field
			$this->db->where('roomfields.field_id', $field_id);
			$this->db->limit('1');
			$query = $this->db->get();
			if( $query->num_rows() == 1 ){
				// One row, match!
				$row = $query->row();
				$row->options = $this->GetOptions($field_id);
				#print_r($row);
				return $row;
			} else {
				// None
				return false;
			}
		} else {
			// Getting all
			$this->db->order_by('roomfields.type asc, roomfields.name asc');
			$query = $this->db->get();
			if( $query->num_rows() > 0 ){
				
				// Got some rooms, return result
				$result = $query->result();	
				
				foreach( $result as $item ){
					if($item->type == 'SELECT'){
						$item->options = $this->GetOptions($item->field_id);
						#print_r($item);
					}
				}
				
				return $result;
			} else {
				// No rooms!
				return false;
			}
		}
	}





	function field_add($data){
		// Run query to insert blank row
		$this->db->insert('roomfields', array('field_id' => NULL) );
		// Get id of inserted record
		$field_id = $this->db->insert_id();
		// Now call the edit function to update the actual data for this new row now we have the ID
		$this->field_edit($field_id, $data);
		return $field_id;
	}
	
	
	
	
	
	function field_edit($field_id, $data){
		// We don't add the options column to the roomfields table, so get it then remove it from the array that gets added
		$options = $data['options'];
		unset($data['options']);
		
		$this->db->where('field_id', $field_id);
		$this->db->set('school_id', $this->session->userdata('school_id'));
		$result = $this->db->update('roomfields', $data);

		// Delete row options of the field
		//		We don't yet know if the type is a SELECT, but we delete
		//		them first anyway incase they changed it 
		//		from a SELECT to something else. The new options get inserted next.
		$this->delete_field_options($field_id);
		
		if( $data['type'] == 'SELECT' ){
			
			// Explode at newline into array
			$arr_options = explode("\n", $options);
			
			// Loop through options and insert a new row for each one
			foreach($arr_options as $key => $value){
				$arr_option['field_id'] = $field_id;
				$arr_option['value'] = addslashes($value);
				$this->db->insert('roomoptions', $arr_option);
			}
			
		}
		// Return bool on success
		if( $result ){
			return true;
		} else {
			return false;
		}
	}
	
	
	
	
	
	
	
	
	/**
	 * Deletes a field with the given ID
	 *
	 * @param   int   $id   ID of field to delete
	 *
	 */
	function field_delete($id){
    $this->db->where('field_id', $id);
    $this->db->delete('roomfields');
    $this->db->where('field_id', $id);
    $this->db->delete('roomvalues');
	}
	
	
	
	
	
	/**
	 * Get options for a field
	 */	 	
	function GetOptions($field_id){
		$this->db->select('*');
		$this->db->from('roomoptions');
		$this->db->orderby('value asc');
		$this->db->where('field_id', $field_id);
		$query = $this->db->get();
		if( $query->num_rows() > 0 ){
			$result = $query->result();
			return $result;
		} else {
			return false;
		}
	}
	
	
	
	
	
	/**
	 * Delete all options for a given field
	 */	 	
	function delete_field_options($field_id){
		$this->db->where('field_id', $field_id);
		$this->db->delete('roomoptions');
	}
	
	
	
	
	
	function save_field_values($room_id, $data){
		$this->db->where('room_id', $room_id);
		$this->db->delete('roomvalues');
		foreach($data as $field_id => $value){
			$values['room_id'] = $room_id;
			$values['field_id'] = $field_id;
			$values['value'] = $value;
			$this->db->insert('roomvalues', $values);
		}
	}
	
	
	
	
	
	function GetFieldValues($room_id){
		$this->db->select('field_id, value');
		$this->db->from('roomvalues');
		$this->db->orderby('value_id asc');
		$this->db->where('room_id', $room_id);
		$query = $this->db->get();
		if( $query->num_rows() > 0 ){
			$result = $query->result();
			foreach($result as $item){
				$values[$item->field_id] = $item->value;
			}
			return $values;
		} else {
			return false;
		}
	}
	
	
	
	

}
?>
