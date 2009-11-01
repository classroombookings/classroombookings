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
	var $ajax;
	

	function Bookings(){
		parent::Controller();
		
		// Required models
		$this->load->helper('cookie');
		$this->load->model('rooms_model');
		$this->load->model('years_model');
		
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
		$this->ajax = (array_key_exists('HTTP_X_REQUESTED_WITH', $_SERVER));
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
	 */
	function _index_room($room_id = NULL){
		
		// No room_id in param?
		if($room_id == NULL){
			// Get it from session..
			$room_id = $this->session->userdata('room_id');
			if(!$room_id){
				// Get it from cookie..
				$room_id = get_cookie('room_id');
			}
		}
		
		#echo var_dump($room_id);
		
		// Vars for sidebar (Room list)
		$bookable = !($this->auth->check('allrooms', TRUE));
		
		// Get academic info
		$academic = $this->_get_academic();
		
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
		$sidebar['cal'] = $this->calendar->generate_sidebar($caly, $calm, NULL, $academic['dates'], $academic['months']);
		
		$sidebar['rooms'] = $this->rooms_model->get_in_categories($bookable);
		$sidebar['cats'] = $this->rooms_model->get_categories_dropdown();
		$sidebar['cats'][-1] = 'Uncategorised';
		$sidebar['room_id'] = $room_id;
		$sidebar['weeks'] = $academic['weeks'];
		
		$tpl['title'] = 'Room View';
		$tpl['sidebar'] = $this->load->view('bookings/side/side-main', $sidebar, TRUE);
		$tpl['body'] = '<div id="tt">getting timetable for room id ' . $room_id . '</div>';
		
		$this->load->view($this->tpl, $tpl);
	}
	
	
	
	
	/**
	 * Timetable View: Day-at-a-time
	 **/
	function _index_day(){
		$tpl['pagetitle'] = 'Day View';
		$this->load->view($this->tpl, $tpl);
	}
	
	
	
	
	/** 
	 * Web-accessible page for loading timetable for a room.
	 *
	 * Can be loaded via AJAX or direct
	 */
	function room($room_id){
	
		// Store requested room ID in session and cookie
		$this->_store('room_id', $room_id);
		
		if($this->ajax){
			
			// Return the timetable HTML fragment via Ajax
			#echo "<p>You requested Room ID $room_id via AJAX.</p>";
			#echo "<pre>";
			#echo var_export($this->rooms_model->get($room_id), TRUE);
			#echo "</pre>";
			
			// To load the new timetable HTML fragment
			$data['room_id'] = $room_id;
			$data['date'] = $this->_get('date');
			// Call to bookings_model->tt($data);
			
			echo "This will be the new timetable view with the stored date but for Room ID $room_id.";
			
		} else {
			
			// No AJAX, just load the actual page but with a room ID
			return $this->_index_room($room_id);
			
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
		
		// Get academic information
		$academic = $this->_get_academic();
		
		// Decide how to output the calendar
		if($this->ajax){
			
			// Respond with the calendar HTML
			echo $this->calendar->generate_sidebar($url_year, $url_month, NULL, $academic['dates'], $academic['months']);
			
			// Send them an error if appropriate
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
	
	
	
	
}

/* End of file: /app/controllers/bookings.php */