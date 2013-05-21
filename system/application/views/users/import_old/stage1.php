<?php
if( !isset($stage) ){
	$stage = @field($this->uri->segment(3, NULL), $this->validation->stage, '1');
}
$errorstr = $this->validation->error_string;

echo form_open_multipart('users/import', array('class' => 'cssform', 'id' => 'user_import'), $post );
?>


<fieldset><legend accesskey="I" tabindex="1">Import Source</legend>


<p>
  <label for="userfile" class="required">File</label>
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


<p>
  <label for="format" class="required">Format</label>
  <?php
	$format = @field($this->validation->format, $_POST['format']);
	$data = array('csv' => 'CSV', 'xml' => 'XML');
	echo form_dropdown(
		'format',
		$data,
		'csv',
		' id="format" tabindex="'.$t.'"'
	);
	$t++;
	?>
</p>
<?php echo @field($this->validation->format_error) ?>


<p>
  <label for="existing">Existing users</label>
  <?php
	$existing = @field($this->validation->existing, $_POST['existing']);
	$data = array('overwrite' => 'Overwrite', 'skip' => 'Skip/ignore');
	echo form_dropdown(
		'existing',
		$data,
		'overwrite',
		' id="existing" tabindex="'.$t.'"'
	);
	$t++;
	?>
	<p class="hint">What to do when a username already exists.</p>
</p>


<p>
  <label for="csvcols">CSV column headings</label>
  <?php
	$csvcols = @field($this->validation->csvcols, $_POST['csvcols']);
	echo form_checkbox(array( 
		'name' => 'csvcols',
		'id' => 'csvcols',
		'value' => "1",
		'tabindex' => $t,
		'checked' => false,	//$csvcols,
	));
	$t++;
	?>
	<p class="hint">Tick this box if the CSV file already contains column headings.</p>
</p>


<p>
  <label for="skiperrors">Skip errors</label>
  <?php
	$skiperrors = @field($this->validation->skiperrors, $_POST['skiperrors']);
	echo form_checkbox(array( 
		'name' => 'skiperrors',
		'id' => 'skiperrors',
		'value' => "1",
		'tabindex' => $t,
		'checked' => false,	//$skiperrors,
	));
	$t++;
	?>
	<p class="hint">Continue importing users if an error occurs; but still show summary when finished.</p>
</p>


</fieldset>





<fieldset><legend accesskey="D">Default values</legend>

<div>Enter the default values that will be applied to all users if not specified in the import file.</div>

<p>
  <label for="password">password</label>
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

<p>
	<label for="department">Department</label>
  <?php
	$departmentlist['0'] = '(None)';
  foreach($departments as $department){
  	$departmentlist[$department->department_id] = $department->name;		#@field($user->displayname, $user->username);
  }
	$department_id = @field($this->validation->department_id, $user->department_id, '0');
	echo form_dropdown('department_id', $departmentlist, $department_id, 'tabindex="'.$t.'"');
	$t++;
	?>
</p>
<?php echo @field($this->validation->department_id_error) ?>

</fieldset>





<?php $this->load->view('users/import/buttons', array('stage' => $stage, 'stage_config' => $stage_config, 't' => $t)) ?>

</form>
