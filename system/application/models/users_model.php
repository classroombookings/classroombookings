<?php
class Users_model extends Model{





	function Users_model(){
		parent::Model();
		$this->CI =& get_instance();
  }
  
  
  
  
  /**
   * Get assoc array of all users for schoolcode or single userid
   * 
   * @param		string		$schoolcode		School code of user to look up
   * @param		int				$user_id			ID of username for single user lookup
   * @param		int				$columns			Columns to select	 	 	    
   */
  function Get($user_id = NULL, $school_id = NULL, $columns = array('*'), $sort = 'authlevel asc, enabled asc, username asc' ){
  	if($school_id == NULL){ $school_id = $this->session->userdata('school_id'); }
 		$i=0;
 		// Put the array of columns into a string
 		foreach( $columns as $column ){
 			$columns[$i] = 'users.'.$column;
 			$i++;
 		}
 		$ci_users_fields = implode( ',', $columns );
 		//echo $ci_users_fields;
  	
		$this->CI->db->select($ci_users_fields);	//.',schools.school_id,schools.code AS schoolcode');
		$this->CI->db->from('users');
		$this->CI->db->where('users.school_id', $school_id);
		#$this->db->join('schools', 'schools.school_id = users.school_id');
		//$this->db->join('users', 'users.user_id = rooms.user_id');
		if( $user_id != NULL ){
			$this->CI->db->where('users.user_id', $user_id);
			$this->CI->db->limit('1');
			$query = $this->CI->db->get();
			if($query->num_rows() == 1){
				return $query->row();
			} else {
				return false;
			}
		} else {
			#$this->db->where('schools.code', $schoolcode);
			$this->CI->db->order_by($sort);
			$query = $this->CI->db->get();
			if($query->num_rows() > 0){
				return $query->result();
			} else {
				return false;
			}
		}
  }
  

  
  
  
}
 
  
?>
