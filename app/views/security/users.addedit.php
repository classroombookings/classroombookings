<?php
$errors = validation_errors();
if($errors){
	echo $this->msg->err('<ul>' . $errors . '</ul>', 'Please check the following invalid item(s) and try again.');
}

echo form_open('security/users/save', NULL, array('user_id' => $user_id));

// Start tabindex
$t = 1;
?>

<table class="form" cellpadding="6" cellspacing="0" border="0" width="100%">
	
	<tr class="h"><td colspan="2">User details</td></tr>
	
	<tr>
		<td class="caption">
			<label for="username" class="r" accesskey="U"><u>U</u>sername</label>
		</td>
		<td class="field">
			<?php
			unset($input);
			$input['accesskey'] = 'U';
			$input['name'] = 'username';
			$input['id'] = 'username';
			$input['size'] = '30';
			$input['maxlength'] = '64';
			$input['tabindex'] = $t;
			$input['autocomplete'] = 'off';
			$input['value'] = @set_value('username', $user->username);
			echo form_input($input);
			$t++;
			?>
		</td>
	</tr>
	
	<tr>
		<td class="caption">
			<label for="password1" class="r" accesskey="P"><u>P</u>assword</label>
		</td>
		<td class="field">
			<?php
			unset($input);
			$input['accesskey'] = 'P';
			$input['name'] = 'password1';
			$input['id'] = 'password1';
			$input['size'] = '30';
			$input['maxlength'] = '104';
			$input['tabindex'] = $t;
			$input['autocomplete'] = 'off';
			echo form_password($input);
			$t++;
			?>
		</td>
	</tr>
	
	<tr>
		<td class="caption">
			<label for="password2" class="r" accesskey="W">Pass<u>w</u>ord (again)</label>
		</td>
		<td class="field">
			<?php
			unset($input);
			$input['accesskey'] = 'W';
			$input['name'] = 'password2';
			$input['id'] = 'password2';
			$input['size'] = '30';
			$input['maxlength'] = '104';
			$input['tabindex'] = $t;
			$input['autocomplete'] = 'off';
			echo form_password($input);
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
			echo form_dropdown('group_id', $groups, set_value('group_id', (isset($user->group_id) ? $user->group_id : 0)), 'tabindex="'.$t.'"');
			$t++;
			?>
		</td>
	</tr>
	
	<tr>
		<td class="caption">
			<label for="enabled" accesskey="E" title="Untick this box to prevent the user logging in. This also applies to LDAP users."><u>E</u>nabled</label>
		</td>
		<td class="field">
			<label for="enabled" class="check">
			<?php
			unset($check);
			$check['name'] = 'enabled';
			$check['id'] = 'enabled';
			$check['value'] = '1';
			$check['checked'] = @set_checkbox($check['name'], $check['value'], ($user->enabled == 1));
			$check['tabindex'] = $t;
			echo form_checkbox($check);
			$t++;
			?>
			</label>
		</td>
	</tr>
	
	<tr>
		<td class="caption">
			<label for="email" accesskey="E"><u>E</u>mail address</label>
		</td>
		<td class="field">
			<?php
			unset($input);
			$input['accesskey'] = 'E';
			$input['name'] = 'email';
			$input['id'] = 'email';
			$input['size'] = '50';
			$input['maxlength'] = '100';
			$input['tabindex'] = $t;
			$input['value'] = @set_value('email', $user->email);
			echo form_input($input);
			$t++;
			?>
		</td>
	</tr>
	
	<tr>
		<td class="caption">
			<label for="displayname" accesskey="D"><u>D</u>isplay name</label>
		</td>
		<td class="field">
			<?php
			unset($input);
			$input['accesskey'] = 'D';
			$input['name'] = 'displayname';
			$input['id'] = 'displayname';
			$input['size'] = '30';
			$input['maxlength'] = '64';
			$input['tabindex'] = $t;
			$input['value'] = @set_value('displayname', $user->displayname);
			echo form_input($input);
			$t++;
			?>
		</td>
	</tr>
	
	<?php if(isset($departments) && $departments != NULL){ ?>
	
	<tr>
		<td class="caption">
			<label for="department_id" accesskey="T">Department</label>
		</td>
		<td class="field">
			<?php
			echo @form_dropdown('deptartment_id', $departments, @set_select('department_id', $user->department_id), 'tabindex="'.$t.'"');
			$t++;
			?>
		</td>
	</tr>
	
	<?php } ?>
	
	<?php
	if($user_id == NULL){
		$submittext = 'Add user';
	} else {
		$submittext = 'Save user';
	}
	unset($buttons);
	$buttons[] = array('submit', 'positive', $submittext, 'disk1.gif', $t);
	#$buttons[] = array('submit', '', 'Save and add another', 'add.gif', $t+1);
	$buttons[] = array('cancel', 'negative', 'Cancel', 'arr-left.gif', $t+2, site_url('security/users'));
	$this->load->view('parts/buttons', array('buttons' => $buttons));
	?>

</table>
</form>
