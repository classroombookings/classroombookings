<?php
$department_id = NULL;
if (isset($department) && is_object($department)) {
	$department_id = set_value('department_id', $department->department_id);
}

echo form_open('departments/save', array('class' => 'cssform', 'id' => 'department_add'), array('department_id' => $department_id) );
?>

<fieldset>

	<legend accesskey="D" tabindex="<?= tab_index() ?>"><?= lang('department.department') ?></legend>

	<p>
		<label for="name" class="required"><?= lang('department.field.name') ?></label>
		<?php
		$field = 'name';
		$value = set_value($field, isset($department) ? $department->name : '', FALSE);
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
		<label for="description"><?= lang('department.field.description') ?></label>
		<?php
		$field = 'description';
		$value = set_value($field, isset($department) ? $department->description : '', FALSE);
		echo form_textarea(array(
			'name' => $field,
			'id' => $field,
			'columns' => '50',
			'rows' => '3',
			'maxlength' => '255',
			'tabindex' => tab_index(),
			'value' => $value,
		));
		?>
	</p>
	<?php echo form_error($field); ?>

</fieldset>

<?php

$this->load->view('partials/submit', array(
	'submit' => array(isset($department) ? lang('app.action.save') : lang('app.action.create'), tab_index()),
	'cancel' => array(lang('app.action.cancel'), tab_index(), 'departments'),
));

echo form_close();
