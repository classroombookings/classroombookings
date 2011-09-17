<?php
$errors = validation_errors();
if ($errors)
{
	echo $this->msg->err('<ul>' . $errors . '</ul>', 'Please check the following invalid item(s) and try again.');
	echo '<br>';
}

if ($lasterr){ echo $lasterr . '<br class="clear">'; }

echo form_open_multipart('users/import/1', null, array('step' => 1));

// Start tabindex
$t = 1;
?>



<div class="alpha three columns"><h6>Upload CSV file</h6></div>

<div class="omega nine columns">

	<?php
	unset($input);
	$input['accesskey'] = 'F';
	$input['name'] = 'userfile';
	$input['id'] = 'userfile';
	$input['size'] = '40';
	$input['maxlength'] = '1024';
	$input['tabindex'] = $t;
	$input['autocomplete'] = 'off';
	echo form_upload($input);
	$t++;
	?>

</div>


<hr>


<div class="alpha three columns"><h6>Default values</h6></div>

<div class="omega nine columns">
	
	<label for="default_password">Password</label>
	<?php
	unset($input);
	$input['accesskey'] = 'P';
	$input['name'] = 'default_password';
	$input['id'] = 'default_password';
	$input['size'] = '30';
	$input['maxlength'] = '104';
	$input['tabindex'] = $t;
	$input['autocomplete'] = 'off';
	echo form_input($input);
	$t++;
	?>
	
	<label for="default_group_id">Group</label>
	<?php
	echo form_dropdown('default_group_id', $groups, set_value('default_group_id', 0), 'tabindex="'.$t.'"');
	$t++;
	?>
	
	<?php if (isset($departments) && $departments != null) : ?>
	<label for="default_departments">Departments</label>
	<select name="default_departments[]" id="default_departments" size="10" multiple="multiple" tabindex="<?php echo $t ?>">
	<?php
	foreach($departments as $did => $dname){
		echo '<option value="' . $did . '">' . $dname . '</option>';
	}
	$t++;
	?>
	</select>
	<?php endif; ?>
	
	<label for="default_emaildomain">Email domain</label>
	<?php
	unset($input);
	$input['accesskey'] = 'E';
	$input['name'] = 'default_emaildomain';
	$input['id'] = 'default_emaildomain';
	$input['size'] = '40';
	$input['maxlength'] = '100';
	$input['tabindex'] = $t;
	$input['autocomplete'] = 'off';
	$input['value'] = set_value('default_emaildomain');
	echo form_input($input);
	$t++;
	?>
	
	<fieldset>
		<label for="default_enabled">
			<?php
			echo form_hidden('default_enabled', '0');
			unset($input);
			$input['name'] = 'default_enabled';
			$input['id'] = 'default_enabled';
			$input['value'] = '1';
			$input['checked'] = set_checkbox($input['name'], $input['value'], true);
			$input['tabindex'] = $t;
			echo form_checkbox($input);
			$t++;
			?>
			<span>Enable account</span>
		</label>
	</fieldset>

</div>


<hr>


<div class="alpha three columns">&nbsp;</div>
<div class="omega nine columns"><?php
unset($buttons);
$buttons[] = array('submit', 'blue', "Next &rarr;", $t);
$buttons[] = array('link', '', 'Cancel', $t + 1, site_url('users'));
$this->load->view('parts/buttons', array('buttons' => $buttons));
?></div>


</form>