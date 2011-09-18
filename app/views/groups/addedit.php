<?php
$errors = validation_errors();
if ($errors)
{
	echo '<div class="row">';
	echo $this->msg->err('<ul class="square">' . $errors . '</ul>', 'Please check the following invalid item(s) and try again.');
	echo '</div>';
}


echo form_open('groups/save', null, array('group_id' => $group_id));


if (!empty($group->name))
{
	echo form_hidden('old_name', $group->name);
}

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
	$input['value'] = @set_value($input['name'], $group->name);
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
	$input['value'] = @set_value($input['name'], $group->description);
	echo form_textarea($input);
	$t++;
	?>
	
	
	<?php if ($this->settings->get('auth_ldap_enable') == 1): ?>
	
	<label for="ldapgroups">LDAP Groups</label>
	<select name="ldapgroups[]" id="ldapgroups" size="20" tabindex="<?php echo $t ?>" multiple="multiple">
	<option value="-1">(None)</option>
	<?php
	foreach($ldapgroups as $id => $name){
		$selected = (in_array($id, $group->ldapgroups)) ? ' selected="selected"' : '';
		echo sprintf('<option value="%1$d"%3$s>%2$s</option>', $id, $name, $selected);
	}
	$t++;
	?>
	</select>
	
	<p class="hint add-bottom">Users who belong to the selected LDAP group(s) will be put in this Classroombookings group.<br />
	The selected group(s) will not be available to assign to other groups, becuase a user can only be a member of one group.</p>
	
	<?php endif; ?>


</div>

<hr>

<div class="alpha three columns"><h6>Other options</h6></div>

<div class="omega nine columns">

	<label for="name">Booking ahead</label>
	<?php
	unset($input);
	$input['name'] = 'bookahead';
	$input['id'] = 'bookahead';
	$input['size'] = '10';
	$input['maxlength'] = '3';
	$input['value'] = @set_value($input['name'], $group->bookahead);
	echo form_input($input);
	?>
	
	<p class="hint add-bottom">The number of days ahead users can create a booking. Leave blank to allow bookings at any time in the future.</p>
	
	
	<label for="quota_num">Quota</label>
	<?php
	unset($input);
	$input['name'] = 'quota_num';
	$input['id'] = 'quota_num';
	$input['size'] = '10';
	$input['maxlength'] = '3';
	$input['value'] = @set_value($input['name'], $group->quota_num);
	echo form_input($input);
	?>
	<p class="tip add-bottom">The number of bookings a user in this group can have or make.</p>

</div>


</form>