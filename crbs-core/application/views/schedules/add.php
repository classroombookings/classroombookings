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

	<legend accesskey="S" tabindex="<?= tab_index() ?>"><?= lang('schedule.schedule') ?></legend>

	<p>
		<label for="name" class="required"><?= lang('schedule.field.name') ?></label>
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
		<label for="description"><?= lang('schedule.field.description') ?></label>
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
	'submit' => array(isset($schedule) ? lang('app.action.save') : lang('app.action.continue'), tab_index()),
	'cancel' => array(lang('app.action.cancel'), tab_index(), 'schedules'),
));

echo form_close();

?>


<?php if (isset($schedule) && $schedule->type == 'periods'): ?>

<fieldset style="margin-top:30px">

	<legend accesskey="P" tabindex="<?= tab_index() ?>"><?= lang('period.periods') ?></legend>

	<div
		id="period_list"
		hx-get="<?= site_url('periods/index/' . $schedule->schedule_id) ?>"
		hx-trigger="load"
	>
		<p><?= lang('app.loading') ?>...</p>
	</div>
</fieldset>

<?php endif; ?>
