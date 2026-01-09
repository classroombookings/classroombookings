<?php

$lang['booking.bookings'] = 'Bookings';
$lang['booking.booking'] = 'Booking';
$lang['booking.bookings_in_series'] = 'Bookings in recurring series';
$lang['booking.details'] = 'Booking details';
$lang['booking.and_others'] = 'and others';		// for editing series: 'Date Month Year (Timetable Week) (**and others**)'

$lang['booking.slot'] = 'Slot';
$lang['booking.start'] = 'Start';
$lang['booking.end'] = 'End';
$lang['booking.book'] = 'Book';
$lang['booking.do_not_book'] = 'Do not book';

$lang['booking.booking_status'] = 'Booking status';
$lang['booking.active_bookings'] = 'Active bookings';
$lang['booking.in_my_rooms'] = 'Bookings in my rooms';
$lang['booking.existing_booking'] = 'Existing booking';
$lang['booking.recurs'] = 'Recurs';
$lang['booking.recur_start'] = 'Recurring start';
$lang['booking.recur_end'] = 'Recurring end';
$lang['booking.date_start'] = 'Start date';
$lang['booking.date_end'] = 'End date';
$lang['booking.create_booking'] = 'Create booking';
$lang['booking.create_bookings'] = 'Create bookings';
$lang['booking.create_multiple_bookings'] = 'Create multiple booking';
$lang['booking.no_conflicts'] = 'No conflicts';
$lang['booking.conflict'] = 'Conflict';
$lang['booking.conflicts'] = 'Conflicts';

$lang['booking.action.replace'] = 'Replace existing booking';
$lang['booking.action.keep'] = 'Keep existing booking';

$lang['booking.conflict.one'] = 'There is one booking conflict to review.';
$lang['booking.conflict.multiple'] = 'There are %d booking conflicts to review.';

$lang['booking.add.title'] = 'Create new booking';
$lang['booking.edit.title'] = 'Update booking';
$lang['booking.edit.action'] = 'Update booking';

$lang['booking.add.single.action'] = 'Create one-time booking';
$lang['booking.add.recurring.action'] = 'Create recurring booking';
$lang['booking.recurring.repeat_description'] = 'Every %s on %s';
$lang['booking.recurring.starting_from'] = 'Starting from...';
$lang['booking.recurring.until'] = 'Until...';
$lang['booking.recurring.start_of_session'] = 'Start of session';
$lang['booking.recurring.end_of_session'] = 'End of session';
$lang['booking.recurring.specific_date'] = 'Specific date';
$lang['booking.recurring.preview'] = 'Preview recurring bookings';

$lang['booking.add.multi.single.action'] = 'Create selected one-time bookings';
$lang['booking.add.multi.recurring.action'] = 'Create recurring bookings';


$lang['booking.selection.this_only'] = 'This booking only';
$lang['booking.selection.future'] = 'This and future bookings in series';
$lang['booking.selection.all'] = 'All bookings in series';

$lang['booking.edit.recurring.title'] = 'Update recurring booking';
$lang['booking.edit.single.hint'] = 'The changes you make below will apply to the selected booking only.';
$lang['booking.edit.future.hint'] = 'The changes you make below will apply to the selected booking and all future entries in the series.';
$lang['booking.edit.all.hint'] = 'The changes you make below will apply to all bookings in the series.';

$lang['booking.cancel.recurring.title'] = 'Cancel recurring booking';
$lang['booking.cancel.single.title'] = 'Cancel one-time booking';
$lang['booking.cancel.single.action'] = 'Yes, cancel booking';
$lang['booking.cancel.abort'] = 'No, keep it';

$lang['booking.action.cancel_booking'] = 'Cancel booking';
$lang['booking.action.cancel_bookings'] = 'Cancel bookings';
$lang['booking.series.go_back'] = 'Back to booking details';

$lang['booking.occurs'] = 'Occurs';
$lang['booking.occurs.once'] = 'Once';
$lang['booking.booked_by'] = 'Booked by';
$lang['booking.notes'] = "Notes";

$lang['booking.legend.legend'] = 'Legend';
$lang['booking.legend.free'] = 'Available';
$lang['booking.legend.static'] = 'Recurring booking';
$lang['booking.legend.staff'] = 'One-time booking';

$lang['booking.type_single'] = 'One-time';
$lang['booking.type_recurring'] = 'Recurring';

$lang['booking.warning.not_own'] = 'This is not your own booking.';

