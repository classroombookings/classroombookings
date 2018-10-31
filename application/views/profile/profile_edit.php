<?php
if(!isset($user_id)){ $user_id = $this->session->userdata('user_id'); }

echo form_open('profile/save', array('class' => 'cssform', 'id' => 'profile_edit'), array('user_id' => $user_id) );

$t = 1;
?>


<fieldset><legend accesskey="U" tabindex="<?php echo $t; $t++; ?>">User Information</legend>


<p>
  <label for="email" class="required">Email address</label>
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


<p>
  <label for="password1">Password</label>
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
  <label for="password2">Password (again)</label>
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
$submit['cancel'] = array('Cancel', $t+1, 'profile');
$this->load->view('partials/submit', $submit);
echo form_close();
?>
