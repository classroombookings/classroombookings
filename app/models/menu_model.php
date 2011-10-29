<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Classroombookings. Hassle-free resource booking for schools. <http://classroombookings.com/>
 * Copyright (C) 2006-2011 Craig A Rodway <craig.rodway@gmail.com>
 *
 * This file is part of Classroombookings.
 * Classroombookings is licensed under the Affero GNU GPLv3 license.
 * Please see license-classroombookings.txt for the full license text.
 */

/**
 * The menu model contains functions that return arrays of menu items specific 
 * to pages within the app. 
 *
 * 0: Slug
 * 1: Title
 * 2: Permission required
 * 3: icon class
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
		$subnav[] = array('dashboard', lang('DASHBOARD'), 'crbs.dashboard.view', 'dashboard');
		$subnav[] = array('bookings', lang('BOOKINGS'), 'bookings.view', 'bookings');
		$subnav[] = array('configure', lang('CONFIGURE'), 'crbs.configure', 'configure');
		$subnav[] = array('reports', lang('REPORTS'), 'reports.view', 'reports');
		$subnav[] = array('events', 'Event Log', 'crbs.eventlog.view', 'event-log');
		return $subnav;
	}
	
	
	
	/**
	 * Configuration navigation
	 */
	function configure()
	{
		$subnav = array();
		$subnav[] = array('configure/settings', lang('GENERAL_SETTINGS'), 'crbs.configure.settings', 'configure-settings');
		$subnav[] = array('authentication', lang('AUTHENTICATION'), 'crbs.configure.authentication', 'configure-authentication');
		$subnav[] = array('users', lang('USERS'), 'users.view', 'configure-users');
		$subnav[] = array('groups', lang('GROUPS'), 'groups.view', 'configure-groups');
		$subnav[] = array('permissions', lang('PERMISSIONS'), 'permissions.view', 'configure-permissions');
		$subnav[] = array('quota', 'Quota', 'permissions.view', 'configure-quota');
		$subnav[] = array('departments', lang('DEPARTMENTS'), 'departments.view', 'configure-departments');
		$subnav[] = array('rooms/manage', lang('ROOMS'), 'rooms.view', 'configure-rooms');
		$subnav[] = array('academic/years', lang('ACADEMIC_YEARS'), 'years.view', 'configure-years');
		$subnav[] = array('academic/terms', lang('TERM_DATES'), 'terms.view', 'configure-terms');
		$subnav[] = array('academic/weeks', lang('TIMETABLE_WEEKS'), 'weeks.view', 'configure-weeks');
		$subnav[] = array('academic/holidays', lang('HOLIDAYS'), 'holidays.view', 'configure-holidays');
		$subnav[] = array('academic/periods', lang('PERIODS'), 'periods.view', 'configure-periods');
		return $subnav;
	}
	
	
	
	
	/**
	 * Configure / Users
	 */
	function users()
	{
		$subnav = array();
		$subnav[] = array('users/add', 'Add new user', 'users.add', 'add');
		$subnav[] = array('users/import', 'Bulk import users', 'users.import', 'upload');
		return $subnav;
	}
	
	
	
	
	/**
	 * Configure / Groups
	 */
	function groups()
	{
		$subnav = array();
		$subnav[] = array('groups/add', 'Add new group', 'groups.add', 'add');
		return $subnav;
	}
	
	
	
	
	/**
	 * Configure / Permissions
	 */
	function permissions()
	{
		$subnav = array();
		$subnav[] = array('permissions/assign_role', 'Assign role', 'permissions', 'assign');
		return $subnav;
	}
	
	
	
	
	/**
	 * Configure / Departments
	 */
	function departments()
	{
		$subnav = array();
		$subnav[] = array('departments/add', 'Add new department', 'departments.add', 'add');
		return $subnav;
	}
	
	
	
	
	
}


/* End of file: app/models/menu_model.php */