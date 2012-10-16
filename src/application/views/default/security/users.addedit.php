<?php
$errors = validation_errors();
if($errors){
	echo $this->msg->err('<ul>' . $errors . '</ul>', 'Please check the following invalid item(s) and try again.');
}

echo form_open('security/users/save', NULL, array('user_id' => $user_id));

// Start tabindex
$t = 1;
?>

<div class="grey"><div>
<table class="form" width="100%">
	
	<tr class="h"><td colspan="2"><div>User details</div></td></tr>
	
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
			<label for="group_id" class="r" accesskey="G">Permission <u>g</u>roup</label>
			<p class="tip">Select the permission group that this user should belong to.</p>
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
			<label for="enabled" accesskey="E"><u>E</u>nabled</label>
			<p class="tip">Untick this box to prevent the user logging in. This also affects LDAP users.</p>
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
			<label for="ldap" accesskey="L"><u>L</u>DAP user</label>
			<p class="tip">Specify if this user should authenticate via LDAP or Classroombookings.</p>
		</td>
		<td class="field">
			<label for="ldap" class="check">
			<?php
			unset($check);
			$check['name'] = 'ldap';
			$check['id'] = 'ldap';
			$check['value'] = '1';
			$check['checked'] = @set_checkbox($check['name'], $check['value'], ($user->ldap == 1));
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
			<label for="departments" accesskey="T">Departments</label>
		</td>
		<td class="field">
			<select name="departments[]" id="departments" size="10" multiple="multiple" tabindex="<?php echo $t ?>">
			<?php
			foreach($departments as $did => $dname){
				$selected = (in_array($did, $user->departments)) ? ' selected="selected"' : '';
				echo '<option value="' . $did . '"' . $selected . '>' . $dname . '</option>';
			}
			#echo @form_dropdown('deptartments[]', $departments, @set_select('departments[]', $user->department_id), 'tabindex="'.$t.'"');
			$t++;
			?>
			</select>
		</td>
	</tr>
	
	<?php } ?>
	
	<?php if(isset($user) && $user->quota_type != NULL){ ?>
	
	<tr>
		<td class="caption">
			<label for="displayname" accesskey="Q"><u>Q</u>uota</label>
		</td>
		<td class="field">
			<?php
			unset($input);
			$input['accesskey'] = 'Q';
			$input['name'] = 'quota';
			$input['id'] = 'quota';
			$input['size'] = '10';
			$input['maxlength'] = '5';
			$input['tabindex'] = $t;
			$input['value'] = @set_value('quota', $user->quota_num);
			echo form_input($input);
			$t++;
			?>
		</td>
	</tr>
	
	<?php } ?>
	
</table>
</div></div>


<table class="form" width="100%">
	<?php
	if($user_id == NULL){
		$submittext = 'Add user';
	} else {
		$submittext = 'Save user';
	}
	unset($buttons);
	$buttons[] = array('submit', 'ok', $submittext, $t);
	#$buttons[] = array('submit', '', 'Save and add another', 'add.gif', $t+1);
	#$buttons[] = array('cancel', 'negative', 'Cancel', 'arr-left.gif', $t+2, site_url('security/users'));
	$this->load->view('parts/buttons', array('buttons' => $buttons));
	?>
</table>


</form>