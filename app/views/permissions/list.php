<div class="alpha three columns"><h6>Booking options</h6></div>

<div class="omega nine columns">
	
	<label for="p_<?php echo $id ?>_quota_concurrent">Total concurrent bookings</label>
	<?php
	unset($input);
	$input['name'] = 'permissions[quota_concurrent]';
	$input['id'] = sprintf('p_%s_quota_concurrent', $id);
	$input['size'] = '5';
	$input['maxlength'] = '5';
	$input['autocomplete'] = 'off';
	$input['value'] = @set_value('permissions[quota_concurrent]', $permission_values['quota_concurrent']);
	echo form_input($input);
	?>
	
	<label for="p_<?php echo $id ?>_quota_weekly">Weekly booking quota</label>
	<?php
	unset($input);
	$input['name'] = 'permissions[quota_weekly]';
	$input['id'] = sprintf('p_%s_quota_weekly', $id);
	$input['size'] = '5';
	$input['maxlength'] = '5';
	$input['autocomplete'] = 'off';
	$input['class'] = 'remove-bottom';
	$input['value'] = @set_value('permissions[quota_weekly]', $permission_values['quota_weekly']);
	echo form_input($input);
	?>
	<p class="hint add-bottom">Up to this amount of bookings can be made every week.</p>
	
	<label for="p_booking_advance">Bookings must be made this amount of days in advance</label>
	<?php
	unset($input);
	$input['name'] = 'permissions[booking_advance]';
	$input['id'] = sprintf('p_%s_booking_advance', $id);
	$input['size'] = '5';
	$input['maxlength'] = '5';
	$input['autocomplete'] = 'off';
	$input['value'] = @set_value('permissions[booking_advance]', $permission_values['booking_advance']);
	echo form_input($input);
	?>
	
	<label for="p_<?php echo $id ?>_booking_future">Bookings cannot be made beyond this amount of days in the future</label>
	<?php
	unset($input);
	$input['name'] = 'permissions[booking_future]';
	$input['id'] = sprintf('p_%s_booking_future', $id);
	$input['size'] = '5';
	$input['maxlength'] = '5';
	$input['autocomplete'] = 'off';
	$input['value'] = @set_value('permissions[booking_future]', $permission_values['booking_future']);
	echo form_input($input);
	?>
	
</div>


<hr>

<?php
unset($checks);
$checks['values'] = $permission_values;
$checks['options'] = $available_permissions['general'];
$checks['category'] = 'General';
$this->load->view('permissions/list-checks', $checks);

unset($checks);
$checks['values'] = $permission_values;
$checks['options'] = $available_permissions['bookings'];
$checks['category'] = 'Bookings';
$this->load->view('permissions/list-checks', $checks);

unset($checks);
$checks['values'] = $permission_values;
$checks['options'] = $available_permissions['rooms'];
$checks['category'] = 'Rooms';
$this->load->view('permissions/list-checks', $checks);

unset($checks);
$checks['values'] = $permission_values;
$checks['options'] = $available_permissions['periods'];
$checks['category'] = 'Periods';
$this->load->view('permissions/list-checks', $checks);

unset($checks);
$checks['values'] = $permission_values;
$checks['options'] = $available_permissions['academic'];
$checks['category'] = 'Academic';
$this->load->view('permissions/list-checks', $checks);

unset($checks);
$checks['values'] = $permission_values;
$checks['options'] = $available_permissions['weeks'];
$checks['category'] = 'Timetable weeks';
$this->load->view('permissions/list-checks', $checks);

unset($checks);
$checks['values'] = $permission_values;
$checks['options'] = $available_permissions['holidays'];
$checks['category'] = 'Holidays';
$this->load->view('permissions/list-checks', $checks);

unset($checks);
$checks['values'] = $permission_values;
$checks['options'] = $available_permissions['terms'];
$checks['category'] = 'Term dates';
$this->load->view('permissions/list-checks', $checks);

unset($checks);
$checks['values'] = $permission_values;
$checks['options'] = $available_permissions['departments'];
$checks['category'] = 'Departments';
$this->load->view('permissions/list-checks', $checks);

unset($checks);
$checks['values'] = $permission_values;
$checks['options'] = $available_permissions['reports'];
$checks['category'] = 'Reporting';
$this->load->view('permissions/list-checks', $checks);

unset($checks);
$checks['values'] = $permission_values;
$checks['options'] = $available_permissions['users'];
$checks['category'] = 'Users';
$this->load->view('permissions/list-checks', $checks);

unset($checks);
$checks['values'] = $permission_values;
$checks['options'] = $available_permissions['groups'];
$checks['category'] = 'Groups';
$this->load->view('permissions/list-checks', $checks);
?>