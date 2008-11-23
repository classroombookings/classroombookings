<?php
class Security extends Model{


	var $lasterr;
	
	
	function Security(){
		parent::Model();
	}
	
	
	
	
	/**
	 * get one or more users (optionally by group)
	 *
	 * @param int user_id
	 * @param int group_id
	 * @param arr pagination limit,start
	 * @return mixed (object on success, false on failure)
	 *
	 * Example - get one user
	 *   get_user(42);
	 *
	 * Example - get all users
	 *   get_user();
	 *
	 * Example - get all users in a group
	 *  get_user(NULL, 4);
	 */
	function get_user($user_id = NULL, $group_id = NULL, $page = NULL){
		
		if ($user_id == NULL) {
		
			// Getting all users
			$this->db->select('users.*, groups.name AS groupname', FALSE);
			$this->db->from('users');
			$this->db->join('groups', 'users.group_id = groups.group_id', 'left');
			
			// Filter to group if necessary
			if ($group_id != NULL && is_numeric($group_id)) {
				$this->db->where('users.group_id', $group_id);
			}
			
			$this->db->orderby('groups.name ASC, users.username ASC');
			
			if (isset($page) && is_array($page)) {
				$this->db->limit($page[0], $page[1]);
			}
			
			$query = $this->db->get();
			if ($query->num_rows() > 0){
				return $query->result();
			} else {
				$this->lasterr = 'This group is empty!';
				return 0;
			}
			
		} else {
			
			if (!is_numeric($user_id)) {
				return FALSE;
			}
			
			// Getting one user
			$sql = 'SELECT * FROM users WHERE user_id = ? LIMIT 1';
			$query = $this->db->query($sql, array($user_id));
			
			if($query->num_rows() == 1){
				return $query->result();
			} else {
				return FALSE;
			}
			
		}
	}
	
	
	
	
	function get_group_name($group_id){
		if($group_id == NULL || !is_numeric($group_id)){
			$this->lasterr = 'No group_id given or invalid data type.';
			return FALSE;
		}
		
		$sql = 'SELECT name FROM groups WHERE group_id = ? LIMIT 1';
		$query = $this->db->query($sql, array($group_id));
		
		if($query->num_rows() == 1){
			$row = $query->row();
			return $row->name;
		} else {
			$this->lasterr = sprintf('The group supplied (ID: %d) does not exist.', $group_id);
			return FALSE;
		}
	}
	
	
	
	
	/*function get_user($user_id = NULL){
	
		// Base query information
		$select =  'users.user_id, '
							.'users.school_id, '
							.'users.username, '
							.'users.displayname, '
							.'users.email, '
							.'users.authlevel, '
							.'users.lastlogin, '
							.'users.enabled';
		
			
		// Check for getting one record or all
		if($user_id == NULL){
			// All users
			$select .= ', schools.name AS schoolname ';
			$this->db->select($select);
			$this->db->from('users');
			$this->db->join('schools', 'users.school_id=schools.school_id', 'left');
			if($school_id != NULL){
				$this->db->where('schools.school_id', $school_id);
			}
			$this->db->orderby('authlevel asc, username asc');

			// Run query
			$query = $this->db->get();
			if($query->num_rows() > 0){
				// Got rows!
				$return = $query->result();
			} else {
				// No rows :(
				$return = false;
			}
		} else {
			// One user
			$this->db->select($select);
			$this->db->from('users');
			$this->db->where('users.user_id', $user_id);
			$this->db->limit(1);
			// Run query
			$query = $this->db->get();
			if($query->num_rows() == 1){
				// Got one row exactly!
				$return = $query->row();
			} else {
				// No rows :(
				$return = false;
			}
		}
		return $return;
	}*/
	
	
	
}
?>