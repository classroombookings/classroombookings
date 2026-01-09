<?php

$lang['exception.DateException.invalidDate'] = "No date selected or date is not valid (%s).";
$lang['exception.DateException.forSessionRange'] = "The selected date (%s), is not within the current Session.";


$lang['exception.AgentException.forInvalidType'] = "Unrecognised booking type. Should be one of: %s";
$lang['exception.AgentException.forNoSession'] = 'Requested date does not belong to a session.';
$lang['exception.AgentException.forNoPeriod'] = 'Requested period could not be found.';
$lang['exception.AgentException.forNoRoom'] = 'Requested room could not be found or is not bookable.';
$lang['exception.AgentException.forInvalidDate'] = 'Requested date is not recognised or is not bookable.';
$lang['exception.AgentException.forNoWeek'] = 'Requested date is not associated with a timetable week.';
$lang['exception.AgentException.forNoBooking'] = 'Requested booking could not be found.';
$lang['exception.AgentException.forAccessDenied'] = 'You do not have perimssion to modify the requested booking.';

$lang['exception.AvailabilityException.forNoWeek'] = "The selected date is not assigned to a timetable week.";
$lang['exception.AvailabilityException.forNoPeriods'] = "There are no periods available for the selected date.";
$lang['exception.AvailabilityException.forHoliday.unknown'] = 'The date you selected is during a holiday.';
$lang['exception.AvailabilityException.forHoliday'] = 'The date you selected is during a holiday: %s: %s - %s';


$lang['exception.BookingValidationException.forExistingBooking'] = "Another booking already exists.";
$lang['exception.BookingValidationException.forHoliday'] = "Booking cannot be created on a holiday.";

$lang['exception.SessionException.notSelected'] = "No active Session found.";


$lang['exception.SettingsException.forDisplayType'] = "The 'Display Type' setting has not been set.";
$lang['exception.SettingsException.forColumns'] = "The 'Display Columns' setting has not been set.";
$lang['exception.SettingsException.forNoRooms'] = "There are no rooms available.";
$lang['exception.SettingsException.forNoSchedule'] = "This room group doesn't have a Schedule configured for this session.";
