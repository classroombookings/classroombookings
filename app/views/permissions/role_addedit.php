<?php
$errors = validation_errors();
if ($errors)
{
	echo '<div class="row">';
	echo $this->msg->err('<ul class="square">' . $errors . '</ul>', 'Please check the following invalid item(s) and try again.');
	echo '</div>';
}


echo form_open('permissions/save_role', null, array('role_id' => $role_id));


// Start tabindex
$t = 1;
?>



<div class="alpha three columns"><h6>Details</h6></div>

<div class="omega nine columns">

	<label for="name">Name</label>
	<?php
	unset($input);
	$input['accesskey'] = 'N';
	$input['name'] = 'name';
	$input['id'] = 'name';
	$input['size'] = '30';
	$input['maxlength'] = '20';
	$input['tabindex'] = $t;
	$input['autocomplete'] = 'off';
	$input['value'] = @set_value($input['name'], $role->name);
	echo form_input($input);
	$t++;
	?>
	
	<label for="description">Description</label>
	<?php
	unset($input);
	$input['accesskey'] = 'D';
	$input['name'] = 'description';
	$input['id'] = 'description';
	$input['cols'] = '50';
	$input['rows'] = '4';
	$input['maxlength'] = '255';
	$input['tabindex'] = $t;
	$input['autocomplete'] = 'off';
	$input['value'] = @set_value($input['name'], $role->description);
	echo form_textarea($input);
	$t++;
	?>

</div>

<hr>


<div class="alpha three columns">&nbsp;</div>
<div class="omega nine columns"><?php
$text = ($role_id == null) ? 'Add' : 'Save';
unset($buttons);
$buttons[] = array('submit', 'blue', "$text role", $t);
$this->load->view('parts/buttons', array('buttons' => $buttons));
?></div>


</form>