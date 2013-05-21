<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

// Default CI stuff
$route['default_controller']		= 'school';
$route['scaffolding_trigger']		= "scaffolding";


// Classroom Bookings specific
$route['site']								= 'site/home';
$route['faq']									= 'site/faq';
$route['contact']							= 'site/contact';
$route['features']						= 'site/features';
$route['controlpanel']				= 'school/manage';


// Rooms
$route['rooms']								= 'rooms/index';
$route['rooms/fields']				= 'rooms/fields_index';
$route['rooms/fields/save']		= 'rooms/fields_save';
$route['rooms/fields/add']		=	'rooms/fields_add';

$route['rooms/fields/edit']					=	'rooms/fields_edit';
$route['rooms/fields/edit/:any']		=	'rooms/fields_edit';

$route['rooms/fields/delete']				=	'rooms/fields_delete';
$route['rooms/fields/delete/:any']	=	'rooms/fields_delete';


// Control panel options
$route['weeks']								= 'weeks/index';
$route['periods']							= 'periods/index';
$route['holidays']						= 'holidays/index';
$route['weekswizard']					= 'weekswizard/index';

$route['school']							= 'school/manage';

#$route['school/:any']							= 'school';
#$route['school/deta'] = 'school/$1';

$route['timetable']						= 'timetable/index';
$route['users']								= 'users/index';
$route['remote']							= 'remote/index';
$route['reports']							= 'reports/index';
$route['bookings']						= 'bookings/index';
$route['profile']							= 'profile/index';


// Help ;-)
$route['help']											= 'help/index';
$route['help/contents']							= 'help/contents';
#$route['help/:any']									= 'help/index';
#$route['help/:any/:any']						= 'help/index';
#$route['help/:any/:any/:any']				= 'help/index';
#$route['help/:any/:any/:any/:any']	= 'help/index';
#$route['help/(([a-z]+)[/]?)+'] = 'help/index/$1';
$route['help/:any'] = 'help/index';


// Login & logout
#$route['signup']							= 'signup/index';
#$route['login/submit']				= 'login/submit';
#$route['login/:any']					= 'login/index';		// login/<school-code> will allow people to login to that school
#$route['([[:alnum:]]{3,10})']								= 'login/index/$1';		// as above


?>
