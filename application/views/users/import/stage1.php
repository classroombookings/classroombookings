<?php
/* if( !isset($stage) ){
	$stage = @field($this->uri->segment(3, NULL), $this->validation->stage, '1');
} */
#$errorstr = $this->validation->error_string;

echo form_open_multipart('users/import2', array('class' => 'cssform', 'id' => 'user_import') );
?>


<fieldset><legend accesskey="I" tabindex="1">Import Source</legend>


<p>
  <label for="userfile" class="required">CSV File</label>
  <?php
  $t = 2;
	#$photo = @field($this->validation->photo, $room->photo);
	echo form_upload(array(
		'name' => 'userfile',
		'id' => 'userfile',
		'size' => '40',
		'maxlength' => '255',
		'tabindex' => $t,
		'value' => '',
	));
	$t++;
	?>
</p>


</fieldset>




<fieldset><legend accesskey="F">Default values</legend>

<div>Enter the default values that will be applied to all users if not specified in the import file.</div>

<p>
  <label for="password">Password</label>
  <?php
	#$password1 = @field($this->validation->email, $user->email);
	echo form_password(array(
		'name' => 'password',
		'id' => 'password',
		'size' => '20',
		'maxlength' => '40',
		'tabindex' => $t,
		'value' => '',
	));
	$t++;
	?>
</p>

<p>
  <label for="authlevel" class="required">Type</label>
  <?php
	$data = array('1' => 'Administrator', '2' => 'Teacher');
	echo form_dropdown(
		'authlevel',
		$data,
		'2',
		' id="authlevel" tabindex="'.$t.'"'
	);
	$t++;
	?>
</p>
<?php echo @field($this->validation->type_error) ?>


<p>
  <label for="enabled">Enabled</label>
  <?php
	echo form_checkbox(array( 
		'name' => 'enabled',
		'id' => 'enabled',
		'value' => '1',
		'tabindex' => $t,
		'checked' => true,
	));
	$t++;
	?>
</p>


</fieldset>




<?php
#$this->load->view('users/import/buttons', array('stage' => $stage, 'stage_config' => $stage_config, 't' => $t))

$submit['submit'] = array('Import', $t);
$submit['cancel'] = array('Cancel', $t+1, 'users');
$this->load->view('partials/submit', $submit);
echo form_close();
?>
