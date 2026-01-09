<?php

echo $this->session->flashdata('saved');

echo validation_errors();

$user_id = NULL;
if (isset($user) && is_object($user)) {
	$user_id = set_value('user_id', $user->user_id);
}

echo form_open(current_url(), array('class' => 'cssform', 'id' => 'users_add'), array('user_id' => $user_id) );

?>

<fieldset>

	<legend accesskey="U" tabindex="<?php echo tab_index() ?>"><?= lang('user.user_details') ?></legend>

	<p>
		<label for="username" class="required"><?= lang('user.field.username') ?></label>
		<?php
		$field = 'username';
		$value = set_value($field, isset($user) ? $user->username : '', FALSE);
		echo form_input(array(
			'name' => $field,
			'id' => $field,
			'size' => '20',
			'maxlength' => '100',
			'tabindex' => tab_index(),
			'value' => $value,
		));
		?>
	</p>
	<?php echo form_error($field); ?>


	<p>
		<label for="role_id"><?= lang('role.role') ?></label>
		<?php
		$field = 'role_id';
		$options = array('' => sprintf('(%s)', lang('app.none')));
		$value = set_value($field, isset($user) ? $user->role_id : '', FALSE);
		echo form_dropdown([
			'name' => 'role_id',
			'id' => 'role_id',
			'options' => $role_options,
			'selected' => $value,
			'tabindex' => tab_index(),
		]);
		?>
	</p>
	<?php echo form_error($field); ?>

	<p>
		<label for="enabled"><?= lang('user.field.enabled') ?></label>
		<?php
		$field = 'enabled';
		$value = isset($user) ? $user->enabled : '1';
		$checked = set_checkbox($field, '1', $value == '1');
		echo form_hidden($field, '0');
		echo form_checkbox(array(
			'name' => $field,
			'id' => $field,
			'value' => '1',
			'tabindex' => tab_index(),
			'checked' => $checked,
		));
		?>
	</p>

	<p>
		<label for="email"><?= lang('user.field.email') ?></label>
		<?php
		$field = 'email';
		$value = set_value($field, isset($user) ? $user->email : '', FALSE);
		echo form_input(array(
			'name' => $field,
			'id' => $field,
			'size' => '35',
			'maxlength' => '255',
			'tabindex' => tab_index(),
			'value' => $value,
		));
		?>
	</p>
	<?php echo form_error($field); ?>

</fieldset>


<fieldset>

	<legend accesskey="C" tabindex="<?php echo tab_index() ?>"><?= lang('constraint.constraints') ?></legend>

	<div><?= lang('constraint.user.hint.1') ?></div><br>
	<div><?= lang('constraint.user.hint.2') ?></div><br>
	<div><?= lang('constraint.user.hint.3') ?></div><br>

	<?php

	$type_options = [
		['value' => 'R', 'label' => lang('constraint.type.R')],
		['value' => 'X', 'label' => lang('constraint.type.X')],
		['value' => 'U', 'label' => lang('constraint.type.U')],
	];

	foreach ($constraints_config as $id => $config) {

		extract($config);

		$main_label = form_label(html_escape($label), "constraints_{$id}_type_R");

		$switch_ref = "sw_{$id}_other";

		// Type
		//
		$type_prop = "{$id}_type";
		$type_field_name = "constraints[{$type_prop}]";
		$type_value = set_value($type_field_name, isset($constraints) ? $constraints->{$type_prop} : 'R');

		$type_input_html = '';
		foreach ($type_options as $opt) {
			$label = $opt['label'];
			if ($opt['value'] == 'R') {
				// Role - format label
				if (isset($role) && !empty($role)) {
					$raw_value = $role->{$id};
					$value = ($raw_value == null) ? 'Not set' : $raw_value;
					$role_fmt = lang('constraint.user.inherit_role_hint');
					$label = sprintf($role_fmt, $role->name, $value);
				}
			}
			$type_field_id = "constraints_{$id}_type_{$opt['value']}";
			$input = form_radio(array(
				'name' => $type_field_name,
				'id' => $type_field_id,
				'value' => $opt['value'],
				'checked' => ($type_value == $opt['value']),
				'up-switch' => ".{$switch_ref}",
			));
			$type_input_html .= "<label for='{$type_field_id}' class='ni'>{$input}{$label}</label>";
		}

		// Other Value
		//
		$value_prop = "{$id}_value";
		$value_field_name = "constraints[{$value_prop}]";
		$value_field_id = "constraints_{$value_prop}";
		$value_value = set_value($value_field_name, isset($constraints) ? $constraints->{$value_prop} : '');
		$value_input_html = '';
		$value_input_html .= form_input(array(
			'class' => $switch_ref,
			'up-show-for' => 'U',
			'name' => $value_field_name,
			'id' => $value_field_id,
			'size' => '15',
			'tabindex' => tab_index(),
			'value' => $value_value,
		));

		echo "<p>{$main_label}{$type_input_html}{$value_input_html}</p>";
		echo form_error($type_field_name);
		echo form_error($value_field_name);
	}
	?>

