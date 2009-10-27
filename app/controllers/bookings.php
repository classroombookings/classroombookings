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
	Note about storing Room ID/Date in session/cookie.
	
	Once one fragment is loaded with one piece of data (e.g. room),
	CRBS needs to know this for when the timetable gets loaded with
	another piece of data (e.g. date).
	
	When loading it via one piece of data, it retrieves the other, stored, piece.
*/


class Bookings extends Controller {


	var $tpl;
	var $ajax;
	

	function Bookings(){
		parent::Controller();
		$this->load->helper('cookie');
		$this->load->model('rooms_model');
		$this->load->model('years_model');
		$this->load->library('calendar');
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
		
		// Academic stuff
		$year_id = $this->session->userdata('year_working');
		$year = $this->years_model->get($year_id);
		$months = $this->weeks_model->get_months($year->date_start, $year->date_end);
		$dates = $this->weeks_model->get_dates(NULL, $year_id, 'date');
		$sidebar['cal'] = $this->calendar->generate($months[0][0], $months[0][1], $dates, NULL, NULL);
		
		$sidebar['rooms'] = $this->rooms_model->get_in_categories($bookable);
		$sidebar['cats'] = $this->rooms_model->get_categories_dropdown();
		$sidebar['cats'][-1] = 'Uncategorised';
		$sidebar['room_id'] = $room_id;
		
		$tpl['title'] = 'Room View';
		$tpl['sidebar'] = $this->load->view('bookings/room/side-rooms', $sidebar, TRUE);
		$tpl['body'] = '<div id="tt">getting timetable for room id ' . $room_id . '</div>';
		
		$this->load->view($this->tpl, $tpl);
	}
	
	
	
	
	/**
	 * Timetable View: Day
	 **/
	function _index_day(){
		$tpl['pagetitle'] = 'Day View';
		$this->load->view($this->tpl, $tpl);
	}
	
	
	
	
	/** 
	 * Web-accessible page for loading a room.
	 *
	 * Can be loaded via AJAX or direct
	 */
	function room($room_id){
	
		// Store requested room ID in session and cookie
		$this->_store('room_id', $room_id);
		
		if($this->ajax){
			
			// Return the timetable HTML fragment via Ajax
			echo "<p>You requested Room ID $room_id via AJAX.</p>";
			echo "<pre>";
			echo var_export($this->rooms_model->get($room_id), TRUE);
			echo "</pre>";
			
		} else {
			
			// No AJAX, just load the actual page but with a room ID
			return $this->_index_room($room_id);
			
		}
		
	}
	
	
	
	
	function _store($key, $value){
		// Put in session
		$this->session->set_userdata($key, $value);
		// Set cookie data
		$cookie['expire'] = 60 * 60 * 24 * 14;		// 14 days
		$cookie['name'] = $key;
		$cookie['value'] = $value;
		set_cookie($cookie);
	}
	
	
	
	
}


?>