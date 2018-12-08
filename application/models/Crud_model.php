<?php
class Crud_model extends CI_Model{





	public function __construct(){
		parent::__construct();
	}





  /**
   * Get one or more departments
   *
   * @param		str		$table				Table to get info from
   * @param		int		$pk						Name of the Primary Key ID field
   * @param		int		$pk_id				Value of the primary key field (if getting one row, NULL if all
   * @param		int		$school_id		ID of the school. If NULL, it is obtained from session
   * @param		str		$orderby			SQL 'order by' string
  */
  function Get($table, $pk = NULL, $pk_id = NULL, $school_id = NULL, $orderby = 'name asc', $per_page = NULL, $start_at = NULL){
  	$this->db->select('*');
  	$this->db->from($table);
  	// $this->db->where('school_id', $school_id);

  	if($pk_id != NULL){
			// Getting only ONE row
  		$this->db->where($pk, $pk_id);
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
			// Get all
  		if( $per_page != NULL){
  			if( $start_at != NULL){
  				$this->db->limit($per_page, $start_at);
  			} else {
  				$this->db->limit($per_page);
  			}
  		}
  		$this->db->order_by($orderby);
  		$query = $this->db->get();
  		if( $query->num_rows() > 0 ){
				// Got some rows, return as assoc array
  			return $query->result();
  		} else {
				// No rows
  			return false;
  		}
  	}
  }





  function Add($table, $pk, $data){
		// Run query to insert blank row
  	$this->db->insert($table, array($pk => NULL) );
		// Get id of inserted record
  	$pk_id = $this->db->insert_id();
		// Now call the edit function to update the actual data for this new row now we have the ID
  	return $this->Edit($table, $pk, $pk_id, $data);
  }





  function Edit($table, $pk, $pk_id, $data, $school_id = NULL){
  	$this->db->where($pk, $pk_id);
  	$result = $this->db->update($table, $data);
		// Return bool on success
  	if( $result ){
  		return $pk_id;
  	} else {
  		return false;
  	}
  }





  function Delete($table, $pk, $pk_id, $school_id = NULL){
  	$this->db->where($pk, $pk_id);
  	$this->db->delete($table);
  }




  function Count($table, $school_id = NULL){
  	return $this->db->count_all($table);
  }





}
