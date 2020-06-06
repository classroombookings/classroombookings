<?php
$holiday_id = NULL;
if (isset($holiday) && is_object($holiday)) {
	$holiday_id = set_value('holiday_id', $holiday->holiday_id);
}

echo form_open('holidays/save', array('class' => 'cssform', 'id' => 'holiday_add'), array('holiday_id' => $holiday_id) );
?>

<fieldset>

	<legend accesskey="H" tabindex="<?= tab_index() ?>">Holiday Information</legend>

	<p>
		<label for="name" class="required">Name</label>
		<?php
		$field = 'name';
		$value = set_value($field, isset($holiday) ? $holiday->name : '', FALSE);
		echo form_input(array(
			'name' => $field,
			'id' => $field,
			'size' => '30',
			'maxlength' => '40',
			'tabindex' => tab_index(),
			'value' => $value,
		));
		?>
	</p>
	<?php echo form_error($field) ?>

	<p>
		<label for="date_start" class="required">Start Date</label>
		<?php
		$field = 'date_start';
		$default = (isset($holiday)
		            ? date('d/m/Y', strtotime($holiday->date_start))
		            : date('d/m/Y')
		        );
		$value = set_value($field, $default, FALSE);
		echo form_input(array(
			'name' => $field,
			'id' => $field,
			'size' => '10',
			'maxlength' => '10',
			'tabindex' => tab_index(),
			'value' => $value,
		));
		?>
		<img style="cursor:pointer" align="top" src="<?= base_url('assets/images/ui/cal_day.png') ?>" width="16" height="16" title="Choose date" onclick="displayDatePicker('date_start', false);" />
	</p>
	<?php echo form_error($field) ?>


	<p>
		<label for="date_start" class="required">End Date</label>
		<?php
		$field = 'date_end';
		$default = (isset($holiday)
		            ? date('d/m/Y', strtotime($holiday->date_end))
		            : date('d/m/Y')
		        );
		$value = set_value($field, $default, FALSE);
		echo form_input(array(
			'name' => $field,
			'id' => $field,
			'size' => '10',
			'maxlength' => '10',
			'tabindex' => tab_index(),
			'value' => $value,
		));
		?>
		<img style="cursor:pointer" align="top" src="<?= base_url('assets/images/ui/cal_day.png') ?>" width="16" height="16" title="Choose date" onclick="displayDatePicker('date_end', false);" />
	</p>
	<?php echo form_error($field) ?>


</fieldset>

<?php

$this->load->view('partials/submit', array(
	'submit' => array('Save', tab_index()),
	'cancel' => array('Cancel', tab_index(), 'holidays'),
));

echo form_close();
