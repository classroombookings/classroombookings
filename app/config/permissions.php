<?php

$permissions['general'][] = array('dashboard', 'Dashboard', 'View dashboard');
$permissions['general'][] = array('dashboard.viewdept', 'Dashboard  - view department bookings');
$permissions['general'][] = array('dashboard.viewown', 'Dashboard - view own bookings');
$permissions['general'][] = array('myprofile', 'My Profile/Change password');
$permissions['general'][] = array('configure', 'Configure Classroombookings');
$permissions['general'][] = array('changeyear', 'Change the working academic year', 'Allow users to change academic settings and make bookings in other academic years');
$permissions['general'][] = array('allrooms', 'Exempt from individual room permissions');

$permissions['bookings'][] = array('bookings', 'View bookings page', 'View the main bookings page');
$permissions['bookings'][] = array('bookings.create.one', 'Create their own one-time bookings', 'Booking days ahead and quota options still apply');
$permissions['bookings'][] = array('bookings.create.recur', 'Create recurring bookings', 'Bookings that occur on every timetabled week');
$permissions['bookings'][] = array('bookings.delete.one.own', 'Delete their own one-time bookings');
$permissions['bookings'][] = array('bookings.delete.one.roomowner', 'Delete room one-time bookings if room owner');
$permissions['bookings'][] = array('bookings.delete.recur.roomowner', 'Delete room recurring bookings if room owner', 'Allow room owners to delete others\' recurring bookings in their room');
/* $permissions['bookings'][] = array('bookings.overwrite.one', 'Can overwrite other one-time bookings');
$permissions['bookings'][] = array('bookings.overwrite.recur', 'Can overwrite other recurring bookings with one-time booking');
$permissions['bookings'][] = array('bookings.overwrite.one.roomowner', 'Can overwrite other one-time bookings if room owner');
$permissions['bookings'][] = array('bookings.overwrite.recur.roomowner', 'Can overwrite other recurring bookings if room owner'); */

$permissions['rooms'][] = array('rooms', 'Rooms', 'Has access to the Rooms page');
$permissions['rooms'][] = array('rooms.add', 'Add a room');
$permissions['rooms'][] = array('rooms.edit', 'Edit a room');
$permissions['rooms'][] = array('rooms.delete', 'Delete a room');
$permissions['rooms'][] = array('rooms.attrs', 'Add and edit attributes');
$permissions['rooms'][] = array('rooms.attrs.values', 'Set room attribute values');
$permissions['rooms'][] = array('rooms.permissions', 'Change room permissions');

$permissions['periods'][] = array('periods', 'Periods', 'Has access to the periods page');
$permissions['periods'][] = array('periods.add', 'Add a period');
$permissions['periods'][] = array('periods.edit', 'Edit periods/change times');
$permissions['periods'][] = array('periods.delete', 'Delete a period');

$permissions['academic'][] = array('academic', 'Academic setup', 'Has access to the main academic setup page');
$permissions['academic'][] = array('years', 'Academic years page');
$permissions['academic'][] = array('years.add', 'Add an academic year');
$permissions['academic'][] = array('years.edit', 'Edit academic years');
$permissions['academic'][] = array('years.delete', 'Delete an academic year');

$permissions['weeks'][] = array('weeks', 'Weeks', 'Has access to the Weeks page');
$permissions['weeks'][] = array('weeks.add', 'Add a timetable week');
$permissions['weeks'][] = array('weeks.edit', 'Edit weeks and set dates');
$permissions['weeks'][] = array('weeks.delete', 'Delete a week');
$permissions['weeks'][] = array('weeks.ayears.manage', 'Manage the academic year dates');
$permissions['weeks'][] = array('weeks.ayears.set', 'Set the current academic year');

$permissions['holidays'][] = array('holidays', 'Holidays');
$permissions['holidays'][] = array('holidays.add', 'Add a holiday');
$permissions['holidays'][] = array('holidays.edit', 'Edit school holidays');
$permissions['holidays'][] = array('holidays.delete', 'Delete a holiday');

$permissions['terms'][] = array('terms', 'Term dates');
$permissions['terms'][] = array('terms.add', 'Add a term date');
$permissions['terms'][] = array('terms.edit', 'Edit term dates');
$permissions['terms'][] = array('terms.delete', 'Delete terms');

$permissions['departments'][] = array('departments', 'Departments', 'Has access to the Departments page');
$permissions['departments'][] = array('departments.add', 'Add a department');
$permissions['departments'][] = array('departments.edit', 'Edit departments');
$permissions['departments'][] = array('departments.delete', 'Delete a department');

$permissions['reports'][] = array('reports', 'Reports');
$permissions['reports'][] = array('reports.owndepartment', 'View reports for their own deparment');
$permissions['reports'][] = array('reports.alldepartments', 'View reports on all departments');
$permissions['reports'][] = array('reports.ownroom', 'View reports for their own room');
$permissions['reports'][] = array('reports.allrooms', 'View reports on all rooms');
$permissions['reports'][] = array('reports.other', 'View other reports');

$permissions['users'][] = array('users', 'Users');
$permissions['users'][] = array('users.add', 'Add a user');
$permissions['users'][] = array('users.edit', 'Edit users');
$permissions['users'][] = array('users.delete', 'Delete a user');
$permissions['users'][] = array('users.import', 'Import users');

$permissions['groups'][] = array('groups', 'Groups');
$permissions['groups'][] = array('groups.add', 'Add a group');
$permissions['groups'][] = array('groups.edit', 'Edit groups');
$permissions['groups'][] = array('groups.delete', 'Delete a group');

$config['permissions'] = $permissions;

?>