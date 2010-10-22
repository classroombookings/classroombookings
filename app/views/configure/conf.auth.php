<?php
echo form_open('configure/save_auth', array('name' => 'form_confauth', 'id' => 'form_confauth'));

// Start tabindex
$t = 1;
?>

<div class="grey"><div>
<table class="form" width="100%">
	
	<tr class="h"><td colspan="2"><div>Pre-authentication</div></td></tr>
	
	<tr>
		<td class="caption">
			<label for="auth_preauth" accesskey="P" title="Enable pre-authentication support. Tick this box to enable it, click Save, then the key will be generated and displayed below."><u>P</u>re-authentication</label>
			<p class="tip">Enable pre-authentication support. Tick this box to enable it, click Save, then the key will be generated and displayed below.</p>
		</td>
		<td class="field">
			<label for="auth_preauth" class="check">
			<?php
			echo form_hidden('auth_preauth_key', @$settings['auth.preauth.key']);
			unset($check);
			$check['name'] = 'auth_preauth';
			$check['id'] = 'auth_preauth';
			$check['value'] = '1';
			$check['checked'] = set_radio($check['name'], $check['value'], (!empty($settings['auth.preauth.key'])));
			$check['tabindex'] = $t;
			echo form_checkbox($check);
			$t++;
			?>Enable
			</label>
		</td>
	</tr>
	
	<?php
	if(!empty($settings['auth.preauth.key'])){
		echo form_hidden('preauthfirst', '0');
	?>
	<tr class="preauth">
		<td class="caption">
			<label for="auth_preauth_group_id" class="r" accesskey="U" title="Choose a group that users created automatically via preauth will belong to.">Default gro<u>u</u>p</label>
			<p class="tip">Choose a group that users created automatically via preauth will belong to.</p>
		</td>
		<td class="field">
			<?php
			echo form_dropdown(
				'auth_preauth_group_id', 
				$groups, 
				set_value(
					'auth_preauth_group_id', 
					(isset($settings['auth.preauth.group_id'])) ? $settings['auth.preauth.group_id'] : 0
				), 
				'tabindex="' . $t . '" id="auth_preauth_group_id"'
			);
			$t++;
			?>
		</td>
	</tr>
	<tr class="preauth">
		<td class="caption">
			<label for="auth_preauth_emaildomain" class="r" accesskey="E" title="Default email address domain applied to users created via preauthentication."><u>E</u>mail domain</label>
			<p class="tip">Default email address domain applied to users created via preauthentication.</p>
		</td>
		<td class="field">
			<?php
			unset($input);
			$input['accesskey'] = 'E';
			$input['name'] = 'auth_preauth_emaildomain';
			$input['id'] = 'auth_preauth_emaildomain';
			$input['size'] = '50';
			$input['maxlength'] = '100';
			$input['tabindex'] = $t;
			$input['value'] = @set_value($input['name'], $settings['auth.preauth.emaildomain']);
			echo form_input($input);
			$t++;
			?>
		</td>
	</tr>
	<tr class="preauth">
		<td class="caption">
			<label title="Please read the documentation on how to use the pre-authentication key.">Pre-auth key</label>
			<p class="tip">Please read the documentation on how to use the pre-authentication key.</p>
		</td>
		<td class="field">
			<?php echo @$settings['auth.preauth.key'] ?>
		</td>
	</tr>
	<?php
	} else {
		echo form_hidden('preauthfirst', '1');
	}
	?>
	
</table>
</div></div>

	
	
	<!-- <tr class="h"><td colspan="2">LDAP/Active Directory</td></tr> -->
