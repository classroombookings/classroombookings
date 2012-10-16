<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['nav'] = array(

	'dashboard' => array(
		'label' => lang('DASHBOARD'),
		'permission' => 'crbs.dashboard.view',
		'class' => 'dashboard',
	),
	
	'bookings' => array(
		'label' => lang('BOOKINGS'),
		'permission' => 'bookings.view',
		'class' => 'bookings',
	),
	
	'configure' => array(
		'label' => lang('CONFIGURE'),
		'permission' => 'crbs.configure',
		'class' => 'configure',
		
		'nav' => array(
			
			'configure/settings' => array(
				'label' => lang('GENERAL_SETTINGS'),
				'permission' => 'crbs.configure.settings',
				'class' => 'configure-settings',
			),
			
			'authentication' => array(
				'label' => lang('AUTHENTICATION'),
				'permission' => 'crbs.configure.authentication',
				'configure-authentication',
			),
			
			'users' => array(
				'label' => lang('USERS'),
				'permission' => 'users.view',
				'class' => 'configure-users',
				
				'nav' => array(
					
					'users/set' => array(
						'label' => 'Add new user',
						'permission' => 'users.add',
						'class' => 'add',
					),
					
					'users/import' => array(
						'label' => 'Bulk import users',
						'permission' => 'users.import',
						'class' => 'upload',
					),
					
				),
				
			),
			
			'groups' => array(
				'label' => lang('GROUPS'),
				'permission' => 'groups.view',
				'class' => 'configure-groups',
				
				'nav' => array(
					
					'groups/set' => array(
						'label' => 'Add new group',
						'permission' => 'groups.add',
						'class' => 'add',
					),
					
				),
				
			),
			
			'permissions' => array(
				'label' => lang('PERMISSIONS'),
				'permission' => 'permissions.view',
				'class' => 'configure-permissions',
				
				'nav' => array(
					
					'permissions/assign_role' => array(
						'label' => 'Assign role',
						'permission' => 'permissions',
						'class' => 'assign',
					),
					
				),
				
			),
			
			'quota' => array(
				'label' => 'Quota',
				'permission' => 'permissions.view',
				'class' => 'configure-quota',
			),
			
			'departments' => array(
				'label' => lang('DEPARTMENTS'),
				'permission' => 'departments.view',
				'class' => 'configure-departments',
				
				'nav' => array(
					'departments/set' => array(
						'label' => 'Add new department',
						'permission' => 'departments.add',
						'class' => 'add',
					),
				
				),
				
			),
			
			'rooms/manage' => array(
				'label' => lang('ROOMS'),
				'permission' => 'rooms.view',
				'class' => 'configure-rooms',
			),
			
			'academic/years' => array(
				'label' => lang('ACADEMIC_YEARS'),
				'permission' => 'years.view',
				'class' => 'configure-years',
				
				'nav' => array(
					'academic/years/set' => array(
						'label' => 'Add new academic year',
						'permission' => 'years.add',
						'class' => 'add',
					),
					
				),
				
			),
			
			'academic/terms' => array(
				'label' => lang('TERM_DATES'),
				'permission' => 'terms.view',
				'class' => 'configure-terms',
			),
			
			'academic/weeks' => array(
				'label' => lang('TIMETABLE_WEEKS'),
				'permission' => 'weeks.view',
				'class' => 'configure-weeks',
			),
			
			'academic/holidays' => array(
				'label' => lang('holidays.view'),
				'permission' => 'holidays.view',
				'class' => 'configure-holidays',
			),
			
			'academic/periods' => array(
				'label' => lang('PERIODS'),
				'permission' => 'periods.view',
				'class' => 'configure-periods',
			),
			
		),
		
	),
	
	'reports' => array(
		'label' => lang('REPORTS'),
		'permission' => 'reports.view',
		'class' => 'reports',
	),
	
	'events' => array(
		'label' => 'Event Log',
		'permission' => 'crbs.events.view',
		'event-log',
	),
		
);