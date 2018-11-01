<?php

require_once(APPPATH . 'third_party/bitmask.php');

class Periods_model extends CI_Model
{


	public $days = array();
	public $days_bitmask;


	public function __construct()
	{
		parent::__construct();

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



	function Get($period_id = NULL)
	{
		if ($period_id == NULL) {
			return $this->crud_model->Get('periods', NULL, NULL, NULL, 'days asc, time_start asc');
		} else {
			return $this->crud_model->Get('periods', 'period_id', $period_id);
		}
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
