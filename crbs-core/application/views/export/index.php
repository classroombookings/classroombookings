
<p><?= lang('export.summary') ?></p>
<br>
<?php
echo $this->session->flashdata('saved');
echo $notice ?? '';
echo form_open(current_url(), array('class' => 'cssform', 'id' => 'export'));
echo form_hidden('action', 'import');
?>

<fieldset>

	<legend accesskey="E" tabindex="<?= tab_index() ?>"><?= lang('export.export') ?></legend>

	<p class="input-group">
		<label for="session_id"><?= lang('session.session') ?></label>
		<?php
		echo form_dropdown([
			'name' => 'session_id',
			'id' => 'session_id',
			'options' => html_escape($session_options),
			'tabindex' => tab_index(),
			'selected' => set_value('session_id', ''),
		]);
		?>
	</p>

	<p class="input-group">
		<label for="room_group_id"><?= lang('room_group.room_group') ?></label>
		<?php
		echo form_dropdown([
			'name' => 'room_group_id',
			'id' => 'room_group_id',
			'options' => html_escape($room_group_options),
			'tabindex' => tab_index(),
			'selected' => set_value('room_group_id', ''),
		]);
		?>
	</p>

	<p class="input-group">
		<label for="include_cancelled"><?= lang('booking.booking_status') ?></label>
		<?php
		echo form_hidden('include_cancelled', '0');
		$value = set_value('include_cancelled', '0');
		echo "<label class='ni'>";
		echo form_checkbox(array(
			'name' => 'include_cancelled',
			'id' => 'include_cancelled',
			'value' => '1',
			'tabindex' => tab_index(),
			'checked' => $value == 1,
		));
		echo lang('export.include_cancelled_bookings');
		echo "</label>";
		?>
	</p>


</fieldset>


<?php

$this->load->view('partials/submit', array(
	'submit' => array(lang('export.export_to_csv'), tab_index()),
));

echo form_close();
