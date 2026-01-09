<?php

if (is_demo_mode()) {
	echo msgbox('notice large', lang('auth.ldap.demo_notice'));
}

echo form_open(current_url(), array('id' => 'ldap_settings', 'class' => 'cssform', 'ldap-settings' => ''));

?>


<fieldset>

	<legend accesskey="L" tabindex="<?php echo tab_index() ?>"><?= lang('auth.ldap.ldap') ?></legend>

	<?php
	$field = 'ldap_enabled';
	$value = set_value($field, element($field, $settings, '0'), FALSE);
	?>
	<p>
		<label for="<?= $field ?>"><?= lang('auth.ldap.field.ldap_enabled') ?></label>
		<?php
		echo form_hidden($field, '0');
		$input_options = [
			'name' => $field,
			'id' => $field,
			'value' => '1',
			'tabindex' => tab_index(),
		];

		if (is_demo_mode()) {
			$input_options['checked'] = FALSE;
			$input_options['disabled'] = 'disabled';
		} else {
			$input_options['checked'] = ($value == '1');
		}

		$input = form_checkbox($input_options);
		echo "<label for='{$field}' class='ni'>{$input} " . lang('auth.ldap.field.ldap_enabled.title') . "</label>";
	?>
	</p>
	<?php echo form_error($field) ?>

	<?php
	$field = 'ldap_create_users';
	$value = set_value($field, element($field, $settings, '0'), FALSE);
	?>
	<p>
		<label for="<?= $field ?>"><?= lang('auth.ldap.field.ldap_create_users') ?></label>
		<?php
		echo form_hidden($field, '0');
		$input = form_checkbox(array(
			'name' => $field,
			'id' => $field,
			'value' => '1',
			'tabindex' => tab_index(),
			'checked' => ($value == '1')
		));
		echo "<label for='{$field}' class='ni'>{$input} " . lang('auth.ldap.field.ldap_create_users.title') . "</label>";
		?>
		<p class="hint"><?= lang('auth.ldap.field.ldap_create_users.hint.1') ?></p><br>
		<p class="hint"><?= lang('auth.ldap.field.ldap_create_users.hint.2') ?></p>
	</p>
	<?php echo form_error($field) ?>

</fieldset>

<fieldset>

	<legend accesskey="C" tabindex="<?php echo tab_index() ?>"><?= lang('auth.ldap.connection') ?></legend>

	<?php
	$field = 'ldap_server';
	$value = set_value($field, element($field, $settings), FALSE);
	?>
	<p>
		<label for="<?= $field ?>"><?= lang('auth.ldap.field.ldap_server') ?></label>
		<?php
		echo form_input(array(
			'name' => $field,
			'id' => $field,
			'size' => '40',
			'maxlength' => '100',
			'tabindex' => tab_index(),
			'value' => $value,
		));
		?>
		<p class="hint"><?= lang('auth.ldap.field.ldap_server.hint') ?></p>
	</p>
	<?php echo form_error($field) ?>

	<?php
	$field = 'ldap_port';
	$value = set_value($field, element($field, $settings), FALSE);
	?>
	<p>
		<label for="<?= $field ?>"><?= lang('auth.ldap.field.ldap_port') ?></label>
		<?php
		echo form_input(array(
			'type' => 'number',
			'name' => $field,
			'id' => $field,
			'size' => '5',
			'maxlength' => '5',
			'tabindex' => tab_index(),
			'value' => $value,
			'style' => 'max-width:50px'
		));
		?>
		<p class="hint"><?= lang('auth.ldap.field.ldap_port.hint') ?></p>
	</p>
	<?php echo form_error($field) ?>

	<?php
	$field = 'ldap_version';
	$value = set_value($field, element($field, $settings, 3), FALSE);
	?>
	<p>
		<label for="<?= $field ?>"><?= lang('auth.ldap.field.ldap_version') ?></label>
		<?php
		echo form_input(array(
			'type' => 'number',
			'name' => $field,
			'id' => $field,
			'size' => '5',
			'maxlength' => '5',
			'tabindex' => tab_index(),
			'value' => $value,
			'style' => 'max-width:50px'
		));
		?>
		<p class="hint"><?= lang('auth.ldap.field.ldap_version.hint') ?></p>
	</p>
	<?php echo form_error($field) ?>

	<?php
	$field = 'ldap_use_tls';
	$value = set_value('ldap_use_tls', element('ldap_use_tls', $settings, '0'), FALSE);
	?>
	<p>
		<label for="<?= $field ?>"><?= lang('auth.ldap.field.ldap_use_tls') ?></label>
		<?php
		echo form_hidden($field, '0');
		echo form_checkbox(array(
			'name' => $field,
			'id' => $field,
			'value' => '1',
			'tabindex' => tab_index(),
			'checked' => ($value == '1')
		));
	?>
	</p>
	<?php echo form_error($field) ?>

	<?php
	$field = 'ldap_ignore_cert';
	$value = set_value('ldap_ignore_cert', element('ldap_ignore_cert', $settings, '0'), FALSE);
	?>
	<p>
		<label for="<?= $field ?>"><?= lang('auth.ldap.field.ldap_ignore_cert') ?></label>
		<?php
		echo form_hidden($field, '0');
		echo form_checkbox(array(
			'name' => $field,
			'id' => $field,
			'value' => '1',
			'tabindex' => tab_index(),
			'checked' => ($value == '1')
		));
	?>
	</p>
	<?php echo form_error($field) ?>


	<?php
	$field = 'ldap_bind_dn_format';
	$value = set_value($field, element($field, $settings), FALSE);
	?>
	<p>
		<label for="<?= $field ?>"><?= lang('auth.ldap.field.ldap_bind_dn_format') ?></label>
		<?php
		echo form_textarea([
			'name' => $field,
			'id' => $field,
			'rows' => '3',
			'cols' => '60',
			'tabindex' => tab_index(),
			'value' => $value,
		]);
		?>
		<p class="hint"><?= lang('auth.ldap.field.ldap_bind_dn_format.hint') ?></p>
		<ul class="hint">
			<li>EXAMPLE.LOCAL\:user</li>
			<li>:user@EXAMPLE.LOCAL</li>
			<li>uid=:user,cn=users,dc=example,dc=com</li>
		</ul>
	</p>
	<?php echo form_error($field) ?>

