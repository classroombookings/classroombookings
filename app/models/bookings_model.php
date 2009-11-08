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
	 * @return	Fragment of HTML with generated timetable
	 */
	private function timetable_room($room_id, $week){
		if(empty($room_id)){
			$this->lasterr = 'No room specified for timetable.';
			return FALSE;
		}
		
		if(empty($week)){
			$this->lasterr = 'No week specified for timetable.';
			return FALSE;
		}
		
		$html = "Timetable. Room ID $room_id; Week beginning $week";
		
		$check = $this->rooms_model->permission_check($this->session->userdata('user_id'), $room_id);
		$html .= '<pre>' . var_export($check, TRUE) . '</pre>';
		$html .= $this->rooms_model->lasterr;
		
		return $html;
	}
	
	
}




/* End of file: /app/models/bookings_model.php */