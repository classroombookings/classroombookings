<?php

$booking_id = NULL;
if (isset($booking) && is_object($booking)) {
	$booking_id = set_value('booking_id', $booking->booking_id);
}

echo isset($notice) ? $notice : '';

echo $this->session->flashdata('saved');

echo form_open('bookings/save?' . http_build_query($this->input->get()), array('id'=>'bookings_book', 'class'=>'cssform'), $hidden);

// Output the date value as d/m/Y format - as this is the expected format of the form processing part
if (isset($booking) && ! empty($booking->date)) {
	echo form_hidden('date', date('d/m/Y', strtotime($booking->date)));
}

?>


<fieldset>

	<legend accesskey="I" tabindex="<?php echo tab_index() ?>">Booking Information</legend>

	<p>
		<label>Use:</label>
		<?php
		$field = 'notes';
		$value = set_value($field, isset($booking) ? $booking->notes : '', FALSE);
		echo form_input(array(
			'name' => $field,
			'id' => $field,
			'size' => '50',
			'maxlength' => '100',
			'tabindex' => tab_index(),
			'value' => $value,
		));
		?>
	</p>
	<?php echo form_error($field); ?>


	<?php if ($this->userauth->is_level(ADMINISTRATOR)): ?>


	<p>
		<label>Date:</label>
		<?php
		$field = 'date';
		$default = '';
		if ( ! empty($booking->date)) {
			$default = date('d/m/Y', strtotime($booking->date));
		}
		$value = set_value($field, isset($booking) ? $default : '', FALSE);
		echo form_input(array(
			'name' => $field,
			'id' => $field,
			'size' => '10',
			'maxlength' => '10',
			'tabindex' => tab_index(),
			'value' => $value,
		));
		?>
	</p>
	<?php echo form_error($field); ?>


	<p>
		<label for="room_id" class="required">Room:</label>
		<?php
		$room_options = array();
		foreach ($rooms as $room) {
			$room_options[ $room->room_id ] = html_escape($room->name);
		}
		$field = 'room_id';
		$value = set_value($field, isset($booking) ? $booking->room_id : '', FALSE);
		echo form_dropdown('room_id', $room_options, $value, 'tabindex="' . tab_index() . '"');
		?>
	</p>
	<?php echo form_error($field) ?>


	<p>
		<label for="period_id" class="required">Period:</label>
		<?php
		$time_fmt = setting('time_format_period');
		$period_options = array();
		foreach ($periods as $period) {
			$label = sprintf("%s (%s - %s)",
				$period->name,
				date($time_fmt, strtotime($period->time_start)),
				date($time_fmt, strtotime($period->time_end)));
			$period_options[ $period->period_id ] = html_escape($label);
		}
		$field = 'period_id';
		$value = set_value($field, isset($booking) ? $booking->period_id : '', FALSE);
		echo form_dropdown('period_id', $period_options, $value, 'tabindex="' . tab_index() . '"');
		?>
	</p>
	<?php echo form_error($field) ?>


	<p>
		<label for="user_id">User:</label>
		<?php
		$user_options = array('' => '(None)');
		foreach ($users as $user) {
			$label = ($user->displayname ? $user->displayname : $user->username);
			$user_options[ $user->user_id ] = html_escape($label);
		}
		$field = 'user_id';
		$value = set_value($field, isset($booking) ? $booking->user_id : $this->userauth->user->user_id, FALSE);
		echo form_dropdown('user_id', $user_options, $value, 'id="user_id" tabindex="' . tab_index() . '"');
		?>
	</p>
	<?php echo form_error($field) ?>


	<?php endif; ?>


</fieldset>


<?php if ($this->userauth->is_level(ADMINISTRATOR)): ?>


<fieldset>

	<legend accesskey="R" tabindex="<?php echo tab_index() ?>">Recurring options</legend>

	<p>
		<label for="recurring">Recurring?</label>
		<?php
		$field = 'recurring';
		$value = (isset($booking) && isset($booking->booking_id) && $booking->day_num) ? '1' : '0';
		echo form_hidden($field, '0');
		echo form_checkbox(array(
			'name' => $field,
			'id' => $field,
			'value' => '1',
			'tabindex' => tab_index(),
			'checked' => ($value == 1),
			'up-switch' => '.recurring_fields',
		));
		?>
	</p>
	<?php echo form_error($field) ?>

	<p class="recurring_fields" up-show-for=":checked">
		<label for="week_id">Week:</label>
		<?php
		$week_options = array('' => '(None)');
		foreach ($weeks as $week) {
			$week_options[ $week->week_id ] = html_escape($week->name);
		}
		$field = 'week_id';
		$value = set_value($field, isset($booking) ? $booking->week_id : '', FALSE);
		echo form_dropdown('week_id', $week_options, $value, 'id="week_id" tabindex="' . tab_index() . '"');
		?>
	</p>
	<?php echo form_error($field) ?>

	<p class="recurring_fields" up-show-for=":checked">
		<label for="day_num">Day:</label>
		<?php
		$day_options = array('' => '(None)');
		$day_options += $days;
		$field = 'day_num';
		$value = set_value($field, isset($booking) ? $booking->day_num : '', FALSE);
		echo form_dropdown('day_num', $day_options, $value, 'id="day_num" tabindex="' . tab_index() . '"');
		?>
	</p>
	<?php echo form_error($field) ?>

</fieldset>

<?php endif; ?>


<?php
$save_label = empty($booking_id) ? 'Book' : 'Save';
$this->load->view('partials/submit', array(
	'submit' => array($save_label, tab_index()),
	'cancel' => array('Cancel', tab_index(), $cancel_uri),
));

echo form_close();
