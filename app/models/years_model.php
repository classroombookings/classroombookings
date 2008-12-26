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


class Years_model extends Model{


	var $lasterr;
	
	
	function Years_model(){
		parent::Model();
		
	}
	
	
	
	
	/**
	 * get one or more years
	 *
	 * @param int week_id
	 * @param arr pagination limit,start
	 * @return mixed (object on success, false on failure)
	 */
	function get($year_id = NULL, $page = NULL){
		
		if ($year_id == NULL) {
		
			// Getting all years
			$this->db->select('*', FALSE);
			$this->db->from('years');
			
			$this->db->orderby('date_start ASC');
			
			if(isset($page) && is_array($page)){
				$this->db->limit($page[0], $page[1]);
			}
			
			$query = $this->db->get();
			if ($query->num_rows() > 0){
				return $query->result();
			} else {
				$this->lasterr = 'There are no academic years defined.';
				return 0;
			}
			
		} else {
			
			if (!is_numeric($week_id)) {
				return FALSE;
			}
			
			// Getting one year
			$sql = 'SELECT * FROM years WHERE year_id = ? LIMIT 1';
			$query = $this->db->query($sql, array($year_id));
			
			if($query->num_rows() == 1){
				// Got the year
				$year = $query->row();				
				return $year;
			} else {
				return FALSE;
			}
			
		}
		
	}
	
	
	
	
	function add($data){
		$add = $this->db->insert('years', $data);
		$week_id = $this->db->insert_id();
		return $department_id;
	}
	
	
	
	
	function edit($week_id = NULL, $data){
		if($week_id == NULL){
			$this->lasterr = 'Cannot update a week without its ID.';
			return FALSE;
		} 
		
		// Update week info
		$this->db->where('week_id', $week_id);
		$edit = $this->db->update('weeks', $data);
		
		return $edit;
	}
	
	
	
	
	function delete($week_id){
		
		$sql = 'DELETE FROM weeks WHERE week_id = ? LIMIT 1';
		$query = $this->db->query($sql, array($week_id));
		
		if($query == FALSE){
			
			$this->lasterr = 'Could not delete week. Does it exist?';
			return FALSE;
			
		} else {
			
			/* $sql = 'DELETE FROM bookings WHERE user_id = ?';
			$query = $this->db->query($sql, array($user_id));
			if($query == FALSE){ $failed[] = 'bookings'; }*/
			
			/*$sql = 'UPDATE rooms SET user_id = NULL WHERE user_id = ?';
			$query = $this->db->query($sql, array($user_id));
			if($query == FALSE){ $failed[] = 'rooms'; }
			
			if(isset($failed)){
				$this->lasterr = 'The user was deleted successfully, but an error occured while removing their bookings and/or updating any rooms they owned.';
			}*/
			
			return TRUE;
			
		}
		
	}
	
	
	
	
}


/* End of file: app/models/years_model.php */