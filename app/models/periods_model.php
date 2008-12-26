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
	function get($period_id = NULL, $page = NULL){
		
		if ($period_id == NULL) {
		
			// Getting all periods
			$this->db->select('*', FALSE);
			$this->db->from('periods');
			
			$this->db->orderby('time_start ASC, time_end ASC');
			
			if (isset($page) && is_array($page)) {
				$this->db->limit($page[0], $page[1]);
			}
			
			$query = $this->db->get();
			if ($query->num_rows() > 0){
				return $query->result();
			} else {
				$this->lasterr = 'There are no periods.';
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
				return $period;
				
			} else {
				return FALSE;
			}
			
		}
		
	}
	
	
	
	
	function add($data){
		
		$add = $this->db->insert('periods', $data);
		
		$period_id = $this->db->insert_id();
		
		return $department_id;
	}
	
	
	
	
	function edit($department_id = NULL, $data){
		if($department_id == NULL){
			$this->lasterr = 'Cannot update a department without its ID.';
			return FALSE;
		}
		
		// If no LDAP groups, set empty array. Otherwise assign to new array for itself
		if(in_array(-1, $data['ldapgroups'])){
			$ldapgroups = array();
		} else {
			$ldapgroups = $data['ldapgroups'];
		}
		// Remove 'column' from data array
		unset($data['ldapgroups']);
		
		// Update department info
		$this->db->where('department_id', $department_id);
		$edit = $this->db->update('departments', $data);
		
		// Now remove LDAP group assignments (don't panic - will now re-insert if they are specified)
		$sql = 'DELETE FROM departments2ldapgroups WHERE department_id = ?';
		$query = $this->db->query($sql, array($group_id));
		
		// If LDAP groups were assigned then insert into DB
		if(count($ldapgroups) > 0){
			$sql = 'INSERT INTO departments2ldapgroups (department_id, ldapgroup_id) VALUES ';
			foreach($ldapgroups as $ldapgroup_id){
				$sql .= sprintf("(%d,%d),", $department_id, $ldapgroup_id);
			}
			// Remove last comma
			$sql = preg_replace('/,$/', '', $sql);
			$query = $this->db->query($sql);
			if($query == FALSE){
				$this->lasterr = 'Could not assign LDAP groups';
			}
		}
		
		return $edit;
	}
	
	
	
	
	function delete($department_id){
		
		$sql = 'DELETE FROM departments WHERE department_id = ? LIMIT 1';
		$query = $this->db->query($sql, array($department_id));
		
		if($query == FALSE){
			
			$this->lasterr = 'Could not delete department. Does it exist?';
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

/* End of file: app/models/periods_model.php */