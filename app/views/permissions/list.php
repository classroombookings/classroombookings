<div class="alpha three columns"><h6>Booking options</h6></div>

<div class="omega nine columns">
	
	<label for="username">Total concurrent bookings</label>
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
?>