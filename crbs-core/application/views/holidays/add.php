<?php

$holiday_id = NULL;

if (isset($holiday) && is_object($holiday)) {
	$holiday_id = set_value('holiday_id', $holiday->holiday_id);
}

echo form_open(current_url(), ['class' => 'cssform', 'id' => 'holiday_add'], ['holiday_id' => $holiday_id, 'session_id' => $session->session_id] );

?>

<fieldset>

	<legend accesskey="H" tabindex="<?= tab_index() ?>">Holiday</legend>

	<p>
		<label for="name" class="required">Name</label>
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
		<label for="date_start" class="required">Start date</label>
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
		?>
		<img style="cursor:pointer" align="top" src="<?= base_url('assets/images/ui/cal_day.png') ?>" width="16" height="16" title="Choose date" onclick="displayDatePicker('date_start', false);" />
	</p>
	<?php echo form_error($field); ?>

	<p>
		<label for="date_end" class="required">End date</label>
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
		?>
		<img style="cursor:pointer" align="top" src="<?= base_url('assets/images/ui/cal_day.png') ?>" width="16" height="16" title="Choose date" onclick="displayDatePicker('date_end', false);" />
	</p>
	<?php echo form_error($field); ?>


</fieldset>

<?php

$this->load->view('partials/submit', array(
	'submit' => array('Save', tab_index()),
	'cancel' => array('Cancel', tab_index(), 'holidays/session/' . $session->session_id),
));

echo form_close();
