<?php
if( !isset($user_id) ){
	$user_id = @field($this->uri->segment(3, NULL), $this->validation->user_id, 'X');
}
$errorstr = $this->validation->error_string;

echo form_open('users/save', array('class' => 'cssform', 'id' => 'user_add'), array('user_id' => $user_id) );
?>


<fieldset><legend accesskey="U" tabindex="1">User Information</legend>


<p>
  <label for="username" class="required">Username</label>
  <?php
  $t = 2;
	$username = @field($this->validation->username, $user->username);
	echo form_input(array(
		'name' => 'username',
		'id' => 'username',
		'size' => '20',
		'maxlength' => '20',
		'tabindex' => $t,
		'value' => $username,
	));
	$t++;
	?>
</p>
<?php echo @field($this->validation->username_error) ?>


<p>
  <label for="password1" class="required">Password</label>
  <?php
	#$password1 = @field($this->validation->email, $user->email);
	echo form_password(array(
		'name' => 'password1',
		'id' => 'password1',
		'size' => '20',
		'maxlength' => '40',
		'tabindex' => $t,
		'value' => '',
	));
	$t++;
	?>
</p>
<?php echo @field($this->validation->password1_error) ?>


<p>
  <label for="password2" class="required">Password (again)</label>
  <?php
	#$password1 = @field($this->validation->email, $user->email);
	echo form_password(array(
		'name' => 'password2',
		'id' => 'password2',
		'size' => '20',
		'maxlength' => '40',
		'tabindex' => $t,
		'value' => '',
	));
	$t++;
	?>
</p>
<?php echo @field($this->validation->password2_error) ?>


<p>
  <label for="authlevel" class="required">Type</label>
  <?php
	$authlevel = @field($this->validation->authlevel, $user->authlevel);
	$data = array('1' => 'Administrator', '2' => 'Teacher');
	echo form_dropdown(
		'authlevel',
		$data,
		$authlevel,
		' id="authlevel" tabindex="'.$t.'"'
	);
	$t++;
	?>
</p>
<?php echo @field($this->validation->type_error) ?>


<p>
  <label for="enabled">Enabled</label>
  <?php
	$enabled = @field($this->validation->enabled, $user->enabled);
	echo form_checkbox(array( 
		'name' => 'enabled',
		'id' => 'enabled',
		'value' => '1',
		'tabindex' => $t,
		'checked' => $enabled,
	));
	$t++;
	?>
</p>


<!--
<p>
	<label for="bquota">Booking quota</label>
  <?php
	/* $bquota = @field($this->validation->bquota, $user->bquota);
	echo form_input(array(
		'name' => 'bquota',
		'id' => 'bquota',
		'size' => '5',
		'maxlength' => '3',
		'tabindex' => $t,
		'value' => $bquota,
	));
	$t+ */;
	?>
	<p class="hint">Number of bookings this user is allowed to make. Enter 0 for unlimited, or leave empty to follow the quota defined for the school.</p>
</p> -->
<?php # echo @field($this->validation->bquota_error) ?>


<p>
  <label for="email">Email address</label>
  <?php
	$email = @field($this->validation->email, $user->email);
	echo form_input(array(
		'name' => 'email',
		'id' => 'email',
		'size' => '35',
		'maxlength' => '255',
		'tabindex' => $t,
		'value' => $email,
	));
	$t++;
	?>
</p>
<?php echo @field($this->validation->email_error) ?>


</fieldset>




<fieldset>


<p>
  <label for="firstname">First name</label>
  <?php
	$firstname = @field($this->validation->firstname, $user->firstname);
	echo form_input(array(
		'name' => 'firstname',
		'id' => 'firstname',
		'size' => '20',
		'maxlength' => '20',
		'tabindex' => $t,
		'value' => $firstname,
	));
	$t++;
	?>
</p>
<?php echo @field($this->validation->firstname_error) ?>


<p>
  <label for="lastname">Last name</label>
  <?php
	$lastname = @field($this->validation->lastname, $user->lastname);
	echo form_input(array(
		'name' => 'lastname',
		'id' => 'lastname',
		'size' => '20',
		'maxlength' => '20',
		'tabindex' => $t,
		'value' => $lastname,
	));
	$t++;
	?>
</p>
<?php echo @field($this->validation->lastname_error) ?>


<p>
  <label for="displayname">Display name</label>
  <?php
	$displayname = @field($this->validation->displayname, $user->displayname);
	echo form_input(array(
		'name' => 'displayname',
		'id' => 'displayname',
		'size' => '20',
		'maxlength' => '20',
		'tabindex' => $t,
		'value' => $displayname,
	));
	$t++;
	?>
</p>
<?php echo @field($this->validation->displayname_error) ?>


<p>
	<label for="department">Department</label>
  <?php
	$departmentlist['0'] = '(None)';
	if($departments){
  	foreach($departments as $department){
	  	$departmentlist[$department->department_id] = $department->name;		#@field($user->displayname, $user->username);
  	}
  }
	$department_id = @field($this->validation->department_id, $user->department_id, '0');
	echo form_dropdown('department_id', $departmentlist, $department_id, 'tabindex="'.$t.'"');
	$t++;
	?>
</p>
<?php echo @field($this->validation->department_id_error) ?>


<p>
  <label for="ext">Extension</label>
  <?php
	$ext = @field($this->validation->ext, $user->ext);
	echo form_input(array(
		'name' => 'ext',
		'id' => 'ext',
		'size' => '10',
		'maxlength' => '10',
		'tabindex' => $t,
		'value' => $ext,
	));
	$t++;
	?>
</p>
<?php echo @field($this->validation->ext_error) ?>


</fieldset>


<?php
$submit['submit'] = array('Save', $t);
$submit['cancel'] = array('Cancel', $t+1, 'users');
$this->load->view('partials/submit', $submit);
echo form_close();
?>
