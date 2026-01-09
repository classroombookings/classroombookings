<p>
	<label for="context_id"><?= lang('role.role') ?></label>
	<?php
	$field = 'context_id';
	$value = set_value($field, '', false);
	echo form_dropdown([
		'name' => 'context_id',
		'options' => html_escape($role_options),
		'selected' => $value,
	]);
	?>
</p>
<?php echo form_error($field); ?>
