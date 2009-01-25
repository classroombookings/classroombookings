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


class Periods_model extends Model{


	var $lasterr;
	var $days;
	
	
	function Periods_model(){
		parent::Model();
		
		$this->days[0] = 'Sunday';
		$this->days[1] = 'Monday';
		$this->days[2] = 'Tuesday';
		$this->days[3] = 'Wednesday';
		$this->days[4] = 'Thursday';
		$this->days[5] = 'Friday';
		$this->days[6] = 'Saturday';
	}
	
	
	
	
	/**
	 * get one or more periods
	 *
	 * @param int period_id
	 * @param arr pagination limit,start
	 * @return mixed (object on success, false on failure)
	 */
	function get($period_id = NULL, $page = NULL, $year_id = NULL){
	
		if($year_id == NULL){
			$this->lasterr = 'There is no active academic year or no working academic year has been selected.';
			return FALSE;
		}
		
		if($period_id == NULL){
			
			// Getting all periods
			$this->db->select('*', FALSE);
			$this->db->from('periods');
			if($year_id != NULL){
				$this->db->where('year_id', $year_id);
			}
			
			$this->db->orderby('time_start ASC, time_end ASC');
			
			if (isset($page) && is_array($page)) {
				$this->db->limit($page[0], $page[1]);
			}
			
			$query = $this->db->get();
			if ($query->num_rows() > 0){
				$result = $query->result();
				foreach($result as $r){
					$r->days = unserialize($r->days);
				}
				return $query->result();
			} else {
				$this->lasterr = 'There are no periods defined.';
				return 0;
			}
			
		} else {
			
			if (!is_numeric($period_id)) {
				return FALSE;
			}
			
			// Getting one period
			$sql = 'SELECT * FROM periods WHERE period_id = ? LIMIT 1';
			$query = $this->db->query($sql, array($period_id));
			
			if($query->num_rows() == 1){
				// Got the period
				$period = $query->row();
				$period->days = unserialize($period->days);
				return $period;
			} else {
				return FALSE;
			}
			
		}
		
	}
	
	
	
	
	function add($data){
		
		if(!array_key_exists('year_id', $data)){
			$this->lasterr = 'No Academic year ID specified.';
			return FALSE;
		}
		
		$data['days'] = serialize($data['days']);
		$add = $this->db->insert('periods', $data);
		$period_id = $this->db->insert_id();
		return $period_id;
	}
	
	
	
	
	function edit($period_id = NULL, $data){
		if($period_id == NULL){
			$this->lasterr = 'Cannot update a department without its ID.';
			return FALSE;
		}
		
		if(!array_key_exists('year_id', $data)){
			$this->lasterr = 'No Academic year ID was specified.';
			return FALSE;
		}
		
		$data['days'] = serialize($data['days']);
		
		// Update department info
		$this->db->where('period_id', $period_id);
		$edit = $this->db->update('periods', $data);
		
		return $edit;
	}
	
	
	
	
	function delete($period_id){
		
		$sql = 'DELETE FROM periods WHERE period_id = ? LIMIT 1';
		$query = $this->db->query($sql, array($period_id));
		
		if($query == FALSE){
			
			$this->lasterr = 'Could not delete period. Does it exist?';
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
	
	
	
	
	function copy($year_from, $year_to){
	
		$sql = 'SELECT period_id FROM periods WHERE year_id = ?';
		$query = $this->db->query($sql, array($year_from));
		if($query->num_rows() == 0){
			$this->lasterr = 'No periods found in the given academic year.';
			return FALSE;
		}
		
		$sql = 'INSERT INTO periods
				(year_id, time_start, time_end, name, days, bookable)
				SELECT ?, time_start, time_end, name, days, bookable
				FROM periods 
				WHERE year_id = ?';
		
		$query = $this->db->query($sql, array($year_to, $year_from));
		
		return $query;
		
	}
	
	
	
	
}




/* End of file: app/models/periods_model.php */