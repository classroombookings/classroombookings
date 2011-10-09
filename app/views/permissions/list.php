<div class="alpha three columns"><h6>Booking options</h6></div>

<div class="omega nine columns">
	
	<label for="p_quota_concurrent">Total concurrent bookings</label>
	<?php
	unset($input);
	$input['name'] = 'permissions[quota_concurrent]';
	$input['id'] = 'p_quota_concurrent';
	$input['size'] = '5';
	$input['maxlength'] = '5';
	$input['autocomplete'] = 'off';
	//$input['value'] = @set_value('quota', $user->quota);
	echo form_input($input);
	?>
	
	<label for="p_quota_weekly">Weekly booking quota</label>
	<?php
	unset($input);
	$input['name'] = 'permissions[quota_weekly]';
	$input['id'] = 'p_quota_weekly';
	$input['size'] = '5';
	$input['maxlength'] = '5';
	$input['autocomplete'] = 'off';
	$input['class'] = 'remove-bottom';
	//$input['value'] = @set_value('quota', $user->quota);
	echo form_input($input);
	?>
	<p class="hint">Up to this amount of bookings can be made every week.</p>
	
	<label for="p_booking_advance">Bookings must be made this amount of days in advance</label>
	<?php
	unset($input);
	$input['name'] = 'permissions[booking_advance]';
	$input['id'] = 'p_booking_advance';
	$input['size'] = '5';
	$input['maxlength'] = '5';
	$input['autocomplete'] = 'off';
	//$input['value'] = @set_value('quota', $user->quota);
	echo form_input($input);
	?>
	
	<label for="p_booking_future">Bookings cannot be made beyond this amount of days in the future</label>
	<?php
	unset($input);
	$input['name'] = 'permissions[booking_future]';
	$input['id'] = 'p_booking_future';
	$input['size'] = '5';
	$input['maxlength'] = '5';
	$input['autocomplete'] = 'off';
	//$input['value'] = @set_value('quota', $user->quota);
	echo form_input($input);
	?>
	
</div>


<hr>

<?php
unset($checks);
$checks['options'] = $permission_list['general'];
$checks['category'] = 'General';
$this->load->view('permissions/list-checks', $checks);

unset($checks);
$checks['options'] = $permission_list['bookings'];
$checks['category'] = 'Bookings';
$this->load->view('permissions/list-checks', $checks);

unset($checks);
$checks['options'] = $permission_list['rooms'];
$checks['category'] = 'Rooms';
$this->load->view('permissions/list-checks', $checks);

unset($checks);
$checks['options'] = $permission_list['periods'];
$checks['category'] = 'Periods';
$this->load->view('permissions/list-checks', $checks);

unset($checks);
$checks['options'] = $permission_list['academic'];
$checks['category'] = 'Academic';
$this->load->view('permissions/list-checks', $checks);

unset($checks);
$checks['options'] = $permission_list['weeks'];
$checks['category'] = 'Timetable weeks';
$this->load->view('permissions/list-checks', $checks);

unset($checks);
$checks['options'] = $permission_list['holidays'];
$checks['category'] = 'Holidays';
$this->load->view('permissions/list-checks', $checks);

unset($checks);
$checks['options'] = $permission_list['terms'];
$checks['category'] = 'Term dates';
$this->load->view('permissions/list-checks', $checks);

unset($checks);
$checks['options'] = $permission_list['departments'];
$checks['category'] = 'Departments';
$this->load->view('permissions/list-checks', $checks);

unset($checks);
$checks['options'] = $permission_list['reports'];
$checks['category'] = 'Reporting';
$this->load->view('permissions/list-checks', $checks);

unset($checks);
$checks['options'] = $permission_list['users'];
$checks['category'] = 'Users';
$this->load->view('permissions/list-checks', $checks);

unset($checks);
$checks['options'] = $permission_list['groups'];
$checks['category'] = 'Groups';
$this->load->view('permissions/list-checks', $checks);
?>