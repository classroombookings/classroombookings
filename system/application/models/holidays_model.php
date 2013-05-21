<?php
class Holidays_model extends Model{





	function Holidays_model(){
		parent::Model();
		/*$this->CI =& get_instance();
		$this->CI->load->Model('crud_model', 'crud', True);*/
  }
  
  
  
  
  
	function Get($holiday_id = NULL, $school_id = NULL){
		if($school_id == NULL){ $school_id = $this->session->userdata('school_id'); }
		if($holiday_id == NULL){
			return $this->crud->Get('holidays', NULL, NULL, $school_id, 'date_start asc, date_end asc');
		} else {
			return $this->crud->Get('holidays', 'holiday_id', $holiday_id);
		}
		/* $this->db->select(
											 'holidays.*,'
											.'schools.school_id,'
											.'schools.code AS schoolcode'
											#.'x'
											);
		$this->db->from('holidays');
		$this->db->join('schools', 'schools.school_id = holidays.school_id');
		$this->db->where('schools.code', $schoolcode);
		
		if($holiday_id != NULL){
			// Getting one specific holiday
			$this->db->where('holiday_id', $holiday_id);
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
			$this->db->order_by('date_start asc, date_end asc');
			$query = $this->db->get();
			if( $query->num_rows() > 0 ){
				// Got some holidays, return result
				return $query->result();
			} else {
				// No holidays
				return false;
			}
		} */
	}
	
	
	
	
	
  function Add($data){
  	return $this->crud->Add('holidays', 'holiday_id', $data);
		/* // Run query to insert blank row
		$this->db->insert('holidays', array('holiday_id' => NULL) );
		// Get id of inserted record
		$holiday_id = $this->db->insert_id();
		// Now call the edit function to update the actual data for this new row now we have the ID
		$this->edit($holiday_id, $data);
		return $holiday_id; */
  }
  
  
  
  
  
  function Edit($holiday_id, $data){
  	return $this->crud->Edit('holidays', 'holiday_id', $holiday_id, $data, $this->school_id);
		/* $this->db->where('holiday_id', $holiday_id);
		$this->db->set('school_id', $this->session->userdata('school_id'));
		$result = $this->db->update('holidays', $data);
		// Return bool on success
		if( $result ){
			return true;
		} else {
			return false;
		} */
  }
  
  
  
  
  
	/**
	 * Deletes a week with the given ID
	 *
	 * @param   int   $id   ID of week to delete
	 *
	 */
	function delete($id){
    $this->db->where('holiday_id', $id);
    $this->db->where('school_id', $this->session->userdata('school_id'));
    $this->db->delete('holidays');
	}
  
  
  
  
  
}
?>
