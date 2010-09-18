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

/*
	Note about storing Room ID/Date in session/cookie (_store() method)
	
	Once one fragment is loaded with one piece of data (e.g. room),
	CRBS needs to know this for when the timetable gets loaded with
	another piece of data (e.g. date).
	
	When loading it via one piece of data, it retrieves the other stored piece
	in order to generate the proper timetable.
*/


class Bookings extends Controller {


	var $tpl;
	

	function Bookings(){
		parent::Controller();
		
		// Required models
		$this->load->helper('cookie');
		$this->load->model('rooms_model');
		$this->load->model('bookings_model');
		
		// Calendar preferences (+ load template from a view file)
		$prefs['start_day'] = 'monday';
		$prefs['month_type'] = 'long';
		$prefs['day_type'] = 'abr';
		$prefs['show_next_prev'] = TRUE;
		$prefs['next_prev_url'] = site_url('bookings/calendar');
		$prefs['template'] = $this->load->view('bookings/side/caltemplate', NULL, TRUE);
		$this->load->library('calendar', $prefs);
		
		// Misc things
		$this->tpl = $this->config->item('template');
		$this->output->enable_profiler($this->config->item('profiler'));
	}
	
	
	
	
	/**
	 * Main index page for bookings.
	 *
	 * Retrive the timetable view type, and load the proper index function.
	 */
	function index(){
		$this->auth->check('bookings');
		$tt['view'] = $this->settings->get('tt_view');
		$tt['cols'] = $this->settings->get('tt_cols');
		
		switch($tt['view']){
			case 'day': return $this->_index_day(); break;
			case 'room': return $this->_index_room(); break;
		}
	}
	
	
	
	
	/** 
	 * Timetable View: Room.
	 *
	 * Not public - can only be called by other methods in this controller.
	 *
	 * @param	room_id		ID of room to load. If none given, retrieve from session/cookie
	 * @param	week		First date of week to load. If none, get from session/cookie
	 */
	private function _index_room($room_id = NULL, $week = NULL){
		
		log_message('debug', 'Bookings _index_room() method');
		
		// Var for sidebar (Room list)
		$bookable = !($this->auth->check('allrooms', TRUE));
		
		log_message('debug', 'Set $bookable variable');
		
		// Get all the available rooms (for user's permissions)
		$available_rooms = $this->rooms_model->get_in_categories($bookable);
		
		log_message('debug', 'Got available rooms');
		
		// No room_id in param?
		if($room_id == NULL){
			// Try and find stored one
			$room_id = $this->_get('crbsb.room_id');
			if(empty($room_id)){
				// Nothing - is user a room owner?
				#echo "Checking if owner..";
				$pwn3d = $this->rooms_model->owned_by($this->session->userdata('user_id'));
				#echo var_dump($pwn3d);
				if($pwn3d != FALSE){
					// They are!
					#echo "They are!";
					$room_id = $pwn3d[0];
				} else {
					// No!
					#echo "Not a room owner. Last resort, getting first available room...";
					$room = current($available_rooms);
					if(!empty($room)){
						$room_id = $room[0]->room_id;
					}
				}
			}
		}
		// Room ID is now hopefully set. If not, then user has no permission to see any rooms..
		$this->_store('crbsb.room_id', $room_id);
		
		log_message('debug', 'Stored data crbsb.room_id');
		
		// No week in param?
		if($week == NULL){
			$week = $this->_get('crbsb.week');
			if(empty($week)){
				$week = $this->_get_monday(date('Y-m-d'));
				$this->_store('crbsb.week', $week);
			}
		}
		
		
		// Get academic info
		$academic = $this->_get_academic();
		
		// If we had a week supplied, load the calendar for the month in that week
		if(!empty($week)){
			list($y, $m, $d) = explode('-', $week);
			$calm = $m;
			$caly = $y;
		}
		
		// Load up the calendar picker with any overriding choices!
		$url_month = $this->_get('cal_month');
		$url_year = $this->_get('cal_year');
		if(empty($url_month) && empty($url_year)){
			$calm = date('m');
			$caly = date('Y');
		} else {
			$calm = $url_month;
			$caly = $url_year;
		}
		
		// Get the actual requested date (for date highlighting in sidebar calendar)
		$cur = $this->_get('crbsb.week_requested_date');
		
		// Set up the sidebar
		$sidebar['cal'] = $this->calendar->generate_sidebar($caly, $calm, $academic, $cur);
		$sidebar['rooms'] = $available_rooms;
		$sidebar['cats'] = $this->rooms_model->get_categories_dropdown();
		$sidebar['cats'][-1] = 'Uncategorised';
		$sidebar['room_id'] = $room_id;
		$sidebar['weeks'] = $academic['weeks'];
		
		// Main page info
		$tpl['title'] = 'Bookings (Room/Week View)';
		$tpl['sidebar'] = $this->load->view('bookings/side/side-main', $sidebar, TRUE);
		
		log_message('debug', 'Assigned sidebar to main template variable');
		
		// Container for timetable
		$tpl['body'] = '<div id="tt">';
		
		// Load up timetable
		$data['room_id'] = $room_id;
		$data['week'] = $week;
		$timetable = $this->bookings_model->timetable($data);
		
		log_message('debug', 'Called timetable() method on bookings_model');
		
		if($timetable == FALSE){
			
			// Timetable returned false, show an error.
			$tpl['alert'] = $this->msg->err($this->bookings_model->lasterr);
			
		} else {
			
			// Add timetable to page.
			$tpl['body'] .= $timetable;
			
		}
		
		$tpl['body'] .= '</div>';
		$tpl['body'] .= $this->load->view('bookings/javascript', NULL, TRUE);
		$tpl['js'] = array('js/crbs-bookings.js');
		
		$this->load->view($this->tpl, $tpl);
	}
	
	
	
	
	/**
	 * Timetable View: Day-at-a-time
	 **/
	/* function _index_day(){
		
		// No date in param?
		if($date == NULL){
			// Get it from session..
			$date = $this->_get('crbsb.date');
		}
		
		// Vars for sidebar (Room list)
		$bookable = !($this->auth->check('allrooms', TRUE));
		
		// Get academic info
		$academic = $this->_get_academic();
		
		// Get room ID from session
		$room_id = $this->_get('crbsb.room_id');
		
		// Load up the calendar picker!
		$url_month = $this->_get('cal_month');
		$url_year = $this->_get('cal_year');
		if(empty($url_month) && empty($url_year)){
			$calm = date('m');
			$caly = date('Y');
		} else {
			$calm = $url_month;
			$caly = $url_year;
		}
		$sidebar['cal'] = $this->calendar->generate_sidebar($caly, $calm, $academic);
		
		$sidebar['rooms'] = $this->rooms_model->get_in_categories($bookable);
		$sidebar['cats'] = $this->rooms_model->get_categories_dropdown();
		$sidebar['cats'][-1] = 'Uncategorised';
		$sidebar['room_id'] = $room_id;
		$sidebar['weeks'] = $academic['weeks'];
		
		$tpl['title'] = 'Bookings';
		$tpl['sidebar'] = $this->load->view('bookings/side/side-main', $sidebar, TRUE);
		$tpl['body'] = '<div id="tt">getting timetable for date ' . $date . ' and room ' . $room_id . '</div>';
		
		$this->load->view($this->tpl, $tpl);
		
	} */
	
	
	
	
	/** 
	 * Web-accessible page for loading timetable for a room. (in room view only)
	 *
	 * Can be loaded via AJAX or directly.
	 */
	function room($room_id){
		
		$this->auth->check('bookings');
	
		// Store requested room ID in session and cookie
		$this->_store('crbsb.room_id', $room_id);
		
		$data['room_id'] = $room_id;
		$data['week'] = $this->_get('crbsb.week');
		
		if(IS_XHR){
			
			// Fetch timetable
			$timetable = $this->bookings_model->timetable($data);
			
			if($timetable == FALSE){
				$data['error'] = $this->bookings_model->lasterr;
				$this->load->view('parts/ajaxerr', $data);
			} else {
				// Return the timetable HTML fragment via Ajax
				echo $timetable;
			}
			
		} else {
			
			// No AJAX, just load the actual page but with a room ID
			return $this->_index_room($data['room_id'], $data['week']);
			
		}
		
	}
	
	
	
	
	/** 
	 * Function for loading a calendar for a week (used for choosing a date/week in the room view)
	 *
	 * Can be loaded via AJAX or directly.
	 */
	function week($date){
		
		$this->auth->check('bookings');
		
		// Put the actual requested day in the session
		$this->_store('crbsb.week_requested_date', $date);
		
		// Find start of week of requested date
		$dateparts = explode('-', $date);
		$crbs_date = mktime(0, 0, 0, $dateparts[1], $dateparts[2], $dateparts[0]);
		if( date("w", $crbs_date) == 1 ){
			$crbs_m = date("Y-m-d", $crbs_date);
		} else {
			$crbs_m = date("Y-m-d", strtotime("last Monday", $crbs_date));
		}
		
		// Store week date in session and cookie
		$this->_store('crbsb.week', $crbs_m);
		
		$data['room_id'] = $this->_get('crbsb.room_id');
		$data['week'] = $crbs_m;
		
		#print_r($data);
		
		if(IS_XHR){
			
			// Fetch timetable
			$timetable = $this->bookings_model->timetable($data);
			
			if($timetable == FALSE){
				$data['error'] = $this->bookings_model->lasterr;
				$this->load->view('parts/ajaxerr', $data);
			} else {
				// Return the timetable HTML fragment via Ajax
				echo $timetable;
			}
			
		} else {
			
			// No AJAX, just load the actual page but with a week date
			return $this->_index_room($data['room_id'], $data['week']);
			
		}
		
	}
	
	
	
	
	/**
	 * Change the calendar-picker month.
	 *
	 * 1) Store requested year & month.
	 * 2) If requested via AJAX, return the HTML. Otherwise, re-load booking page.
	 */
	function calendar($url_year, $url_month){
		
		$err = FALSE;
		
		// Check the date is OK
		if(checkdate($url_month, 1, $url_year) == TRUE){
			// Yes! Store them.
			$this->_store('cal_year', $url_year);
			$this->_store('cal_month', $url_month);
		} else {
			// Nada! Set to current month/year
			$err = TRUE;
			$this->lasterr = 'Invalid date selected.';
			$url_year = date('Y');
			$url_month = date('m');
		}
		
		// Decide how to output the calendar
		if(IS_XHR){
			
			// Get academic information
			$academic = $this->_get_academic();
			
			// Get the actual date requested before if any (to set class)
			$cur = $this->_get('crbsb.week_requested_date');
			// Respond with the calendar HTML
			echo $this->calendar->generate_sidebar($url_year, $url_month, $academic, $cur);
			
			// Send them an error as well if appropriate
			if($err == TRUE){ echo $this->msg->err($this->lasterr); }
			
		} else {
			
			if($err == FALSE){
				
				// Re-load the main bookings page.
				// The requested month & year have been stored, and will be fetched on page-load.
				return $this->index();
				
			} else {
				
				// Add a flashdata error message and redirect to the main bookings page
				$this->msg->add('err', $this->lasterr);
				redirect('bookings');
				
			}
			
		}
		
	}
	
	
	
	
	/**
	 * Retrieve a load of important academic details and return in an array.
	 *
	 * Made into a function here as data is used in more than 1 place.
	 */
	function _get_academic(){
		$data = array();
		
		// Get the working academic year
		$data['year_id'] = $this->session->userdata('year_working');
		// Get info about the year
		$data['year'] = $this->years_model->get($data['year_id']);
		// Get the start and end months of the year
		$data['months'] = $this->weeks_model->get_months($data['year']->date_start, $data['year']->date_end);
		// Get the week dates and the week_id of them
		$data['dates'] = $this->weeks_model->get_dates(NULL, $data['year_id'], 'date');
		// Get the academic weeks
		$data['weeks'] = $this->weeks_model->get(NULL, NULL, $data['year_id']);
		
		return $data;
	}
	
	
	
	
	/**
	 * Store a piece of data in 'memory' - session and cookie
	 *
	 * See the note at the top of this file for why.
	 */
	function _store($key, $value){
		// Put in session
		$this->session->set_userdata($key, $value);
		// Set cookie data
		$cookie['expire'] = 60 * 60 * 24 * 14;		// 14 days
		$cookie['name'] = $key;
		$cookie['value'] = $value;
		#echo "Storing..." . var_export($cookie, TRUE);
		set_cookie($cookie);
	}
	
	
	
	
	/**
	 * Get a piece of stored data from 'memory' - session or cookie
	 *
	 * See the note at the top of this file for why.
	 */
	function _get($key){
		// Try to get from session first
		$value = $this->session->userdata($key);
		
		// Failing that, get from cookie instead
		if(empty($value)){
			$value = get_cookie($key);
		}
		
		// Send back whatever value we got
		return $value;
	}
	
	
	
	
	/**
	 * Get monday of a date
	 */
	function _get_monday($date){
		// Find start of week of requested date
		$dateparts = explode('-', $date);
		$crbs_date = mktime(0, 0, 0, $dateparts[1], $dateparts[2], $dateparts[0]);
		if( date("w", $crbs_date) == 1 ){
			$crbs_m = date("Y-m-d", $crbs_date);
		} else {
			$crbs_m = date("Y-m-d", strtotime("last Monday", $crbs_date));
		}
		return $crbs_m;
	}
	
	
	
	
}

/* End of file: /app/controllers/bookings.php */