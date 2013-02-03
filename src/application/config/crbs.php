<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Filter: number of items per page
 */
$config['per_page'] = array(
	'10' => '10',
	'25' => '25',
	'42' => '42',
	'50' => '50',
	'100' => '100',
);


/**
 * Quota: types
 */
$config['quota_types'] = array(
	'time_day' => 'quota_type_time_day',
	'time_week' => 'quota_type_time_week', 
	'time_month' => 'quota_type_time_month',
	'time_term' => 'quota_type_time_term',
	'time_year' => 'quota_type_time_year',
	'booking_day' => 'quota_type_booking_day',
	'booking_week' => 'quota_type_booking_week',
	'booking_month' => 'quota_type_booking_month',
	'booking_term' => 'quota_type_booking_term',
	'booking_year' => 'quota_type_booking_year',
	'concurrent' => 'quota_type_concurrent',
);


$config['permission_entities'] = array(
	'E' => 'Everyone',
	'D' => 'Department',
	'G' => 'Group',
	'U' => 'User',
	'R' => 'Role',
);