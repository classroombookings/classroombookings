<?php
$attrs = [
	'class' => 'cssform-stacked up-form',
	'ldap-test' => '',
	'up-target' => '#ldap_test_results',
	'up-history' => 'false',
];

$hidden = [
	'ldap_server' => '',
	'ldap_port' => '',
	'ldap_version' => '',
	'ldap_use_tls' => '',
	'ldap_ignore_cert' => '',
	'ldap_bind_dn_format' => '',
	'ldap_base_dn' => '',
	'ldap_search_filter' => '',
	'ldap_attr_firstname' => '',
	'ldap_attr_lastname' => '',
	'ldap_attr_displayname' => '',
	'ldap_attr_email' => '',
];

echo form_open('settings/authentication/ldap_test', $attrs, $hidden);

?>

<fieldset>

	<div class="fieldset-description">
		<p><small>Change settings on the left then enter a username and password here to test them. You don't need to click Save before testing the credentials.</small></p>
		<p><small>These credentials are only passed to the LDAP server and are never saved or stored.</small></p>
		<br>
	</div>

	<legend accesskey="T" tabindex="<?php echo tab_index() ?>">Test Settings</legend>

	<p class="input-group">
		<?php
		echo form_label('Username', 'username');
		echo form_input([
			'name' => 'username',
			'id' => 'username',
			'size' => '30',
			'maxlength' => '50',
			'tabindex' => tab_index(),
		]);
	?>
	</p>

	<p class="input-group">
		<?php
		echo form_label('Password', 'password');
		echo form_password([
			'name' => 'password',
			'id' => 'password',
			'size' => '30',
			'maxlength' => '50',
			'tabindex' => tab_index(),
		]);
		?>
	</p>

	<?php
	echo form_submit([
		'value' => 'Test credentials',
		'tabindex' => tab_index(),
	]);
	?>

</fieldset>

<div class="loading-notice">Testing connection...</div>

<div id="ldap_test_results"></div>

<?= form_close() ?>
