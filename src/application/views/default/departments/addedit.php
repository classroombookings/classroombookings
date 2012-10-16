<?php
$errors = validation_errors();
if ($errors)
{
	echo '<div class="row">';
	echo $this->msg->err('<ul class="square">' . $errors . '</ul>', 'Please check the following invalid item(s) and try again.');
	echo '</div>';
}


echo form_open('departments/save', null, array('department_id' => $department_id));


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
	$input['maxlength'] = '64';
	$input['tabindex'] = $t;
	$input['autocomplete'] = 'off';
	$input['value'] = @set_value('name', $department->name);
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
	$input['value'] = @set_value($input['name'], $department->description);
	echo form_textarea($input);
	$t++;
	?>
	
	<label for="colour">Colour</label>
	<?php
	unset($input);
	$input['accesskey'] = 'C';
	$input['name'] = 'colour';
	$input['id'] = 'colour';
	$input['size'] = '10';
	$input['maxlength'] = '7';
	$input['tabindex'] = $t;
	$input['class'] = 'colorpicker';
	$input['value'] = @set_value('colour', $department->colour);
	$input['class'] = 'hidden';
	echo form_input($input);
	$t++;
	?>
	<div id="cp" class="add-bottom"></div>
	
	<?php if ($this->settings->get('auth_ldap_enable') == 1): ?>
	<label for="ldapgroups">LDAP Groups</label>
	<select name="ldapgroups[]" id="ldapgroups" size="20" tabindex="<?php echo $t ?>" multiple="multiple">
	<option value="-1">(None)</option>
	<?php
	foreach ($ldapgroups as $id => $name)
	{
		$selected = (@in_array($id, $department->ldapgroups)) ? ' selected="selected"' : '';
		echo sprintf('<option value="%1$d"%3$s>%2$s</option>', $id, $name, $selected);
	}
	$t++;
	?>
	</select>
	<?php endif; ?>

</div>


<hr>


<div class="alpha three columns">&nbsp;</div>
<div class="omega nine columns"><?php
$text = ($department_id == null) ? 'Add' : 'Save';
unset($buttons);
$buttons[] = array('submit', 'blue', "$text department", $t);
$this->load->view('parts/buttons', array('buttons' => $buttons));
?></div>


</form>




<script type="text/javascript">
_jsQ.push(function(){
	
	// Initialise the colour picker and hide the original textbox
	$('#cp').colorPicker({
		activeColour: '<?php echo $input['value'] ?>',
		click: function(c){$('#colour').val(c);}
	});
	$('#colour').hide();
	
});
</script>