</fieldset>

<fieldset>

	<legend accesskey="H" tabindex="<?php echo tab_index() ?>"><?= lang('auth.ldap.search') ?></legend>

	<?php
	$field = 'ldap_base_dn';
	$value = set_value($field, element($field, $settings), FALSE);
	?>
	<p>
		<label for="<?= $field ?>"><?= lang('auth.ldap.field.ldap_base_dn') ?></label>
		<?php
		echo form_textarea(array(
			'name' => $field,
			'id' => $field,
			'rows' => '3',
			'cols' => '60',
			'tabindex' => tab_index(),
			'value' => $value,
		));
		?>
		<p class="hint"><?= lang('app.example') ?>: dc=example,dc=local</p>
	</p>
	<?php echo form_error($field) ?>

	<?php
	$field = 'ldap_search_filter';
	$value = set_value($field, element($field, $settings), FALSE);
	?>
	<p>
		<label for="<?= $field ?>"><?= lang('auth.ldap.field.ldap_search_filter') ?></label>
		<?php
		echo form_textarea(array(
			'name' => $field,
			'id' => $field,
			'rows' => '6',
			'cols' => '60',
			'tabindex' => tab_index(),
			'value' => $value,
		));
		?>
		<p class="hint"><?= lang('app.example') ?>: (&(:attr=:user))</p>
		<br>
		<p class="hint"><?= lang('auth.ldap.field.ldap_search_filter.hint') ?></p>
	</p>
	<?php echo form_error($field) ?>

</fieldset>


<fieldset>

	<legend accesskey="U" tabindex="<?php echo tab_index() ?>"><?= lang('auth.ldap.user_attribute_mapping') ?></legend>

	<div class="fieldset-description">
		<p><?= lang('auth.ldap.user_attribute_mapping.hint.1') ?></p>
		<p><?= lang('auth.ldap.user_attribute_mapping.hint.2') ?><span>:givenName :sn</span>.</p>
		<p><?= lang('auth.ldap.user_attribute_mapping.hint.3') ?></p>
	</div>

	<?php

	$fields = [
		[
			'name' => 'ldap_attr_firstname',
			'label' => lang('user.field.firstname'),
			'hint' => sprintf('%s: %s', lang('app.example'), 'givenName'),
		],
		[
			'name' => 'ldap_attr_lastname',
			'label' => lang('user.field.lastname'),
			'hint' => sprintf('%s: %s', lang('app.example'), 'sn'),
		],
		[
			'name' => 'ldap_attr_displayname',
			'label' => lang('user.field.displayname'),
			'hint' => sprintf('%s: %s', lang('app.example'), "displayName or ':givenName :sn'"),
		],
		[
			'name' => 'ldap_attr_email',
			'label' => lang('user.field.email'),
			'hint' => sprintf('%s: %s', lang('app.example'), "mail"),
		],
	];

	foreach ($fields as $field) {

		$value = set_value($field['name'], element($field['name'], $settings), FALSE);

		$label_el = "<label for='{$field['name']}'>{$field['label']}</label>";
		$input = form_input([
			'name' => $field['name'],
			'id' => $field['name'],
			'size' => '40',
			'maxlength' => '100',
			'tabindex' => tab_index(),
			'value' => $value,
		]);

		$hint = '';
		if (isset($field['hint'])) {
			$hint = "<p class='hint'>{$field['hint']}</p>";
		}

		echo "<p>\n{$label_el}\n{$input}\n{$hint}</p>";
		echo form_error($field['name']);

	}
	?>

</fieldset>

<fieldset>
	<legend><?= lang('auth.ldap.user_assignments') ?></legend>


	<?php
	$field = 'ldap_default_role_id';
	$value = set_value($field, element($field, $settings, ''), FALSE);
	?>
	<p>
		<label for="<?= $field ?>"><?= lang('role.role') ?></label>
		<?php
		echo form_dropdown([
			'name' => 'ldap_default_role_id',
			'id' => 'ldap_default_role_id',
			'options' => $role_options,
			'selected' => $value,
			'tabindex' => tab_index(),
		]);
		?>
	</p>
	<?php echo form_error($field) ?>

	<?php
	$field = 'ldap_default_department_id';
	$value = set_value($field, element($field, $settings, ''), FALSE);
	?>
	<p>
		<label for="<?= $field ?>"><?= lang('department.department') ?></label>
		<?php
		echo form_dropdown([
			'name' => 'ldap_default_department_id',
			'id' => 'ldap_default_department_id',
			'options' => $department_options,
			'selected' => $value,
			'tabindex' => tab_index(),
		]);
		?>
	</p>
	<?php echo form_error($field) ?>

</fieldset>



<?php

$this->load->view('partials/submit', array(
	'submit' => array(lang('app.action.save'), tab_index()),
));

echo form_close();
