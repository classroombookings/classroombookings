<?php
$period_id = NULL;
if (isset($period) && is_object($period)) {
	$period_id = set_value('period_id', $period->period_id);
}

echo form_open('periods/save', array('class' => 'cssform', 'id' => 'schoolday_add'), array('period_id' => $period_id) );

?>

<fieldset>

	<legend accesskey="R" tabindex="<?php echo tab_index() ?>">Period details</legend>

	<p>
		<label for="name" class="required">Name</label>
		<?php
		$field = 'name';
		$value = set_value($field, isset($period) ? $period->name : '', FALSE);
		echo form_input(array(
			'name' => $field,
			'id' => $field,
			'size' => '25',
			'maxlength' => '30',
			'tabindex' => tab_index(),
			'value' => $value,
		));
		?>
	</p>
	<?php echo form_error($field); ?>

	<p>
		<label for="time_start" class="required">Start time</label>
		<?php
		$field = 'time_start';
		$value = set_value($field, isset($period) ? $period->time_start : '', FALSE);
		$value = strftime('%H:%M', strtotime($value));
		echo form_input(array(
			'name' => $field,
			'id' => $field,
			'size' => '5',
			'maxlength' => '5',
			'tabindex' => tab_index(),
			'value' => $value,
		));
		?>
	</p>
	<?php echo form_error($field); ?>


	<p>
		<label for="time_end" class="required">End time</label>
		<?php
		$field = 'time_end';
		$value = set_value($field, isset($period) ? $period->time_end : '', FALSE);
		$value = strftime('%H:%M', strtotime($value));
		echo form_input(array(
			'name' => $field,
			'id' => $field,
			'size' => '5',
			'maxlength' => '5',
			'tabindex' => tab_index(),
			'value' => $value,
		));
		?>
	</p>
	<?php echo form_error($field); ?>

	<p>
		<label for="days" class="required">Days of the week</label>
		<?php
		$default_value = array();

		foreach ($days_list as $day_num => $day_name) {
			$field = "day_{$day_num}";
			echo form_hidden($field, '0');
			$input = form_checkbox(array(
				'name' => $field,
				'id' => $field,
				'value' => '1',
				'checked' => set_value($field, isset($period) ? $period->$field : ($day_num < 6)),
				'tabindex' => tab_index(),
			));
			echo "<label for='{$field}' class='ni'>{$input}{$day_name}</label>";
		}
		?>
	</p>

	<p>
		<label for="bookable">Can be booked</label>
		<?php
		$field = 'bookable';
		echo form_hidden($field, '0');
		echo form_checkbox(array(
			'name' => 'bookable',
			'id' => 'bookable',
			'value' => '1',
			'tabindex' => tab_index(),
			'checked' => set_value($field, isset($period) ? $period->bookable : 1, TRUE),
		));
		?>
		<p class="hint">Tick this box to allow bookings for this period</p>
	</p>

</fieldset>


<?php

$this->load->view('partials/submit', array(
	'submit' => array('Save', tab_index()),
	'cancel' => array('Cancel', tab_index(), 'periods'),
));

echo form_close();
