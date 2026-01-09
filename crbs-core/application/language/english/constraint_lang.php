<?php

$lang['constraint.constraints'] = 'Constraints';

$lang['constraint.user.hint.1'] = 'Booking constraints lets you control the number of active bookings users can have, the date range that one-time bookings can be created and the number of events in a recurring series.';
$lang['constraint.user.hint.2'] = 'Users can inherit a constraint from their Role or have it configured at the user level.';
$lang['constraint.user.hint.3'] = 'If the user does not have a Role, the inherited value will be the same as if it was not set.';

$lang['constraint.type.R'] = 'Inherit from Role (not configured)';
$lang['constraint.type.X'] = 'Not set';
$lang['constraint.type.U'] = 'Other...';
$lang['constraint.user.inherit_role_hint'] = 'Inherit from Role (%s: %s)';

$lang['constraint.max_active_bookings.short'] = 'Max active bookings';
$lang['constraint.max_active_bookings'] = 'Maximum number of active one-time bookings';
$lang['constraint.max_active_bookings.hint'] = 'Maximum number of active bookings that a user can have at one time.';

$lang['constraint.range_min'] = 'Minimum days notice for one-time bookings';
$lang['constraint.range_min.hint'] = "The minimum number of days required between the day the booking is created and the booking's date.";

$lang['constraint.range_max'] = 'Maximum days ahead for one-time bookings';
$lang['constraint.range_max.hint'] = "The maximum number of days in the future a booking can be.";

$lang['constraint.recur_max_instances'] = 'Maximum number of occurrences for recurring bookings';
$lang['constraint.recur_max_instances.hint'] = 'The maximum number of occurrences that can be created as part of a recurring booking series.';