<div class="grey"><div>
<table class="form" width="100%">

	<tr class="h"><td colspan="2"><div>LDAP/Active Directory</div></td></tr>
	
	<tr>
		<td class="caption">
			<label for="ldap" accesskey="L" title="If you enable LDAP authentication, users who attempt to login with LDAP credentials will be added to Classroombookings if successful. Ensure you set the Default group below to ensure they inherit the correct permissions."><u>L</u>DAP</label>
			<p class="tip">If you enable LDAP authentication, users who attempt to login with LDAP credentials will be added to Classroombookings if successful. Ensure you set the Default group below to ensure they inherit the correct permissions.</p>
		</td>
		<td class="field">
			<label for="auth_ldap" class="check">
			<?php
			unset($check);
			$check['name'] = 'auth_ldap';
			$check['id'] = 'auth_ldap';
			$check['value'] = '1';
			$check['checked'] = set_radio($check['name'], $check['value'], ($settings['auth.ldap'] == 1));
			$check['tabindex'] = $t;
			echo form_checkbox($check);
			$t++;
			?>Enable
			</label>
		</td>
	</tr>
	
	
	<?php
	if($settings['auth.ldap'] == 1){
		echo form_hidden('ldapfirst', '0');
	?>
	
	
	<tr class="ldap">
		<td class="caption">
			<label for="auth_ldap_host" class="r" accesskey="H" title="The LDAP hostname or IP address. If specifying a hostname, please ensure that the server can resolve it via DNS."><u>H</u>ostname</label>
			<p class="tip">The LDAP hostname or IP address. If specifying a hostname, please ensure that the server can resolve it via DNS.</p>
		</td>
		<td class="field">
		  <?php
		  	unset($input);
			$input['accesskey'] = 'H';
			$input['name'] = 'auth_ldap_host';
			$input['id'] = 'auth_ldap_host';
			$input['size'] = '40';
			$input['maxlength'] = '50';
			$input['tabindex'] = $t;
			$input['value'] = set_value($input['name'], $settings['auth.ldap.host']);
			echo form_input($input);
			$t++;
			?>
		</td>
	</tr>
	
	<tr class="ldap">
		<td class="caption">
			<label for="auth_ldap_port" class="r" accesskey="C" title="TCP port used to communicate with the LDAP port on, use 389 if unsure.">T<u>C</u>P Port</label>
			<p class="tip">TCP port used to communicate with the LDAP port on, use 389 if unsure.</p>
		</td>
		<td class="field">
		  <?php
		  	unset($input);
			$input['accesskey'] = 'C';
			$input['name'] = 'auth_ldap_port';
			$input['id'] = 'auth_ldap_port';
			$input['size'] = '5';
			$input['maxlength'] = '5';
			$input['tabindex'] = $t;
			$input['value'] = set_value($input['name'], ($settings['auth.ldap.port']) ? $settings['auth.ldap.port'] : '389');
			echo form_input($input);
			$t++;
			?>
		</td>
	</tr>
	
	<tr class="ldap">
		<td class="caption">
			<label for="auth_ldap_base" class="r" accesskey="D" title="Separate multiple DNs to search with a semicolon">Base <u>D</u>Ns</label>
			<p class="tip">Separate multiple DNs to search with a semicolon.</p>
		</td>
		<td class="field">
		  <?php
		  	unset($input);
			$input['accesskey'] = 'D';
			$input['name'] = 'auth_ldap_base';
			$input['id'] = 'auth_ldap_base';
			$input['maxlength'] = '65536';
			$input['tabindex'] = $t;
			$input['rows'] = '6';
			$input['cols'] = '60';
			$input['value'] = set_value($input['name'], $settings['auth.ldap.base']);
			echo form_textarea($input);
			$t++;
			?>
		</td>
	</tr>
	
	<tr class="ldap">
		<td class="caption">
			<label for="auth_ldap_filter" class="r" accesskey="F" title="LDAP query filter. %u is where the supplied username will be replaced. Leave as default if unsure.">LDAP query filter</label>
			<p class="tip">LDAP query filter. %u is where the supplied username will be replaced. Leave as default if unsure.</p>
		</td>
		<td class="field">
		  <?php
		  	unset($input);
			$input['accesskey'] = 'F';
			$input['name'] = 'auth_ldap_filter';
			$input['id'] = 'auth_ldap_filter';
			#$input['size'] = '60';
			$input['maxlength'] = '5120';
			$input['tabindex'] = $t;
			$input['rows'] = '4';
			$input['cols'] = '60';
			$input['value'] = set_value('auth_ldap_filter', ($settings['auth.ldap.filter']) ? $settings['auth.ldap.filter'] : "(& (| (!(displayname=Administrator*)) (!(displayname=Admin*)) ) (cn=%u) )");
			echo form_textarea($input);
			$t++;
			?>
		</td>
	</tr>
	
	<tr class="ldap">
		<td class="caption">
			<label for="auth_ldap_group_id" class="r" accesskey="G" title="This is the group that users who authenticate via LDAP will become members of automatically.">Default CRBS group</label>
			<p class="tip">This is the group that users who authenticate via LDAP will become members of automatically.</p>
		</td>
		<td class="field">
			<?php
			echo form_dropdown(
				'auth_ldap_group_id', 
				$groups, 
				set_value(
					'auth_ldap_group_id', 
					(isset($settings['auth.ldap.group_id'])) ? $settings['auth.ldap.group_id'] : 0
				),
				array('tabindex' => $t)
			);
			$t++;
			?>
		</td>
	</tr>
	
	<tr>
		<td class="caption">
			<label for="auth_ldap_loginupdate" accesskey="T" title="If this option is enabled, the user details (display name, group and department membership) will be updated with their LDAP info every time they login; potentially un-doing any customisations you made to the user.">Upda<u>t</u>e details on login</label>
			<p class="tip">If this option is enabled, the user details (display name, group and department membership) will be updated with their LDAP info every time they login; potentially un-doing any customisations you made to the user.</p>
		</td>
		<td class="field">
			<label for="auth_ldap_loginupdate" class="check">
			<?php
			unset($check);
			$check['name'] = 'auth_ldap_loginupdate';
			$check['id'] = 'auth_ldap_loginupdate';
			$check['value'] = '1';
			$check['checked'] = set_radio($check['name'], $check['value'], ($settings['auth.ldap.loginupdate'] == 1));
			$check['tabindex'] = $t;
			echo form_checkbox($check);
			$t++;
			?>Yes
			</label>
		</td>
	</tr>
	
	<tr class="ldap">
		<td class="caption">
			<label for="ldaptestuser" accesskey="U" title="Enter a username to test the LDAP settings with, and click Test LDAP.">Test username</label>
			<p class="tip">Enter a username to test the LDAP settings with, and click Test LDAP.</p>
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
	
	<tr class="ldap">
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
	} else {
		echo form_hidden('ldapfirst', '1');
	}
	?>
	
</table>
</div></div>


<table class="form" width="100%">
	<?php
	unset($buttons);
	$buttons[] = array('submit', 'ok', 'Save authentication settings', $t);
	$buttons[] = array('button', 'misc', 'Test LDAP', $t+1, '', 'test-ldap');
	$this->load->view('parts/buttons', array('buttons' => $buttons));
	?>
</table>


</form>


<script type="text/javascript">
_jsQ.push(function(){
	$("#test-ldap").bind("click", function(e){
		var w = window.open("about:blank","ldaptestwin","width=640,height=400,toolbar=0,location=0,menubar=0,scrollbars=1,resizable=1");
		var oldaction = $("#form_confauth").attr("action");
		var oldtarget = $("#form_confauth").attr("target");
		$("#form_confauth").attr("action","<?php echo site_url("configure/test_ldap") ?>");
		$("#form_confauth").attr("target","ldaptestwin");
		$("#form_confauth").submit();
		$("#form_confauth").attr("action",oldaction);
		$("#form_confauth").attr("target",oldtarget);
	});
});

/* 
function togglefields(cat, visibility){
	$(".preauth").addClass("hidden");
} */
</script>