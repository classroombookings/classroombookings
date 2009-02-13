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
	function get($week_id = NULL, $page = NULL, $year_id = NULL){
		
		if ($week_id == NULL) {
		
			// Getting all departments
			$this->db->select('*', FALSE);
			$this->db->from('weeks');
			if($year_id != NULL){
				$this->db->where('year_id', $year_id);
			}
			
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
		
		$dates = $data['dates'];
		unset($data['dates']);
		
		$data['created'] = date("Y-m-d");
		
		$add = $this->db->insert('weeks', $data);
		$week_id = $this->db->insert_id();
		
		$update = $this->update_dates($week_id, $data['year_id'], $dates);
		
		if($update == FALSE){
			$this->lasterr = 'Could not update week dates.';
			return FALSE;
		}
		
		return $week_id;
	}
	
	
	
	
	function edit($week_id = NULL, $data){
		
		if($week_id == NULL){
			$this->lasterr = 'Cannot update a week without its ID.';
			return FALSE;
		}
		
		$dates = $data['dates'];
		unset($data['dates']);
		
		// Update week info
		$this->db->where('week_id', $week_id);
		$edit = $this->db->update('weeks', $data);
		
		$update = $this->update_dates($week_id, $data['year_id'], $dates);
		
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
	 * Update the dates for a given week
	 *
	 * @param	int		week_id		ID of the week whose dates we want to update
	 * @param	int		year_id		ID of the academic year it will belong to
	 * @param	array	dates		1-dimensional array of dates
	 * @return	bool
	 */
	function update_dates($week_id, $year_id, $dates){
		
		// Put dates into string
		$str = implode(',', $dates);
		// Remove last comma
		$str = preg_replace('/,$/', '', $str);
		// Put the dates into a string ready for SQL
		$datesql = '';
		foreach($dates as $date){
			$datesql .= "($week_id, $year_id, '$date'),";
		}
		$datesql = preg_replace('/,$/', '', $datesql);
		
		// Remove current entries for this week ID (incase user has de-selected some dates)
		$sql = 'DELETE FROM weekdates WHERE week_id = ? AND year_id = ?';
		$query = $this->db->query($sql, array($week_id, $year_id));
		
		// Now insert new dates
		$sql = 'INSERT INTO weekdates (week_id, year_id, date) 
				VALUES %s
				ON DUPLICATE KEY UPDATE week_id = %d';
		$sql = sprintf($sql, $datesql, $week_id);
		$query = $this->db->query($sql, array($week_id));
		
		return $query;
		
	}
	
	
	
	
	function get_dates($week_id = NULL, $year_id, $array_key = 'week_id'){
		
		$dates = array();
		
		if($week_id == NULL){
			
			$sql = 'SELECT week_id, date 
					FROM weekdates 
					WHERE year_id = ? 
					ORDER BY week_id ASC, date ASC';
			$query = $this->db->query($sql, array($year_id));
			
			if($query->num_rows() > 0){
				
				$result = $query->result();
				
				switch($array_key){
					case 'week_id':
					foreach($result as $row){
						if(!isset($dates[$row->week_id])){ $dates[$row->week_id] = array(); }
						array_push($dates[$row->week_id], $row->date);
					}
					break;
					
					case 'date':
					foreach($result as $row){
						$dates["{$row->date}"] = $row->week_id;
					}
					break;
				}
				
				return $dates;
				
			} else {
				
				$this->lasterr = 'No dates found.';
				return FALSE;
				
			}
			
		} else {
			
			$sql = 'SELECT date FROM weekdates 
					WHERE week_id = ?
					AND year_id = ?
					ORDER BY date ASC';
			$query = $this->db->query($sql, array($week_id, $year_id));
			
			if($query->num_rows() > 0){
				
				$result = $query->result();
				foreach($result as $row){
					array_push($dates, $row->date);
				}
				return $dates;
				
			} else {
				
				$this->lasterr = 'No dates found for given week.';
				return FALSE;
				
			}
			
		}
		
	}
	
	
	
	/**
	 * Show calendar so user can select dates for the week
	 *
	 * @param	int		week_id		ID of the editing week so we can highlight it
	 * @param	int		year_id		ID of the working acadmic year
	 */
	function calendar($week_id = NULL, $year_id){
		
		// Calendar preferences (+ load template from a view file)
		$prefs['start_day'] = 'monday';
		$prefs['month_type'] = 'long';
		$prefs['day_type'] = 'short';
		$prefs['template'] = $this->load->view('academic/weeks/caltemplate', NULL, TRUE);
		$this->load->library('calendar', $prefs);
		
		// Get all week dates
		$dates = $this->get_dates(NULL, $year_id, 'date');
		#die(print_r($dates));
		
		$this->load->model('years_model');
		$year = $this->years_model->get($year_id);

		$start['ts'] = strtotime($year->date_start);
		$start['m'] = date('m', $start['ts']);
		$start['y'] = date('Y', $start['ts']);
		
		$end['ts'] = strtotime($year->date_end);
		$end['m'] = date('m', $end['ts']);
		$end['y'] = date('Y', $end['ts']);
		
		#echo "Year {$year_id} starts in {$start['m']} of {$start['y']} and ends in {$end['m']} of {$end['y']}.";
		
		// Months for calendar
		$months = $this->get_months($year->date_start, $year->date_end);
		
		$html = '<table width="100%" id="cc">';
		$cols = 3;
		$c = 0;
		
		// Loop through months in this academic year and print the calendar
		foreach($months as $month){
			if($c == 0){ $html .= '<tr>'; }
			$html .= '<td valign="top">';
			$html .= $this->calendar->generate($month[0], $month[1], NULL, $dates,  $week_id);
			$html .= '</td>';
			$c++;
			if($c == $cols){ $html .= '</tr>'; $c = 0; }
		}
		
		$html .= '</table>';
		
		return($html);
		
	}
	
	
	
	
	/**
	 * Get the months in a given date range (academic year)
	 *
	 * @param	str		date_start		Start date in YYYY-MM-DD format
	 * @param	str		date_end		End date in YYYY-MM-DD format
	 * @return	array					[0] = array(YYYY, MM)
	 */
	function get_months($date_start, $date_end){
		
		$start = strtotime($date_start);
		$end = strtotime($date_end);
		
		$my = date('mY', $end);
		
		// Initialise array and add first month (start date of the year)
		$months = array();
		array_push($months, array(date('Y', $start), date('m', $start)));
		
		$f = ''; 
		
		while($start < $end){
			
			// Next month from start year
			$start = strtotime( date( 'Y-m-d', $start ).' next month'); 
			
			if(date('F', $start) != $f){
				$f = date('F', $start); 
				if(date('mY', $start) != $my && ($start < $end)){
					array_push($months, array(date('Y', $start), date('m', $start)));
				}
			}
			
		}
		
		// Last month
		array_push($months, array(date('Y', $end), date('m', $end)));
		
		return $months;
		
	}
	
	
	
	
}


/* End of file: app/models/weeks_model.php */