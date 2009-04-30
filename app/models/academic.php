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


class Academic extends Model{
	
	
	function Academic(){
		parent::Model();
	}
	
	
	/**
	 * Link definitions of pages in this section
	 */
	function subnav(){
		$subnav = array();
		// Other pages in this parent section
		$subnav[] = array('academic/years', 'Years', 'years');
		$subnav[] = array('academic/terms', 'Term dates', 'terms');
		$subnav[] = array('academic/weeks', 'Timetable weeks', 'weeks');
		$subnav[] = array('academic/periods', 'Periods', 'periods');
		$subnav[] = array('academic/holidays', 'Holidays', 'holidays');
		return $subnav;
	}
	
	
}




/* End of file: app/models/academic.php */