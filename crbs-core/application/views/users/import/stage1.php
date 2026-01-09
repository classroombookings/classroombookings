<?php
echo $this->session->flashdata('saved');
echo $notice ?? '';
echo form_open_multipart(current_url(), array('class' => 'cssform', 'id' => 'user_import'));
echo form_hidden('action', 'import');
?>

<fieldset class="cssform-stacked">

	<legend accesskey="I" tabindex="<?= tab_index() ?>"><?= lang('user.import.source') ?></legend>

	<p class="input-group">
		<label for="userfile" class="required"><?= lang('user.import.csv_file') ?></label>
		<?php
		echo form_upload(array(
			'name' => 'userfile',
			'id' => 'userfile',
			'size' => '40',
			'maxlength' => '255',
			'tabindex' => tab_index(),
			'value' => '',
			'accept' => '.csv,text/x-comma-separated-values,text/comma-separated-values,application/octet-stream,application/vnd.ms-excel,application/x-csv,text/x-csv,text/csv,application/csv,application/excel,application/vnd.msexcel,text/plain',
		));
		?>
		<p class="hint"><?= lang('app.upload.max_filesize') ?> <span><?php echo $max_size_human ?></span>.</p>
	</p>


</fieldset>



<fieldset>

	<legend accesskey="F"><?= lang('user.import.default_values') ?></legend>

	<div><?= lang('user.import.default_values.hint') ?></div>

	<p class="input-group">
		<label for="password"><?= lang('user.field.password') ?></label>
		<?php
		$value = set_value('password', '');
		echo form_password(array(
			'name' => 'password',
			'id' => 'password',
			'size' => '20',
			'tabindex' => tab_index(),
			'value' => $value,
		));
		?>
	</p>

	<p class="input-group">
		<label for="role_id"><?= lang('role.role') ?></label>
		<?php
		$options = array('' => sprintf('(%s)', lang('app.none')));
		$value = set_value('role_id', '', FALSE);
		echo form_dropdown([
			'name' => 'role_id',
			'id' => 'role_id',
			'options' => $role_options,
			'selected' => $value,
			'tabindex' => tab_index(),
		]);
		?>
	</p>

	<p class="input-group">
		<label for="department_id"><?= lang('department.department') ?></label>
		<?php
		$options = array('' => sprintf('(%s)', lang('app.none')));
		$value = set_value('department_id', '', FALSE);
		echo form_dropdown([
			'name' => 'department_id',
			'id' => 'department_id',
			'options' => $department_options,
			'selected' => $value,
			'tabindex' => tab_index(),
		]);
		?>
	</p>


	<p class="input-group">
		<label for="enabled"><?= lang('user.field.enabled') ?></label>
		<?php
		echo form_hidden('enabled', '0');
		$value = set_value('enabled', '1');
		echo form_checkbox(array(
			'name' => 'enabled',
			'id' => 'enabled',
			'value' => '1',
			'tabindex' => tab_index(),
			'checked' => $value == 1,
		));
		?>
	</p>


	<p class="input-group">
		<label for="force_password_reset"><?= lang('user.field.force_password_reset') ?></label>
		<?php
		echo form_hidden('force_password_reset', '0');
		$value = set_value('force_password_reset', '1');
		echo form_checkbox(array(
			'name' => 'force_password_reset',
			'id' => 'force_password_reset',
			'value' => '1',
			'tabindex' => tab_index(),
			'checked' => $value == 1,
		));
		?>
	</p>


</fieldset>

<?php

$this->load->view('partials/submit', array(
	'submit' => array(lang('user.import.create_accounts'), tab_index()),
	'cancel' => array(lang('app.action.cancel'), tab_index(), 'users'),
));

echo form_close();
