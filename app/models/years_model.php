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
			
			if (!is_numeric($year_id)) {
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
		
		$active = FALSE;
		// Use the proper function to make it active (to de-activate other years)
		if($data['active'] == 1){
			// Set flag to check to activate it later as we don't have the ID yet
			$active = TRUE;
		}
		$data['active'] = NULL;
		
		$add = $this->db->insert('years', $data);
		$year_id = $this->db->insert_id();
		
		if($active == TRUE){
			// Now we have the ID of the new year, and we need to make it active
			$this->activate($year_id);
		}
		
		return $year_id;
	}
	
	
	
	
	function edit($year_id = NULL, $data){
		if($year_id == NULL){
			$this->lasterr = 'Cannot update a year without its ID.';
			return FALSE;
		}
		
		$active = FALSE;
		// Use the proper function to make it active (to de-activate other years)
		if($data['active'] == 1){
			$active = TRUE;
		}
		$data['active'] = NULL;
		
		// Update year info
		$this->db->where('year_id', $year_id);
		$edit = $this->db->update('years', $data);
		
		if($active == TRUE){
			echo $this->activate($year_id);
		}
		
		return $edit;
	}
	
	
	
	
	function delete($year_id){
		
		$sql = 'DELETE FROM years WHERE year_id = ? LIMIT 1';
		$query = $this->db->query($sql, array($year_id));
		
		if($query == FALSE){
			
			$this->lasterr = 'Could not delete year. Does it exist?';
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
	
	
	
	
	/* Activate
	 *
	 * Make a given year the active one
	 */
	function activate($year_id){
		
		// Check the year is valid first
		$sql = 'SELECT year_id FROM years WHERE year_id = ?';
		$query = $this->db->query($sql, array($year_id));
		if($query->num_rows() != 1){
			$this->lasterr = 'No year by that ID';
			return FALSE;
		}
		
		// Clear all other years making them inactive
		$sql = 'UPDATE years SET active = NULL';
		$query = $this->db->query($sql);
		
		// Now set the given year as the active one
		$sql = 'UPDATE years SET active = 1 WHERE year_id = ? LIMIT 1';
		$query = $this->db->query($sql, array($year_id));
		
		return $query;
	}
	
	
	
	
}


/* End of file: app/models/years_model.php */