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


class Weeks_model extends Model{


	var $lasterr;
	
	
	function Weeks_model(){
		parent::Model();
		
	}
	
	
	
	
	/**
	 * get one or more weeks
	 *
	 * @param int week_id
	 * @param arr pagination limit,start
	 * @return mixed (object on success, false on failure)
	 */
	function get($week_id = NULL, $page = NULL){
		
		if ($week_id == NULL) {
		
			// Getting all departments
			$this->db->select('*', FALSE);
			$this->db->from('weeks');
			
			$this->db->orderby('name ASC');
			
			if (isset($page) && is_array($page)) {
				$this->db->limit($page[0], $page[1]);
			}
			
			$query = $this->db->get();
			if ($query->num_rows() > 0){
				return $query->result();
			} else {
				$this->lasterr = 'There are no weeks defined.';
				return 0;
			}
			
		} else {
			
			if (!is_numeric($week_id)) {
				return FALSE;
			}
			
			// Getting one week
			$sql = 'SELECT * FROM weeks WHERE week_id = ? LIMIT 1';
			$query = $this->db->query($sql, array($week_id));
			
			if($query->num_rows() == 1){
				// Got the week
				$week = $query->row();				
				return $week;
			} else {
				return FALSE;
			}
			
		}
		
	}
	
	
	
	
	function add($data){
		$data['created'] = date("Y-m-d");
		$add = $this->db->insert('weeks', $data);
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
	
	
	
	/**
	 * Show calendar so user can select dates for the week
	 *
	 * @param	int		week_id		ID of the editing week so we can highlight it
	 * @param	int		year_id		ID of the working acadmic year
	 */
	function calendar($week_id = NULL, $year_id){
		
		$CI =& get_instance();
		
		$prefs['start_day'] = 'monday';
		$prefs['month_type'] = 'long';
		$prefs['day_type'] = 'long';
		$this->load->library('calendar', $prefs);
		
		$CI->load->model('years_model');
		$year = $CI->years_model->get($year_id);

		$start['ts'] = strtotime($year->date_start);
		$start['m'] = date('m', $start['ts']);
		$start['y'] = date('Y', $start['ts']);
		
		$end['ts'] = strtotime($year->date_end);
		$end['m'] = date('m', $end['ts']);
		$end['y'] = date('Y', $end['ts']);
		
		#echo "Year {$year_id} starts in {$start['m']} of {$start['y']} and ends in {$end['m']} of {$end['y']}.";
		
		// Months for calendar
		$months = $this->get_months($year->date_start, $year->date_end);
		$html = "";
		
		foreach($months as $month){
			$html .= $this->calendar->generate($month[0], $month[1]);
		}
		
		return($html);
		
	}
	
	
	
	
	function get_months($date_start, $date_end){
		
		$start = strtotime($date_start);
		$end = strtotime($date_end);
		
		$my = date('mY', $end);
		
		$months = array();
		array_push($months, array(date('Y', $start), date('m', $start)));
		
		$f = ''; 
		
		while($start < $end){
			
			$start = strtotime( date( 'Y-m-d', $start ).' next month'); 
			
			if(date('F', $start) != $f){
				$f = date('F', $start); 
				if(date('mY', $start) != $my && ($start < $end)){
					#$months[] = date('F', $start);
					array_push($months, array(date('Y', $start), date('m', $start)));
				}
			}
			
		}
		
		// End one
		array_push($months, array(date('Y', $end), date('m', $end)));
		
		return $months;
	}
	
	
	
	
	function css(){
		return "OK";
	}
	
	
	
	
}

/* End of file: app/models/weeks_model.php */