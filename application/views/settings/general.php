<?php
echo $this->session->flashdata('saved');
echo form_open(current_url(), array('id'=>'settings', 'class'=>'cssform'));
?>


<fieldset>

	<legend accesskey="S" tabindex="<?php echo tab_index() ?>">Bookings</legend>

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
		<p class="hint">How many days in the future users can make their own bookings. Enter <span>0</span> for no restriction.</p>
	</p>
	<?php echo form_error('bia') ?>

	<p>
		<label for="num_max_bookings">Maximum active bookings</label>
		<?php
		$value = (int) set_value('num_max_bookings', element('num_max_bookings', $settings), FALSE);
		echo form_input(array(
			'name' => 'num_max_bookings',
			'id' => 'num_max_bookings',
			'size' => '5',
			'tabindex' => tab_index(),
			'value' => $value,
		));
		?>
		<p class="hint">Maximum number of active single bookings for a user. Enter <span>0</span> for no limit.</p>
		<p class="hint">'Active' is any single booking for a date and period start time in the future.</p>
	</p>
	<?php echo form_error('num_max_bookings') ?>

	<hr size="1" />

	<p id="settings_displaytype">
		<label for="displaytype">Display type</label>
		<?php

		$field = "displaytype";
		$value = set_value($field, element($field, $settings), FALSE);

		$options = [
			['value' => 'day', 'label' => 'One day at a time', 'enable' => 'd_columns_rooms'],
			['value' => 'room', 'label' => 'One room at a time', 'enable' => 'd_columns_days'],
		];

		foreach ($options as $opt) {
			$id = "{$field}_{$opt['value']}";
			$input = form_radio(array(
				'name' => $field,
				'id' => $id,
				'value' => $opt['value'],
				'checked' => ($value == $opt['value']),
				'tabindex' => tab_index(),
				'up-switch' => '.d_columns_target',
			));
			echo "<label for='{$id}' class='ni'>{$input}{$opt['label']}</label>";
		}

		?>
		<br />
		<p class="hint">Specify the main focus of the bookings page.<br />
			<strong><span>One day at a time</span></strong> - all periods and rooms are shown for the selected date.<br />
			<strong><span>One room at a time</span></strong> - all periods and days of the week are shown for the selected room.
		</p>
	</p>
	<?php echo form_error('displaytype'); ?>

	<p id="settings_columns">
		<label for="columns">Columns</label>
		<?php

		$field = 'd_columns';
		$value = set_value($field, element($field, $settings), FALSE);

		$options = [
			['value' => 'periods', 'label' => 'Periods', 'for' => ''],
			['value' => 'rooms', 'label' => 'Rooms', 'for' => 'day'],
			['value' => 'days', 'label' => 'Days', 'for' => 'room'],
		];

		foreach ($options as $opt) {
			$id = "{$field}_{$opt['value']}";
			$input = form_radio(array(
				'name' => $field,
				'id' => $id,
				'value' => $opt['value'],
				'checked' => ($value == $opt['value']),
				'tabindex' => tab_index(),
			));
			echo "<label for='{$id}' class='d_columns_target ni' up-show-for='{$opt['for']}'>{$input}{$opt['label']}</label>";
		}
		?>
		<p class="hint">Select which details you want to be displayed along the top of the bookings page.</p>
	</p>
	<?php echo form_error('d_columns') ?>

	<hr size="1" />

	<p>
		<label for="<?= $field ?>">User details</label>
		<?php

		$field = 'bookings_show_user_recurring';
		$value = set_value($field, element($field, $settings, '0'), FALSE);
		echo form_hidden($field, '0');
		$input = form_checkbox(array(
			'name' => $field,
			'id' => $field,
			'value' => '1',
			'tabindex' => tab_index(),
			'checked' => ($value == '1')
		));
		echo "<label for='{$field}' class='ni'>{$input} Show users of recurring bookings</label>";

		$field = 'bookings_show_user_single';
		$value = set_value($field, element($field, $settings, '0'), FALSE);
		echo form_hidden($field, '0');
		$input = form_checkbox(array(
			'name' => $field,
			'id' => $field,
			'value' => '1',
			'tabindex' => tab_index(),
			'checked' => ($value == '1')
		));
		echo "<label for='{$field}' class='ni'>{$input} Show users of single bookings</label>";
		?>

		<p class="hint">This setting controls the visibility of a booking's user on the Bookings page.</p>
		<p class="hint">User details are always displayed to administrators, and on user's own bookings.</p>

	</p>

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

	<legend accesskey="L" tabindex="<?php echo tab_index() ?>">Login Message</legend>

	<div>Display a custom message to users on the login page.</div>

	<?php
	$field = 'login_message_enabled';
	$value = set_value($field, element($field, $settings, '0'), FALSE);
	?>
	<p>
		<label for="<?= $field ?>">Enable</label>
		<?php
		echo form_hidden($field, '0');
		echo form_checkbox(array(
			'name' => $field,
			'id' => $field,
			'value' => '1',
			'tabindex' => tab_index(),
			'checked' => ($value == '1')
		));
		?>
	</p>

	<?php
	$field = 'login_message_text';
	$value = set_value($field, element($field, $settings, ''), FALSE);
	?>
	<p>
		<label for="<?= $field ?>">Message</label>
		<?php
		echo form_textarea(array(
			'name' => $field,
			'id' => $field,
			'rows' => '5',
			'cols' => '60',
			'tabindex' => tab_index(),
			'value' => $value,
		));
		?>
	</p>
	<?php echo form_error($field) ?>

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



<?php

$this->load->view('partials/submit', array(
	'submit' => array('Save', tab_index()),
	'cancel' => array('Cancel', tab_index(), 'controlpanel'),
));

echo form_close();
