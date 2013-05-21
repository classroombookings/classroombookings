<?php
class Periods_model extends Model{





	function Periods_model(){
		parent::Model();
		
		$this->CI =& get_instance();
		
		$this->days[1] = 'Monday';
		$this->days[2] = 'Tuesday';
		$this->days[3] = 'Wednesday';
		$this->days[4] = 'Thursday';
		$this->days[5] = 'Friday';
		$this->days[6] = 'Saturday';
		$this->days[7] = 'Sunday';
		
		$this->days_bitmask = new bitmask;
		$this->days_bitmask->assoc_keys = $this->days;

  }
  
  
  
  
  
	function Get($period_id = NULL, $school_id = NULL){
		if($school_id == NULL){ $school_id = $this->session->userdata('school_id'); }
		if($period_id == NULL){
			return $this->CI->crud->Get('periods', NULL, NULL, NULL, 'days asc, time_start asc');
		} else {
			return $this->CI->crud->Get('periods', 'period_id', $period_id);
		}
		/* $this->db->select(
											 'periods.*,'
											.'schools.school_id,'
											.'schools.code AS schoolcode'
											#.'x'
											);
		$this->db->from('periods');
		$this->db->join('schools', 'schools.school_id = periods.school_id');
		$this->db->where('schools.code', $schoolcode);
		$this->db->orderby('days', 'asc');
		
		if( $period_id != NULL ){
			// Getting one specific room
			$this->db->where('period_id', $period_id);
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
			$this->db->order_by('time_start asc');
			$query = $this->db->get();
			if( $query->num_rows() > 0 ){
				// Got some rooms, return result
				return $query->result();
			} else {
				// No periods
				return false;
			}
		}*/
	}
	
	
	
	
	
	function Add($data){
		return $this->CI->crud->Add('periods', 'period_id', $data);
		/* // Run query to insert blank row
		$this->db->insert('periods', array('period_id' => NULL) );
		// Get id of inserted record
		$period_id = $this->db->insert_id();
		// Now call the edit function to update the actual data for this new row now we have the ID
		$this->edit($period_id, $data);
		return $period_id; */
	}
	
	
	
	
	
	function Edit($period_id, $data){
		return $this->CI->crud->Edit('periods', 'period_id', $period_id, $data);
		/* $this->db->where('period_id', $period_id);
		$this->db->set('school_id', $this->session->userdata('school_id'));
		$result = $this->db->update('periods', $data);
		// Return bool on success
		if( $result ){
			return true;
		} else {
			return false;
		} */
	}
	
	
	
	
	
	/**
	 * Deletes a period with the given ID
	 *
	 * @param   int   $id   ID of period to delete
	 *
	 */
	function Delete($id){
		return $this->CI->crud->Delete('periods', 'period_id', $id);
    /* $this->db->where('period_id', $id);
    $this->db->delete('periods'); */
	}





}
?>
