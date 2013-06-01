<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['nav'] = array(

	'home' => array(
		'label' => lang('home'),
		'permission' => 'crbs.dashboard.view',
		'class' => 'dashboard',
	),
	
	'bookings' => array(
		'label' => lang('bookings'),
		'permission' => 'bookings.view',
		'class' => 'bookings',
	),
	
	'configure' => array(
		'label' => lang('configure'),
		'permission' => 'crbs.configure',
		'class' => 'configure',
	),
	
	'reports' => array(
		'label' => lang('reports'),
		'permission' => 'reports.view',
		'class' => 'reports',
	),
	
	'event_log' => array(
		'label' => lang('event_log'),
		'permission' => 'crbs.eventlog.view',
		'class' => 'event-log',
	),
		
);