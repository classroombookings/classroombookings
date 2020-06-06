<?php
echo $this->session->flashdata('saved');

$iconbar = iconbar(array(
	array('weeks', 'Weeks', 'school_manage_weeks.png'),
));

echo $iconbar;

echo form_open('weeks/saveacademicyear', array('class' => 'cssform', 'id' => 'saveacademicyear') );
?>


<fieldset style="width:50%">

	<legend accesskey="A" tabindex="<?= tab_index() ?>">Academic year</legend>

	<p>
		<label for="date_start" class="required">Start date:</label>
		<?php
		$field = 'date_start';
		$value = set_value('date_start', date('d/m/Y', strtotime($academicyear->date_start)), FALSE);
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
		<label for="date_end" class="required">End date:</label>
		<?php
		$field = 'date_end';
		$value = set_value($field, date('d/m/Y', strtotime($academicyear->date_end)), FALSE);
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
	'cancel' => array('Cancel', tab_index(), 'weeks'),
));

echo form_close();
