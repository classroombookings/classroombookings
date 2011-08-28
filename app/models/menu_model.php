<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Classroombookings. Hassle-free resource booking for schools. <http://classroombookings.com/>
 * Copyright (C) 2006-2011 Craig A Rodway <craig.rodway@gmail.com>
 *
 * This file is part of Classroombookings.
 * Classroombookings is licensed under the Affero GNU GPLv3 license.
 * Please see license-classroombookings.txt for the full license text.
 */

class Menu_model extends CI_Model
{
	
	
	function __construct()
	{
		parent::__construct();
	}
	
	
	/**
	 * Main navigation
	 */
	function main()
	{
		$subnav = array();
		$subnav[] = array('dashboard', lang('DASHBOARD'), 'dashboard', 'dashboard');
		$subnav[] = array('bookings', lang('BOOKINGS'), 'bookings', 'bookings');
		$subnav[] = array('configure', lang('CONFIGURE'), 'configure', 'configure');
		$subnav[] = array('reports', lang('REPORTS'), 'reports', 'reports');
		return $subnav;
	}
	
	
}




/* End of file: app/models/academic.php */