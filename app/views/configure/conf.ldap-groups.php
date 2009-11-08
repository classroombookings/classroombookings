<?php
echo form_open('configure/get_ldap_groups', array('name' => 'form_confldapgroups', 'id' => 'form_confldapgroups'));

// Start tabindex
$t = 1;
?>

<table class="form" cellpadding="6" cellspacing="0" border="0" width="100%">
	
	<tr class="h"><td colspan="2">LDAP bind settings</td></tr>
	
	<tr>
		<td class="caption">
			<label for="ldapbase" class="r" accesskey="D" title="Separate multiple DNs to search with a semicolon">Base <u>D</u>Ns</label>
			<p class="tip">Separate multiple DNs to search with a semicolon.</p>
		</td>
		<td class="field">
		  <?php
		  	unset($input);
			$input['accesskey'] = 'D';
			$input['name'] = 'ldapbase';
			$input['id'] = 'ldapbase';
			$input['maxlength'] = '65536';
			$input['tabindex'] = $t;
			$input['rows'] = '6';
			$input['cols'] = '60';
			$input['value'] = set_value('ldapbase', $auth->ldapbase);
			echo form_textarea($input);
			$t++;
			?>
		</td>
	</tr>
	
	<tr>
		<td class="caption">
			<label class="r" for="user" accesskey="U"><u>U</u>sername</label>
		</td>
		<td class="field">
		  <?php
		  	unset($input);
			$input['accesskey'] = 'U';
			$input['name'] = 'user';
			$input['id'] = 'user';
			$input['size'] = '20';
			$input['maxlength'] = '50';
			$input['tabindex'] = $t;
			$input['autocomplete'] = 'off';
			$input['value'] = set_value('ldaptestuser');
			echo form_input($input);
			$t++;
			?>
		</td>
	</tr>
	
	<tr>
		<td class="caption">
			<label class="r" for="pass" accesskey="W">Pass<u>w</u>ord</label>
		</td>
		<td class="field">
		  <?php
		  	unset($input);
			$input['accesskey'] = 'W';
			$input['name'] = 'pass';
			$input['id'] = 'pass';
			$input['size'] = '20';
			$input['maxlength'] = '50';
			$input['tabindex'] = $t;
			$input['autocomplete'] = 'off';
			$input['value'] = '';
			echo form_password($input);
			$t++;
			?>
		</td>
	</tr>
	
	<tr>
		<td class="caption">
			<label for="clear" accesskey="C" title="Remove the groups you previously imported and start from fresh."><u>C</u>lear</label>
			<p class="tip">Remove the groups you previously imported and start from fresh.</p>
		</td>
		<td class="field">
			<label for="clear" class="check">
			<?php
			unset($check);
			$check['name'] = 'clear';
			$check['id'] = 'clear';
			$check['value'] = '1';
			$check['checked'] = FALSE;
			$check['tabindex'] = $t;
			echo form_checkbox($check);
			$t++;
			?>Clear existing groups first
			</label>
		</td>
	</tr>
	
	<tr>
		<td class="caption">
			<label for="ignorespecial" accesskey="I" title="Ignore groups with special characters like {."><u>I</u>gnore special chars</label>
			<p class="tip">Ignore groups with special characters like {.</p>
		</td>
		<td class="field">
			<label for="ignorespecial" class="check">
			<?php
			unset($check);
			$check['name'] = 'ignorespecial';
			$check['id'] = 'ignorespecial';
			$check['value'] = '1';
			$check['checked'] = FALSE;
			$check['tabindex'] = $t;
			echo form_checkbox($check);
			$t++;
			?>Ignore group names containing special characters
			</label>
		</td>
	</tr>
	
	<?php
	unset($buttons);
	$buttons[] = array('submit', 'positive', 'Get groups', 'arr-circle1.gif', $t);
	$this->load->view('parts/buttons', array('buttons' => $buttons));
	?>
	
	<tr><td colspan="2">&nbsp;</td></tr>
	
	<tr class="h"><td colspan="2">LDAP groups (<?php echo count($ldapgroups) ?>)</td></tr>
	
	<tr>
		<td colspan="2">
		<table class="list" width="99%" cellpadding="0" cellspacing="0" border="0">
		<?php
		if(count($ldapgroups) > 0){
			foreach($ldapgroups as $group){
				echo sprintf('<tr><td class="m">%s</td></tr>', $group);
			}
		} else {
			echo '<p>No groups exist at the moment.</p>';
		}
		?>
		</table>
		</td>
	</tr>

</table>

</form>