$lang['booking.error.not_found'] = 'Could not find requested booking details.';
$lang['booking.error.bad_type'] = 'Invalid booking type.';
$lang['booking.error.bad_form'] = 'Invalid selection.';
$lang['booking.error.not_cancelable'] = 'Booking cannot be cancelled.';
$lang['booking.error.bad_session'] = 'Requested session is not available.';
$lang['booking.error.no_permission_room_date'] = 'You do not have permission to create bookings in this room on this date.';
$lang['booking.error.no_permission_room'] = 'You do not have permission to create bookings of that type in this room.';
$lang['booking.error.no_slots_selected'] = "You did not select any free slots to book.";
$lang['booking.error.multibooking_create_error'] = "Could not create multibooking entry.";
$lang['booking.error.some_invalid_values'] = 'One or more of the bookings contained some invalid values. Please check and try again.';
$lang['booking.error.none_created'] = 'No bookings have been created.';
$lang['booking.error.generic'] = 'Could not create one or more bookings.';
$lang['booking.error.not_created'] = 'Could not create the requested booking.';
$lang['booking.error.must_select_fewer'] = 'Please de-select some bookings to stay within your limit.';
$lang['booking.error.too_many_instances'] = 'You can only create up to %d events in your recurring booking. De-select %d slots to continue.';
$lang['booking.error.no_recurring_dates'] = 'The session does not have any available dates to support recurring bookings.';
$lang['booking.error.invalid_recurring_dates'] = 'The recurring End Date (%s) must be after the Starting From date of %s.';
$lang['booking.error.no_dates'] = 'No dates selected.';

$lang['booking.error.constraint.range_min_only'] = 'One-time bookings must be made at least %d days ahead.';
$lang['booking.error.constraint.range_max_only'] = 'One-time bookings can only be created up to %d days ahead.';
$lang['booking.error.constraint.range_min'] = "One-time bookings must be at least %d days ahead.\nThe earliest booking date is %s.";
$lang['booking.error.constraint.range_max'] = "One-time bookings can only be created up to %d days ahead.\nThe furthest booking date is %s.";
$lang['booking.error.constraint.max_reached'] = 'You have reached the maximum number of active bookings (%d). Wait until your next booking has taken place or cancel a future booking.';
$lang['booking.error.period_wrong_day'] = '%s not available on %s.';
$lang['booking.error.date_not_in_range'] = "This date isn't within the allowed date range or is in the past.";

$lang['booking.success.created'] = 'The booking has been created.';
$lang['booking.success.created.multiple'] = 'The bookings have been created.';
$lang['booking.success.some_created'] = '%d bookings have been created.';
$lang['booking.success.recurring.some_created'] = '%d recurring bookings have been created successfully.';

$lang['booking.warning.permitted_limit'] = 'The maximum number of bookings you can create is %d.';
$lang['booking.warning.permitted_limit_with_active'] = 'The maximum number of bookings you can create is %d. Your active booking limit is %d and you have %d active bookings.';

$lang['booking.error.cancelling'] = 'There was an error cancelling the booking.';

$lang['booking.notice.instances_to_create'] = 'This recurring booking will create %d instances.';

$lang['booking.cancel.one.success'] = 'The booking has been cancelled.';
$lang['booking.cancel.future.success'] = 'The selected booking and all future occurrences in the series have been cancelled.';
$lang['booking.cancel.all.success'] = 'All bookings in the recurring series have been cancelled.';
$lang['booking.cancel.invalid_type.error'] = 'Invalid cancellation type.';

$lang['booking.cancel_multi.title'] = 'Cancel multiple bookings';
$lang['booking.cancel_multi.action'] = 'Cancel selected bookings';
$lang['booking.cancel_multi.error.none_selected'] = 'No bookings selected for cancelling.';
$lang['booking.cancel_multi.number_cancelled'] = '%d bookings have been cancelled.';
$lang['booking.cancel_multi.none_cancelled'] = 'No bookings have been cancelled.';


$lang['booking.edit.one.success'] = 'The booking has been updated.';
$lang['booking.edit.future.success'] = 'The booking and all future bookings in the series have been updated.';
$lang['booking.edit.all.success'] = 'All bookings in the series have been updated.';

$lang['booking.edit.error'] = 'Could not update the booking.';


$lang['booking.session.current'] = 'Current and future';
$lang['booking.session.past'] = 'Past';

$lang['booking.nav.back'] = 'Back';
$lang['booking.nav.next'] = 'Next';
$lang['booking.nav.week_prev'] = 'Previous week';
$lang['booking.nav.week_next'] = 'Next week';
$lang['booking.nav.week_commencing'] = 'Week commencing %s';

$lang['booking.slot.unavailable_period'] = 'Period not available';
$lang['booking.toggle_multi_select'] = 'Toggle multi-select';

$lang['booking.status.booked'] = 'Booked';
$lang['booking.status.cancelled'] = 'Cancelled';
