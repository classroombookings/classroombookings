<?php
echo $this->session->flashdata('saved');
echo form_open('settings', array('id'=>'settings', 'class'=>'cssform'));
?>


<fieldset>

	<legend accesskey="S" tabindex="<?php echo tab_index() ?>">Settings</legend>

	<p>
		<label for="bia">Booking in advance</label>
		<?php
		$value = (int) set_value('bia', element('bia', $settings), FALSE);
		echo form_input(array(
			'name' => 'bia',
			'id' => 'bia',
			'size' => '5',
			'maxlength' => '3',
			'tabindex' => tab_index(),
			'value' => $value,
		));
		?>
		<p class="hint">How many days in the future users can make their own bookings. Enter 0 for no restriction.</p>
	</p>
	<?php echo form_error('bia') ?>

	<hr size="1" />

	<p>
		<label for="displaytype">Bookings display type</label>
		<?php
		$displaytype = set_value('displaytype', element('displaytype', $settings), FALSE);
		$options = array(
			'day' => 'One day at a time',
			'room' => 'One room at a time',
		);
		echo form_dropdown(
			'displaytype',
			$options,
			$displaytype,
			' id="displaytype" tabindex="' . tab_index() . '"'
		);
		?>
		<p class="hint">Specify the main focus of the bookings page.<br />
			<strong><span>One day at a time</span></strong> - all periods and rooms are shown for the selected date.<br />
			<strong><span>One room at a time</span></strong> - all periods and days of the week are shown for the selected room.
		</p>
	</p>
	<?php echo form_error('displaytype'); ?>

	<p>
		<label for="columns">Bookings columns</label>
		<?php
		$columns = set_value('d_columns', element('d_columns', $settings), FALSE);
		?>
		<select name="d_columns" id="d_columns" tabindex="<?php echo tab_index() ?>">
			<option value="periods" class="day room" <?= $columns == 'periods' ? 'selected="selected"' : '' ?>>Periods</option>
			<option value="rooms" class="day" <?= $columns == 'rooms' ? 'selected="selected"' : '' ?>>Rooms</option>
			<option value="days" class="room" <?= $columns == 'days' ? 'selected="selected"' : '' ?>>Days</option>
		</select>
		<p class="hint">Select which details you want to be displayed along the top of the bookings page.</p>
	</p>
	<?php echo form_error('d_columns') ?>

</fieldset>




<fieldset>

	<legend accesskey="D" tabindex="<?php echo tab_index() ?>">Date formats</legend>

	<div>
		Dates follow the PHP format - <a href="https://www.php.net/manual/en/function.date.php#refsect1-function.date-parameters" target="_blank">view reference</a>.
	</div>

	<p>
		<label for="date_format_long">Long date format</label>
		<?php
		$value = set_value('date_format_long', element('date_format_long', $settings), FALSE);
		echo form_input(array(
			'name' => 'date_format_long',
			'id' => 'date_format_long',
			'size' => '15',
			'maxlength' => '10',
			'tabindex' => tab_index(),
			'value' => $value,
		));
		?>
		<p class="hint">Long date format displayed at the top of the bookings page.</p>
	</p>
	<?php echo form_error('date_format_long') ?>

	<p>
		<label for="date_format_weekday">Weekday date format</label>
		<?php
		$value = set_value('date_format_weekday', element('date_format_weekday', $settings), FALSE);
		echo form_input(array(
			'name' => 'date_format_weekday',
			'id' => 'date_format_weekday',
			'size' => '15',
			'maxlength' => '10',
			'tabindex' => tab_index(),
			'value' => $value,
		));
		?>
		<p class="hint">Short date format for a specific weekday.</p>
	</p>
	<?php echo form_error('date_format_weekday') ?>

	<p>
		<label for="time_format_period">Period time format</label>
		<?php
		$value = set_value('time_format_period', element('time_format_period', $settings), FALSE);
		echo form_input(array(
			'name' => 'time_format_period',
			'id' => 'time_format_period',
			'size' => '15',
			'maxlength' => '10',
			'tabindex' => tab_index(),
			'value' => $value,
		));
		?>
		<p class="hint">Time format for periods.</p>
	</p>
	<?php echo form_error('time_format_period') ?>


</fieldset>


<fieldset>

	<legend accesskey="M" tabindex="<?php echo tab_index() ?>">Maintenance Mode</legend>

	<div>Enabling Maintenance Mode prevents Teacher user accounts from viewing and making bookings. All users can still log in to make changes to their own account or change their password.</div>

	<p>
		<label for="maintenance_mode">Maintenance Mode</label>
		<?php
		$value = set_value('maintenance_mode', element('maintenance_mode', $settings, '0'), FALSE);
		echo form_hidden('maintenance_mode', '0');
		echo form_checkbox(array(
			'name' => 'maintenance_mode',
			'id' => 'maintenance_mode',
			'value' => '1',
			'tabindex' => tab_index(),
			'checked' => ($value == '1')
		));
		?>
	</p>


	<p>
		<label for="maintenance_mode_message">Message</label>
		<?php
		$field = 'maintenance_mode_message';
		$value = set_value($field, element($field, $settings, ''), FALSE);
		echo form_textarea(array(
			'name' => $field,
			'id' => $field,
			'rows' => '5',
			'cols' => '60',
			'tabindex' => tab_index(),
			'value' => $value,
		));
		?>
		<p class="hint">This is the message that will be displayed during maintenance mode.</p>
	</p>
	<?php echo form_error($field) ?>

</fieldset>


<script type="text/javascript">
Q.push(function() {
	dynamicSelect('displaytype', 'd_columns');
});
</script>


<?php

$this->load->view('partials/submit', array(
	'submit' => array('Save', tab_index()),
	'cancel' => array('Cancel', tab_index(), 'controlpanel'),
));

echo form_close();
