<?php
echo isset($notice) ? $notice : '';
echo form_open_multipart(current_url(), array('class' => 'cssform', 'id' => 'install_step_config'));
?>

<fieldset>

	<legend accesskey="D" tabindex="<?php echo tab_index() ?>">Database connection details</legend>

	<p>
		<label for="hostname" class="required">Hostname</label>
		<?php
		$field = 'hostname';
		$value = set_value($field, isset($_SESSION['data'][$field]) ? $_SESSION['data'][$field] : '', FALSE);
		echo form_input(array(
			'name' => $field,
			'id' => $field,
			'size' => '20',
			'maxlength' => '50',
			'tabindex' => tab_index(),
			'value' => $value,
		));
		?>
	</p>
	<?php echo form_error($field); ?>

	<p>
		<label for="database" class="required">Database name</label>
		<?php
		$field = 'database';
		$value = set_value($field, isset($_SESSION['data'][$field]) ? $_SESSION['data'][$field] : '', FALSE);
		echo form_input(array(
			'name' => $field,
			'id' => $field,
			'size' => '20',
			'maxlength' => '50',
			'tabindex' => tab_index(),
			'value' => $value,
		));
		?>
	</p>
	<?php echo form_error($field); ?>

	<p>
		<label for="username" class="required">Username</label>
		<?php
		$field = 'username';
		$value = set_value($field, isset($_SESSION['data'][$field]) ? $_SESSION['data'][$field] : '', FALSE);
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
		<label for="password" class="required">Password</label>
		<?php
		$field = 'password';
		$value = set_value($field, isset($_SESSION['data'][$field]) ? $_SESSION['data'][$field] : '', FALSE);
		echo form_input(array(
			'type' => 'password',
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

</fieldset>


<fieldset>

	<legend accesskey="C" tabindex="<?php echo tab_index() ?>">Configuration</legend>

	<p>
		<label for="url" class="required">URL</label>
		<?php
		$default = config_item('base_url');
		$field = 'url';
		$value = set_value($field, isset($_SESSION['data'][$field]) ? $_SESSION['data'][$field] : $default, FALSE);
		echo form_input(array(
			'name' => $field,
			'id' => $field,
			'size' => '40',
			'maxlength' => '255',
			'tabindex' => tab_index(),
			'value' => $value,
		));
		?>
		<br>
		<br>
		<p class="hint">This is the web address that classroombookings will be accessed at. It must end with a forward slash /.</p>
	</p>
	<?php echo form_error($field); ?>

</fieldset>

<?php

$this->load->view('partials/submit', array(
	'submit' => array('Next', tab_index()),
	// 'cancel' => array('Cancel', tab_index(), 'users'),
));

echo form_close();
