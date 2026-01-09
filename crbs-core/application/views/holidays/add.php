<?php

$holiday_id = NULL;

if (isset($holiday) && is_object($holiday)) {
	$holiday_id = set_value('holiday_id', $holiday->holiday_id);
}

echo form_open(current_url(), ['class' => 'cssform', 'id' => 'holiday_add'], ['holiday_id' => $holiday_id, 'session_id' => $session->session_id] );

?>

<fieldset>

	<legend accesskey="H" tabindex="<?= tab_index() ?>"><?= lang('holiday.holiday') ?></legend>

	<p>
		<label for="name" class="required"><?= lang('holiday.field.name') ?></label>
		<?php
		$field = 'name';
		$value = set_value($field, isset($holiday) ? $holiday->name : '', FALSE);
		echo form_input(array(
			'name' => $field,
			'id' => $field,
			'size' => '40',
			'maxlength' => '50',
			'tabindex' => tab_index(),
			'value' => $value,
		));
		?>
	</p>
	<?php echo form_error($field) ?>

	<p>
		<label for="date_start" class="required"><?= lang('holiday.field.date_start') ?></label>
		<?php
		$field = 'date_start';
		$value = set_value('date_start', isset($holiday) ? $holiday->date_start->format('d/m/Y') : '', FALSE);
		echo form_input(array(
			'name' => 'date_start',
			'id' => 'date_start',
			'size' => '10',
			'maxlength' => '10',
			'tabindex' => tab_index(),
			'value' => $value,
		));
		echo date_picker_img('date_start');
		?>
	</p>
	<?php echo form_error($field); ?>

	<p>
		<label for="date_end" class="required"><?= lang('holiday.field.date_end') ?></label>
		<?php
		$field = 'date_end';
		$value = set_value('date_end', isset($holiday) ? $holiday->date_end->format('d/m/Y') : '', FALSE);
		echo form_input(array(
			'name' => 'date_end',
			'id' => 'date_end',
			'size' => '10',
			'maxlength' => '10',
			'tabindex' => tab_index(),
			'value' => $value,
		));
		echo date_picker_img('date_end');
		?>
	</p>
	<?php echo form_error($field); ?>

</fieldset>

<?php

$this->load->view('partials/submit', array(
	'submit' => array(isset($holiday) ? lang('app.action.save') : lang('app.action.create'), tab_index()),
	'cancel' => array(lang('app.action.cancel'), tab_index(), 'holidays/session/' . $session->session_id),
));

echo form_close();
