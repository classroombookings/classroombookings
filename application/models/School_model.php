<?php
class School_model extends Model{





	function School_model(){
		parent::Model();
		$this->load->library('gradient');
  }
  
  
  
  
  
  /**
	 * Get all fields on school by code
	 *
	 * @param	string	$schoolcode		School code
	 * @return array	DB fields with school information
	 *
	 */
	function GetInfoByCode($schoolcode){
		$query_str = "SELECT * FROM school WHERE code='$schoolcode' LIMIT 1";
		$query = $this->db->query($query_str);
		if($query->num_rows() == 1){
			return $query->row();
		} else {
			return false;
		}
	}
	
	
	
	
	Function GetInfo(){
		$query_str = "SELECT * FROM school LIMIT 1";
		$query = $this->db->query($query_str);
		if($query->num_rows() ==1){
			return $query->row();
		} else {
			return false;
		}
	}
  
  
  
  
  
  /**
   * ADD SCHOOL
   */
	function add($data){
		// Run query to insert blank row
		$this->db->insert('school', array('school_id' => 0) );
		// Get id of inserted record
		$school_id = $this->db->insert_id();
		// Now call the edit function to update the actual data for this new row now we have the ID
		return $this->edit( 'school_id', $school_id, $data );
	}
	
	
	
	
	
	/**
	 * EDIT SCHOOL
	 */
	function edit($column, $value, $data){
		$this->db->where($column, $value);
		$result = $this->db->update('school', $data);
		// Return bool on success
		if( $result ){
			return $value;
		} else {
			return false;
		}
	}
  
  
  
  
  
  /**
   * Check to see if the schoolcode exists (must be unique!)
   * 
   * @param		string		$schoolcode		Schoolcode to look up
   * @return	int				0 on not exist; 1 on exists; 3 if app-restricted code	 	    
   */
  /*function schoolcode_exists($schoolcode){
  	// Lowercase it
  	$schoolcode = strtolower($schoolcode);
		// Run query  	
  	$query_str = "SELECT code FROM schools WHERE code='$schoolcode' LIMIT 1";
  	$query = $this->db->query($query_str);
  	$rows = $query->num_rows();
  	switch($rows){
  		case '1': return 1; break;
  		case '0': return 0; break;
  	}
		#if( $this->schoolcode_restricted($schoolcode) ){ $ret = 3; }
	}*/
  
  
  
  
  
  function schoolcode_restricted($schoolcode){
  	if( in_array( $schoolcode, $this->restricted_codes ) ){
  		return true;
  	} else {
  		return false;
  	}
  }
  
  
  
  
  
  function GetSchoolName($schoolcode){
  	$query_str = "SELECT name FROM schools WHERE code='$schoolcode' LIMIT 1";
  	$query = $this->db->query($query_str);
  	if($query->num_rows() == 1){
  		$row = $query->row();
  		return $row->name;
  	} else {
  		return false;
  	}
  }





	function delete_logo($school_id = NULL){
		if($school_id == NULL){ $school_id = $this->session->userdata('school_id'); }
		$row = $this->GetInfo();	//ByCode($schoolcode);
		$logo = $row->logo;
		@unlink('webroot/images/schoollogo/300/'.$logo);
		@unlink('webroot/images/schoollogo/200/'.$logo);
		@unlink('webroot/images/schoollogo/100/'.$logo);
		$this->db->where('school_id', $school_id);
		$this->db->update('school', array('logo' => ''));
	}





}
?>
