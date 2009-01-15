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


class Terms_model extends Model{


	var $lasterr;
	
	
	function Terms_model(){
		parent::Model();
		
	}
	
	
	
	
	/**
	 * get one or more terms
	 *
	 * @param int term_id
	 * @param arr pagination limit,start
	 * @return mixed (object on success, false on failure)
	 */
	function get($term_id = NULL, $page = NULL, $year_id = NULL){
		
		if($year_id == NULL){
			$this->lasterr = 'There is no active academic year or no working academic year has been selected.';
			return FALSE;
		}
		
		if($term_id == NULL){
			
			// Getting all years
			$this->db->select('*', FALSE);
			$this->db->from('terms');
			if($year_id != NULL){
				$this->db->where('year_id', $year_id);
			}
			
			$this->db->orderby('date_start ASC');
			
			if(isset($page) && is_array($page)){
				$this->db->limit($page[0], $page[1]);
			}
			
			$query = $this->db->get();
			if ($query->num_rows() > 0){
				return $query->result();
			} else {
				$this->lasterr = 'There are no terms defined.';
				return 0;
			}
			
		} else {
			
			if(!is_numeric($term_id)){
				return FALSE;
			}
			
			// Getting one year
			$sql = 'SELECT * FROM terms WHERE term_id = ? LIMIT 1';
			$query = $this->db->query($sql, array($term_id));
			
			if($query->num_rows() == 1){
				// Got the year
				$term = $query->row();				
				return $term;
			} else {
				return FALSE;
			}
			
		}
		
	}
	
	
	
	
	function add($data){
		
		$add = $this->db->insert('terms', $data);
		$term_id = $this->db->insert_id();
		
		return $term_id;
		
	}
	
	
	
	
	function edit($term_id = NULL, $data){
		
		if($term_id == NULL){
			$this->lasterr = 'Cannot update a term without its ID.';
			return FALSE;
		}
		
		$this->db->where('term_id', $term_id);
		$edit = $this->db->update('terms', $data);
		
		return $edit;
		
	}
	
	
	
	
	function delete($term_id){
		
		$sql = 'DELETE FROM terms WHERE term_id = ? LIMIT 1';
		$query = $this->db->query($sql, array($term_id));
		
		if($query == FALSE){
			
			$this->lasterr = 'Could not delete term. Does it exist?';
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




/* End of file: app/models/terms_model.php */