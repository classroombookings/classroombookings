<?php

echo $this->session->flashdata('saved');

$schedule_id = NULL;

if (isset($schedule) && is_object($schedule)) {
	$schedule_id = set_value('schedule_id', $schedule->schedule_id);
}

echo form_open(current_url(), [
	'class' => 'cssform',
	'id' => 'schedule_edit'
], ['schedule_id' => $schedule_id]);

?>

<fieldset>

	<legend accesskey="S" tabindex="<?= tab_index() ?>">Schedule details</legend>

	<p>
		<label for="name" class="required">Name</label>
		<?php
		$field = 'name';
		$value = set_value($field, isset($schedule) ? $schedule->name : '', FALSE);
		echo form_input(array(
			'name' => $field,
			'id' => $field,
			'size' => '25',
			'maxlength' => '32',
			'tabindex' => tab_index(),
			'value' => $value,
			'autofocus' => true,
		));
		?>
	</p>
	<?php echo form_error('name'); ?>

	<p>
		<label for="description">Description</label>
		<?php
		$field = 'description';
		$value = set_value($field, isset($schedule) ? $schedule->description : '', FALSE);
		echo form_textarea(array(
			'name' => $field,
			'id' => $field,
			'rows' => '5',
			'cols' => '30',
			'tabindex' => tab_index(),
			'value' => $value,
		));
		?>
	</p>
	<?php echo form_error($field) ?>


</fieldset>


<?php

$this->load->view('partials/submit', array(
	'submit' => array('Save', tab_index()),
	'cancel' => array('Cancel', tab_index(), 'schedules'),
));

echo form_close();

?>


<?php if (isset($schedule) && $schedule->type == 'periods'): ?>

<fieldset style="margin-top:30px">

	<legend accesskey="P" tabindex="<?= tab_index() ?>">Periods</legend>

	<div
		id="period_list"
		hx-get="<?= site_url('periods/index/' . $schedule->schedule_id) ?>"
		hx-trigger="load"
	>
		<p>Loading...</p>
	</div>
</fieldset>

<?php endif; ?>
