<?php
echo $this->session->flashdata('saved');


// Menu for all users
$i = 0;
$menu[$i]['text'] = 'Bookings';
$menu[$i]['icon'] = 'school_manage_bookings.png';
$menu[$i]['href'] = site_url('bookings');

$i++;
$menu[$i]['text'] = 'My Profile';
$menu[$i]['icon'] = ($this->userauth->is_level(ADMINISTRATOR)) ? 'user_administrator.png' : 'user_teacher.png';
$menu[$i]['href'] = site_url('profile');

$i++;
$menu[$i]['text'] = '';
$menu[$i]['icon'] = 'blank.png';
$menu[$i]['href'] = '';




// Menu items for Administrators

$i = 0;
$school[$i]['text'] = 'School Details';
$school[$i]['icon'] = 'school_manage_details.png';
$school[$i]['href'] = site_url('school');

$i++;
$school[$i]['text'] = 'The School Day';
$school[$i]['icon'] = 'school_manage_times.png';
$school[$i]['href'] = site_url('periods');

$i++;
$school[$i]['text'] = 'Week Cycle';
$school[$i]['icon'] = 'school_manage_weeks.png';
$school[$i]['href'] = site_url('weeks');

$i++;
$school[$i]['text'] = 'Holidays';
$school[$i]['icon'] = 'school_manage_holidays.png';
$school[$i]['href'] = site_url('holidays');

$i++;
$school[$i]['text'] = 'Rooms';
$school[$i]['icon'] = 'school_manage_rooms.png';
$school[$i]['href'] = site_url('rooms');

$i++;
$school[$i]['text'] = 'Departments';
$school[$i]['icon'] = 'school_manage_departments.png';
$school[$i]['href'] = site_url('departments');


$i = 0;

/*
$i++;
$admin[$i]['text'] = 'Reports';
$admin[$i]['icon'] = 'school_manage_reports.png';
$admin[$i]['href'] = site_url('reports');
*/

$i++;
$admin[$i]['text'] = 'Users';
$admin[$i]['icon'] = 'school_manage_users.png';
$admin[$i]['href'] = site_url('users');


$i++;
$admin[$i]['text'] = 'Settings';
$admin[$i]['icon'] = 'school_manage_settings.png';
$admin[$i]['href'] = site_url('settings/general');

$i++;
$admin[$i]['text'] = 'Authentication';
$admin[$i]['icon'] = 'lock.png';
$admin[$i]['href'] = site_url('settings/authentication/ldap');




// Start echoing the admin menu
$i = 0;


// Print Normal menu
dotable($menu);



// Check if user is admin
if ($this->userauth->is_level(ADMINISTRATOR)) {
	echo '<h2>School-related</h2>';
	dotable($school);
	echo '<h2>Management</h2>';
	dotable($admin);
}




function dotable($array){

	echo '<table width="100%" cellpadding="0" cellspacing="0" border="0">';
	echo '<tbody>';
	$row = 0;

	foreach($array as $link){
		if($row == 0){ echo '<tr>'; }
		echo '<td width="33%">';
		echo '<h5 style="margin:14px 0px">';
		echo '<a href="'.$link['href'].'">';
		echo '<img src="' . base_url('assets/images/ui/'.$link['icon']) . '" alt="'.$link['text'].'" hspace="4" align="top" width="16" height="16" />';
		echo $link['text'];
		echo '</a>';
		echo '</h5>';
		echo '</td>';
		echo "\n";
		if($row == 2){ echo '</tr>'."\n\n"; $row = -1; }
		$row++;
	}

	echo '</tbody>';
	echo '</table>'."\n\n";
}
?>
