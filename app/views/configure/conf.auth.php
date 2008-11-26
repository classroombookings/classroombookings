<?php
echo form_open('configure/save_ldap', array('name' => 'form_confauth', 'id' => 'form_confauth'));

// Start tabindex
$t = 1;
?>


<table class="form" cellpadding="6" cellspacing="0" border="0" width="50%">
	
	<tr class="h"><td colspan="2">Authentication options</td></tr>
	
	<tr>
		<td class="caption">
			<label for="preauth" accesskey="P" title="Enable pre-authentication support. Tick this box to enable it, click Save, then the key will be generated and displayed below."><u>P</u>re-authentication</label>
		</td>
		<td class="field">
			<label for="preauth" class="check">
			<?php
			echo form_hidden('preauthkey', $auth->preauthkey);
			unset($check);
			$check['name'] = 'preauth';
			$check['id'] = 'preauth';
			$check['value'] = 'preauth';
			$check['checked'] = set_radio($check['name'], $check['value'], (!empty($auth->preauthkey)));
			$check['tabindex'] = $t;
			echo form_checkbox($check);
			$t++;
			?>Enable
			</label>
		</td>
	</tr>
	
	<?php if(!empty($auth->preauthkey)){ ?>
	<tr>
		<td class="caption">
			<label>Your pre-auth key</label>
		</td>
		<td class="field">
			<label><?php echo $auth->preauthkey ?></label>
		</td>
	</tr>
	<?php } ?>
	
	<tr>
		<td class="caption">
			<label for="ldap" accesskey="L" title="If you enable LDAP authentication, users who attempt to login with LDAP credentials will be added to Classroombookings if successful. Ensure you set the Default group below to ensure they inherit the correct permissions."><u>L</u>DAP</label>
		</td>
		<td class="field">
			<label for="ldap" class="check">
			<?php
			unset($check);
			$check['name'] = 'ldap';
			$check['id'] = 'ldap';
			$check['value'] = 'ldap';
			$check['checked'] = set_radio($check['name'], $check['value'], ($auth->ldap == 1));
			$check['tabindex'] = $t;
			echo form_checkbox($check);
			$t++;
			?>Enable
			</label>
		</td>
	</tr>
	
	<tr class="h"><td colspan="2">LDAP settings</td></tr>
	
	<tr>
		<td class="caption">
			<label for="ldaphost" class="r" accesskey="H" title="The LDAP hostname or IP address. If specifying a hostname, please ensure that the server can resolve it via DNS."><u>H</u>ostname</label>
		</td>
		<td class="field">
		  <?php
		  	unset($input);
			$input['accesskey'] = 'H';
			$input['name'] = 'ldaphost';
			$input['id'] = 'ldaphost';
			$input['size'] = '40';
			$input['maxlength'] = '50';
			$input['tabindex'] = $t;
			$input['value'] = set_value('ldaphost', $auth->ldaphost);
			echo form_input($input);
			$t++;
			?>
		</td>
	</tr>
	
	<tr>
		<td class="caption">
			<label for="ldapport" class="r" accesskey="C" title="TCP port used to communicate with the LDAP port on, use 389 if unsure.">T<u>C</u>P Port</label>
		</td>
		<td class="field">
		  <?php
		  	unset($input);
			$input['accesskey'] = 'C';
			$input['name'] = 'ldapport';
			$input['id'] = 'ldapport';
			$input['size'] = '5';
			$input['maxlength'] = '5';
			$input['tabindex'] = $t;
			$input['value'] = set_value('ldapport', $auth->ldapport);
			echo form_input($input);
			$t++;
			?>
		</td>
	</tr>
	
	<tr>
		<td class="caption">
			<label for="ldapbase" class="r" accesskey="D">Base <u>D</u>N</label>
		</td>
		<td class="field">
		  <?php
		  	unset($input);
			$input['accesskey'] = 'D';
			$input['name'] = 'ldapbase';
			$input['id'] = 'ldapbase';
			$input['size'] = '60';
			$input['maxlength'] = '1024';
			$input['tabindex'] = $t;
			$input['value'] = set_value('ldapbase', $auth->ldapbase);
			echo form_input($input);
			$t++;
			?>
		</td>
	</tr>
	
	<tr>
		<td class="caption">
			<label for="ldapgroup_id" class="r" accesskey="G" title="This is the group that users who authenticate via LDAP will become members of automatically.">Default CRBS group</label>
		</td>
		<td class="field">
		  <?php
		  	unset($input);
			$input[0] = 'Guests';
			$input[1] = 'Administrators';
			$input[3] = 'Teachers';
			echo form_dropdown('ldapgroup_id', $input, 0, 'id="ldapgroup_id"');
			$t++;
			?>
		</td>
	</tr>
	
	<tr>
		<td class="caption">
			<label for="ldaptestuser" accesskey="U" title="Enter a username to test the LDAP settings with, and click Test LDAP.">Test username</label>
		</td>
		<td class="field">
		  <?php
		  	unset($input);
			$input['accesskey'] = 'U';
			$input['name'] = 'ldaptestuser';
			$input['id'] = 'ldaptestuser';
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
			<label for="ldaptestpass" accesskey="W">Test password</label>
		</td>
		<td class="field">
		  <?php
		  	unset($input);
			$input['accesskey'] = 'W';
			$input['name'] = 'ldaptestpass';
			$input['id'] = 'ldaptestpass';
			$input['size'] = '20';
			$input['maxlength'] = '50';
			$input['tabindex'] = $t;
			$input['autocomplete'] = 'off';
			$input['value'] = set_value('ldaptestpass');
			echo form_password($input);
			$t++;
			?>
		</td>
	</tr>
	
	<?php
	unset($buttons);
	$buttons[] = array('submit', 'positive', 'Save authentication settings', 'disk1.gif', $t);
	$buttons[] = array('other', 'positive', 'Test LDAP', 'control-double.gif', $t+1);
	$this->load->view('parts/buttons', array('buttons' => $buttons));
	?>

</table>

</form>




<script type="text/javascript">
$("#test-ldap").bind("click", function(e){
    var w = window.open("about:blank","ldaptestwin","width=640,height=400,toolbar=0");
	var oldaction = $("#form_confauth").attr("action");
	var oldtarget = $("#form_confauth").attr("target");
	$("#form_confauth").attr("action","<?php echo site_url("configure/test_ldap") ?>");
	$("#form_confauth").attr("target","ldaptestwin");
	$("#form_confauth").submit();
	$("#form_confauth").attr("action",oldaction);
	$("#form_confauth").attr("target",oldtarget);
});
</script>