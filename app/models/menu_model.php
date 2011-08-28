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
	
	
	
	/**
	 * Configuration navigation
	 */
	function configure()
	{
		$subnav = array();
		$subnav[] = array('settings', lang('DISPLAY_SETTINGS'), 'configure', 'configure-settings');
		$subnav[] = array('authentication', lang('AUTHENTICATION'), 'configure', 'configure-authentication');
		$subnav[] = array('security/users', lang('USERS'), 'users', 'configure-users');
		$subnav[] = array('security/groups', lang('USER_GROUPS'), 'groups', 'configure-groups');
		$subnav[] = array('security/permissions', lang('GROUP_PERMISSIONS'), 'permissions', 'configure-permissions');
		$subnav[] = array('departments', lang('DEPARTMENTS'), 'departments', 'configure-departments');
		$subnav[] = array('rooms', lang('ROOMS'), 'rooms', 'configure-rooms');
		$subnav[] = array('academic/years', lang('ACADEMIC_YEARS'), 'years', 'configure-years');
		$subnav[] = array('academic/terms', lang('TERM_DATES'), 'terms', 'configure-terms');
		$subnav[] = array('academic/weeks', lang('TIMETABLE_WEEKS'), 'weeks', 'configure-weeks');
		$subnav[] = array('academic/holidays', lang('HOLIDAYS'), 'holidays', 'configure-holidays');
		$subnav[] = array('academic/periods', lang('PERIODS'), 'periods', 'configure-periods');
		return $subnav;
	}
	
	
}




/* End of file: app/models/academic.php */