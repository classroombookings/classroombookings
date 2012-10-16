<?php
$errors = validation_errors();
if($errors){
	echo $this->msg->err('<ul>' . $errors . '</ul>', 'Please check the following invalid item(s) and try again.');
}

echo form_open_multipart('security/users/import/1', NULL, array('stage' => 1));

// Start tabindex
$t = 1;
?>

<table class="form" cellpadding="6" cellspacing="0" border="0" width="100%">
	
	<tr class="h"><td colspan="2">File</td></tr>
	
	<tr>
		<td class="caption">
			<label for="userfile" class="r" accesskey="F">CSV <u>F</u>ile</label>
		</td>
		<td class="field">
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
		</td>
	</tr>
	
	<tr class="h"><td colspan="2">Default values</td></tr>
	
	<tr>
		<td class="caption">
			<label for="default_password" accesskey="P"><u>P</u>assword</label>
		</td>
		<td class="field">
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
		</td>
	</tr>
	
	<tr>
		<td class="caption">
			<label for="group_id" class="r" accesskey="G" title="Select the permission group that this user should belong to.">Permission <u>g</u>roup</label>
		</td>
		<td class="field">
			<?php
			echo form_dropdown('default_group_id', $groups, set_value('default_group_id', 0), 'tabindex="'.$t.'"');
			$t++;
			?>
		</td>
	</tr>
	
	<tr>
		<td class="caption">
			<label for="default_enabled" accesskey="E" title="Untick this box to prevent the user logging in. This also applies to LDAP users."><u>E</u>nabled</label>
		</td>
		<td class="field">
			<label for="default_enabled" class="check">
			<?php
			unset($check);
			$check['name'] = 'default_enabled';
			$check['id'] = 'default_enabled';
			$check['value'] = '1';
			#$check['checked'] = @set_checkbox($check['name'], $check['value'], ($user->enabled == 1));
			$check['tabindex'] = $t;
			echo form_checkbox($check);
			$t++;
			?>
			</label>
		</td>
	</tr>
	
	<tr>
		<td class="caption">
			<label for="default_emaildomain" accesskey="E"><u>E</u>mail domain</label>
		</td>
		<td class="field">
			<?php
			unset($input);
			$input['accesskey'] = 'E';
			$input['name'] = 'default_emaildomain';
			$input['id'] = 'default_emaildomain';
			$input['size'] = '50';
			$input['maxlength'] = '100';
			$input['tabindex'] = $t;
			#$input['value'] = @set_value('email', $user->email);
			echo form_input($input);
			$t++;
			?>
		</td>
	</tr>
	
	<?php if(isset($departments) && $departments != NULL){ ?>
	
	<tr>
		<td class="caption">
			<label for="default_department_id" accesskey="T">Department</label>
		</td>
		<td class="field">
			<?php
			echo @form_dropdown('default_deptartment_id', $departments, @set_select('default_department_id'), 'tabindex="'.$t.'"');
			$t++;
			?>
		</td>
	</tr>
	
	<?php } ?>
	
	<?php
	unset($buttons);
	$buttons[] = array('submit', 'positive', 'Upload file', 'table-upload.gif', $t);
	#$buttons[] = array('submit', '', 'Save and add another', 'add.gif', $t+1);
	$buttons[] = array('cancel', 'negative', 'Cancel', 'arr-left.gif', $t+2, site_url('security/users'));
	$this->load->view('parts/buttons', array('buttons' => $buttons));
	?>

</table>
</form>
