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


class Bookings_model extends Model{
	
	
	var $lasterr;
	var $cols;
	
	
	function Bookings_model(){
		parent::Model();
	}
	
	
	
	
	/**
	 * Main timetable-showing method.
	 *
	 * Checks the view mode and shows the appropriate view.
	 */
	public function timetable($data){
		
		// Get settings for view mode
		$tt['view'] = $this->settings->get('tt_view');
		$tt['cols'] = $this->settings->get('tt_cols');
		
		$this->cols = $tt['cols'];
		
		switch($tt['view']){
			case 'room':
				
				// Should have room_id and week in $data
				return $this->timetable_room($data['room_id'], $data['week']);
				break;
			
			case 'day':
				
				// Should just have date in $data
				return $this->timetable_day($data['date']);
				break;
			
			default:
				$this->laster = 'No valid mode chosen.';
				return FALSE;
				break;
		}
		
	}
	
	
	
	
	/**
	 * Timetable for room view mode.
	 *
	 * @access	private
	 * @param	int		room_id		Room ID to load
	 * @param	string	week		Date of start of week to show
	 * @return	Fragment of HTML with generated timetable (controller will load this appropriately)
	 */
	private function timetable_room($room_id, $week_start){
		
		log_message('debug', 'Asked to load timetable for week: ' . $week_start . ' and room: ' . $room_id . '...');
		
		if(empty($room_id)){
			$this->lasterr = 'No room specified for timetable.';
			return FALSE;
		}
		
		if(empty($week_start)){
			$this->lasterr = 'No week specified for timetable.';
			return FALSE;
		}
		
		// Initialise variable to hold HTML code
		$html = '';
		
		// Get info on the room and week
		$room = $this->rooms_model->get($room_id);
		$week = $this->weeks_model->get_by_date($week_start);
		
		// Get year ID
		$year_id = $this->session->userdata('year_working');
		if($year_id == FALSE){
			// If not in session, get active one configured.
			$year_id = $this->years_model->get_active_id();
		}
		
		// Get the weeks in the current working academic year.
		// Prevents non-weeks from being linked to in the nav header
		$weeks_in_year = $this->weeks_model->get_dates(NULL, $year_id, 'date');
		if(!is_array($weeks_in_year)){ $weeks_in_year = array(); }
		
		// Check if provided date is in the academic year
		if(!array_key_exists($week_start, $weeks_in_year)){
			// Nope! Set $week_start to first week in the year
			$week_start = key($weeks_in_year);
		}
		
		#print_r($weeks_in_year);
		
		// Set up navigating header
		$week_prev = date('Y-m-d', strtotime('-7 days', strtotime($week_start)));
		$week_next = date('Y-m-d', strtotime('+7 days', strtotime($week_start)));
		
		// Is last week an actual configured week in the year? If not - go back until we find one.
		if(!array_key_exists($week_prev, $weeks_in_year)){
			while(key($weeks_in_year) !== $week_start) next($weeks_in_year);
			prev($weeks_in_year);
			$week_prev = key($weeks_in_year);
		}
		
		// Is next week a configured week in the year? ........
		if(!array_key_exists($week_next, $weeks_in_year)){
			while(key($weeks_in_year) !== $week_start) next($weeks_in_year);
			next($weeks_in_year);
			$week_next = key($weeks_in_year);
		}
		
		// Variables needed for navigation header. Then load the view.
		$nav['mode'] = 'week';
		$nav['week'] = $week;
		$nav['week_start'] = $week_start;
		$nav['prev']['text'] = '&lt; Previous Week';
		$nav['prev']['href'] = (!empty($week_prev)) ? 'bookings/week/' . $week_prev : NULL;
		$nav['next']['text'] = 'Next Week &gt;';
		$nav['next']['href'] = (!empty($week_next)) ? 'bookings/week/' . $week_next : NULL;
		$nav = $this->load->view('bookings/navheader', $nav, TRUE);
		
		$html .= $nav;
		
		$html .= "Timetable. Room ID $room_id; Week beginning $week_start.";
		
		$this->auth->set_user($this->session->userdata('user_id'));
		$this->auth->set_room($room_id);
		
		$perm = 'bookings.create.one';
		$check = $this->auth->check_room($perm);
		
		// Load the Grid library
		$this->load->library('crbsgrid');
		
		// Give it some data
		$this->crbsgrid->set_type('room');
		$this->crbsgrid->set_room($room_id);
		$this->crbsgrid->set_date($week_start);
		$this->crbsgrid->set_columns($this->cols);
		
		// Ger period info
		$periods = $this->periods_model->get();
		$this->crbsgrid->set_periods($periods);
		
		// Generate the table
		$html .= $this->crbsgrid->generate();
		
		
		$html .= '<pre>User has permission to do ' . $perm . ': ' . var_export($check, TRUE) . '</pre>';
		#$html .= var_export($perms, TRUE);
		
		return $html;
		
	}
	
	
	
	
	/**
	 * Timetable for day view mode
	 *
	 * @access private
	 * @param	string		week		Date to show
	 * @return	Fragment of HTML with generated timetable
	 */
	private function timetable_day($date){
	
		log_message('debug', 'Asked to load timetable for date: ' . $date);
		
		if(empty($date)){
			$this->lasterr = 'No date specified for timetable.';
			return FALSE;
		}
		
		// Initialise HTML variable
		$html = '';
		
		// Get info on the week that the date is in
		$week = $this->weeks_model->get_by_date($date);
		
		// Get year ID
		$year_id = $this->session->userdata('year_working');
		if($year_id == FALSE){
			// If not in session, get active one configured.
			$year_id = $this->years_model->get_active_id();
		}
		
		// Get the weeks in the current working academic year.
		// Prevents non-weeks from being linked to in the nav header
		$weeks_in_year = $this->weeks_model->get_dates(NULL, $year_id, 'date');
		if(!is_array($weeks_in_year)){ $weeks_in_year = array(); }
		
		// Set up navigating header
		$date_prev = date('Y-m-d', strtotime('-1 day', strtotime($date)));
		$date_next = date('Y-m-d', strtotime('+1 day', strtotime($date)));
		
		/*
		// Find week start of current date
		$dateparts = explode('-', $date);
		$crbs_date = mktime(0, 0, 0, $dateparts[1], $dateparts[2], $dateparts[0]);
		if( date("w", $crbs_date) == 1 ){
			$week_date = date("Y-m-d", $crbs_date);
		} else {
			$week_date = date("Y-m-d", strtotime("last Monday", $crbs_date));
		}
		
		// Find week start of 'previous' date
		$dateparts = explode('-', $date_prev);
		$crbs_date = mktime(0, 0, 0, $dateparts[1], $dateparts[2], $dateparts[0]);
		if( date("w", $crbs_date) == 1 ){
			$week_prev = date("Y-m-d", $crbs_date);
		} else {
			$week_prev = date("Y-m-d", strtotime("last Monday", $crbs_date));
		}
		
		// Find week start of 'next' date
		$dateparts = explode('-', $date_next);
		$crbs_date = mktime(0, 0, 0, $dateparts[1], $dateparts[2], $dateparts[0]);
		if( date("w", $crbs_date) == 1 ){
			$week_next = date("Y-m-d", $crbs_date);
		} else {
			$week_next = date("Y-m-d", strtotime("last Monday", $crbs_date));
		}
		
		// Is last week an actual configured week in the year? If not - go back until we find one.
		if(!array_key_exists($week_prev, $weeks_in_year)){
			while(key($weeks_in_year) !== $week_date) next($weeks_in_year);
			prev($weeks_in_year);
			$week_prev = key($weeks_in_year);
		}
		
		// Is next week a configured week in the year? ........
		if(!array_key_exists($week_next, $weeks_in_year)){
			while(key($weeks_in_year) !== $week_date) next($weeks_in_year);
			next($weeks_in_year);
			$week_next = key($weeks_in_year);
		}
		*/
		
		// Variables needed for navigation header. Then load the view.
		$nav['mode'] = 'day';
		$nav['date'] = $date;
		$nav['week'] = $week;
		$nav['prev']['text'] = '&lt; Previous Day';
		$nav['prev']['href'] = (!empty($date_prev)) ? 'bookings/date/' . $date_prev : NULL;
		$nav['next']['text'] = 'Next Week &gt;';
		$nav['next']['href'] = (!empty($date_next)) ? 'bookings/date/' . $date_next : NULL;
		$nav = $this->load->view('bookings/navheader', $nav, TRUE);
		
		$html .= $nav;
		
		$html .= "Timetable. Date: $date.";
		
		$this->auth->set_user($this->session->userdata('user_id'));
		
		
		
		return $html;
		
	}
	
	
}




/* End of file: /app/models/bookings_model.php */