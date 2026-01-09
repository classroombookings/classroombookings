<?php

$lang['session.sessions'] = 'Sessions';
$lang['session.all_sessions'] = 'All sessions';
$lang['session.weeks'] = 'Weeks';
$lang['session.update'] = 'Update';
$lang['session.schedules'] = 'Room schedules';
$lang['session.holidays'] = 'Holidays';
$lang['session.session'] = 'Session';
$lang['session.add.action'] = 'Create session';
$lang['session.add.title'] = 'Create session';
$lang['session.edit.title'] = 'Update session';
$lang['session.no_items'] = 'No sessions found.';

$lang['session.error.no_timetable_weeks'] = 'Please add at least one Timetable Week.';
$lang['session.error.no_week_selected'] = 'No week selected.';

$lang['session.field.name'] = 'Name';
$lang['session.field.is_selectable'] = 'User-selectable';
$lang['session.field.is_selectable.hint'] = "Allow users to view and make bookings in this session even if it isn't the current one.";
$lang['session.field.default_schedule_id'] = 'Default schedule';
$lang['session.field.date_start'] = 'Start date';
$lang['session.field.date_end'] = 'End date';
$lang['session.field.available'] = 'Available';
$lang['session.field.is_current'] = 'Current';

$lang['session.create.success'] = "Session %s has been created.";
$lang['session.create.error'] = 'There was an error creating the session.';
$lang['session.update.success'] = 'Session %s has been updated.';
$lang['session.update.error'] = 'There was an error updating the session.';
$lang['session.delete.success'] = 'Session %s has been deleted.';
$lang['session.delete.title'] = 'Delete Session: %s';

$lang['session.save_weeks.success'] = "The session weeks have been updated.";
$lang['session.save_weeks.error'] = "There was an error updating the session weeks.";
$lang['session.bulk_week.success'] = "%s has been applied to every week in the session.";
$lang['session.bulk_week.no_week_selected'] = "No week selected.";

$lang['session.delete.warning'] = 'All bookings and holidays for this session will be permanently deleted as well.';

$lang['session.validation.date_check'] = 'The {field} (%s) is already part of an existing session (%s).';

$lang['session.list.current_and_future'] = 'Current and future sessions';
$lang['session.list.past'] = 'Past sessions';


$lang['session.help.session.text'] = "Each session typically lasts for the whole school year. Set the start and end dates of the session here.";


$lang['session.help.date_format'] = "Date format";
$lang['session.help.date_format.text'] = "Use the DD/MM/YYYY format when entering dates. For example, 16/04/2026.";

$lang['session.help.changing_dates'] = 'Changing dates';
$lang['session.help.changing_dates.text'] = "If you change the start or end date after bookings have been made during the session, existing bookings <em>outside the new date range</em> will be deleted.";

$lang['session.help.default_schedule'] = 'Default schedule';
$lang['session.help.default_schedule.text'] = "Each room group in the session can run on a different schedule. Specify a default schedule here that will be applied to existing room groups or will be applied for any new room groups you create. You can change each group's schedule later.";


$lang['session.weeks.intro'] = 'Click on the dates in each calendar to toggle the Timetable Week for that week.';


$lang['session.room_schedules'] = 'Room schedules';
$lang['session.room_schedules.no_groups'] = 'No room groups found.';

$lang['session.room_schedules.help.intro'] = "Specify the schedule that should be used by each Room group for this session. The chosen schedule for each group will apply to all rooms within it.";
$lang['session.room_schedules.help.change_warning'] = "If you change the schedule for a room group, bookings on the old schedule will no longer be accessible.";
