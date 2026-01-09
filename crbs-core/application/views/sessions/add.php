<?php

$messages = $this->session->flashdata('saved');
echo "<div class='messages'>{$messages}</div>";

$session_id = NULL;

if (isset($session) && is_object($session)) {
	$session_id = set_value('session_id', $session->session_id);
}

echo form_open(current_url(), ['class' => 'cssform', 'id' => 'session_add'], ['session_id' => $session_id]);

?>

<fieldset>

	<legend accesskey="S" tabindex="<?= tab_index() ?>"><?= lang('session.session') ?></legend>

	<p>
		<label for="name" class="required"><?= lang('session.field.name') ?></label>
		<?php
		$field = 'name';
		$value = set_value($field, isset($session) ? $session->name : '', FALSE);
		echo form_input(array(
			'name' => $field,
			'id' => $field,
			'size' => '25',
			'maxlength' => '50',
			'tabindex' => tab_index(),
			'value' => $value,
		));
		?>
	</p>
	<?php echo form_error('name'); ?>

	<p>
		<label for="date_start" class="required"><?= lang('session.field.date_start') ?></label>
		<?php
		$field = 'date_start';
		$value = set_value('date_start', isset($session) ? $session->date_start->format('d/m/Y') : '', FALSE);
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
		<label for="date_end" class="required"><?= lang('session.field.date_end') ?></label>
		<?php
		$field = 'date_end';
		$value = set_value('date_end', isset($session) ? $session->date_end->format('d/m/Y') : '', FALSE);
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

	<p>
		<label for="is_selectable"><?= lang('session.field.is_selectable') ?></label>
		<?php
		$field = 'is_selectable';
		$value = isset($session) ? $session->is_selectable : '0';
		$checked = set_checkbox($field, '1', $value == '1');
		echo form_hidden($field, '0');
		echo form_checkbox(array(
			'name' => $field,
			'id' => $field,
			'value' => '1',
			'tabindex' => tab_index(),
			'checked' => $checked,
		));
		?>
		<p class="hint"><?= lang('session.field.is_selectable.hint') ?></p>
	</p>
	<?php echo form_error($field); ?>


	<p>
		<label for="default_schedule_id"><?= lang('session.field.default_schedule_id') ?></label>
		<?php
		$schedule_options = ['' => ''];
		if (is_array($schedules)) {
			foreach ($schedules as $schedule) {
				$schedule_options[ $schedule->schedule_id ] = html_escape($schedule->name);
			}
		}
		$field = 'default_schedule_id';
		$value = set_value($field, isset($session) ? $session->default_schedule_id : '', FALSE);
		echo form_dropdown([
			'name' => $field,
			'id' => $field,
			'options' => $schedule_options,
			'selected' => $value,
			'tabindex' => tab_index(),
		]);
		?>
	</p>
	<?php echo form_error($field); ?>


</fieldset>


<?php

$this->load->view('partials/submit', array(
	'submit' => array(isset($session) ? lang('app.action.save') : lang('app.action.create'), tab_index()),
	'cancel' => isset($session) ? null : array(lang('app.action.cancel'), tab_index(), 'sessions'),
));

echo form_close();
