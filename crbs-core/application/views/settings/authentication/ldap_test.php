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
		<p><small><?= lang('auth.ldap.test.hint.1') ?></small></p>
		<p><small><?= lang('auth.ldap.test.hint.2') ?></small></p>
		<br>
	</div>

	<legend accesskey="T" tabindex="<?php echo tab_index() ?>"><?= lang('auth.ldap.test.title') ?></legend>

	<p class="input-group">
		<?php
		echo form_label(lang('user.field.username'), 'username');
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
		echo form_label(lang('user.field.password'), 'password');
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
	$attrs = [
		'value' => lang('auth.ldap.test.verify'),
		'tabindex' => tab_index(),
	];
	if (is_demo_mode()) {
		$attrs['disabled'] = '';
	}
	echo form_submit($attrs);
	?>

</fieldset>

<div class="loading-notice"><?= lang('auth.ldap.test.verifying') ?>...</div>

<?= form_close() ?>

<div id="ldap_test_results"></div>