</fieldset>


<fieldset>

	<legend accesskey="P" tabindex="<?php echo tab_index() ?>"><?= lang('user.field.password') ?></legend>

	<?php if (isset($user)): ?>
	<div><?= lang('user.password.hint') ?></div>
	<br>
	<?php endif; ?>

	<p>
		<label for="password1" class="required"><?= lang('user.field.password') ?></label>
		<?php
		$field = 'password1';
		echo form_password(array(
			'name' => $field,
			'id' => $field,
			'size' => '20',
			'tabindex' => tab_index(),
			'value' => '',
		));
		?>
	</p>
	<p class="hint"><?= lang('user.field.password.hint') ?></p>
	<?php echo form_error($field); ?>

	<p>
		<label for="password2" class="required"><?= lang('user.field.password2') ?></label>
		<?php
		$field = 'password2';
		echo form_password(array(
			'name' => $field,
			'id' => $field,
			'size' => '20',
			'tabindex' => tab_index(),
			'value' => '',
		));
		?>
	</p>
	<?php echo form_error($field); ?>

	<p>
		<label for="force_password_reset"><?= lang('user.field.force_password_reset') ?></label>
		<?php
		$field = 'force_password_reset';
		$value = isset($user) ? $user->force_password_reset : '1';
		$checked = set_checkbox($field, '1', $value == '1');
		echo form_hidden($field, '0');
		echo form_checkbox(array(
			'name' => $field,
			'id' => $field,
			'value' => '1',
			'tabindex' => tab_index(),
			'checked' => $checked,
		));
		?>
	</p>

</fieldset>


<fieldset>

	<legend accesskey="P" tabindex="<?php echo tab_index() ?>"><?= lang('user.personal_details') ?></legend>

	<p>
		<label for="firstname"><?= lang('user.field.firstname') ?></label>
		<?php
		$field = 'firstname';
		$value = set_value($field, isset($user) ? $user->firstname : '', FALSE);
		echo form_input(array(
			'name' => $field,
			'id' => $field,
			'size' => '20',
			'maxlength' => '20',
			'tabindex' => tab_index(),
			'value' => $value,
		));
		?>
	</p>
	<?php echo form_error($field); ?>

	<p>
		<label for="lastname"><?= lang('user.field.lastname') ?></label>
		<?php
		$field = 'lastname';
		$value = set_value($field, isset($user) ? $user->lastname : '', FALSE);
		echo form_input(array(
			'name' => $field,
			'id' => $field,
			'size' => '20',
			'maxlength' => '20',
			'tabindex' => tab_index(),
			'value' => $value,
		));
		?>
	</p>
	<?php echo form_error($field); ?>

	<p>
		<label for="displayname"><?= lang('user.field.displayname') ?></label>
		<?php
		$field = 'displayname';
		$value = set_value($field, isset($user) ? $user->displayname : '', FALSE);
		echo form_input(array(
			'name' => $field,
			'id' => $field,
			'size' => '20',
			'maxlength' => '20',
			'tabindex' => tab_index(),
			'value' => $value,
		));
		?>
	</p>
	<?php echo form_error($field); ?>

	<p>
		<label for="department_id"><?= lang('department.department') ?></label>
		<?php
		$value = set_value($field, isset($user) ? $user->department_id : '', FALSE);
		echo form_dropdown([
			'name' => 'department_id',
			'id' => 'department_id',
			'options' => $department_options,
			'selected' => $value,
			'tabindex' => tab_index(),
		]);
		?>
	</p>
	<?php echo form_error($field); ?>

	<p>
		<label for="ext"><?= lang('user.field.ext') ?></label>
		<?php
		$field = 'ext';
		$value = set_value($field, isset($user) ? $user->ext : '', FALSE);
		echo form_input(array(
			'name' => $field,
			'id' => $field,
			'size' => '10',
			'maxlength' => '10',
			'tabindex' => tab_index(),
			'value' => $value,
		));
		?>
	</p>
	<?php echo form_error($field); ?>

</fieldset>


<?php

$this->load->view('partials/submit', array(
	'submit' => array(isset($role) ? lang('app.action.save') : lang('app.action.create'), tab_index()),
	'cancel' => array(lang('app.action.cancel'), tab_index(), 'users'),
));

echo form_close();
