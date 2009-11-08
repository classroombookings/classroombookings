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


class Account_model extends Model{
	
	
	function Account_model(){
		parent::Model();
	}
	
	
	/**
	 * Link definitions of pages in this section
	 */
	function subnav(){
		$subnav = array();
		// Other pages in this parent section
		$subnav[] = array('account/main', 'My Account', 'account');
		$subnav[] = array('account/activebookings', 'Active bookings', 'account');
		$subnav[] = array('account/previousbookings', 'Previous bookings', 'account');
		$subnav[] = array('account/changepassword', 'Change password', 'account.changepwd');
		return $subnav;
	}
	
	
}




/* End of file: app/models/account_model.php */