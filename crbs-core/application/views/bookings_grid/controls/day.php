<?php

if (feature('room_groups')) {

	$date_url = site_url('bookings/filter/date') . '?' . http_build_query($query_params);
	$long_date = $datetime->format(setting('date_format_long'));
	$date_img = img('assets/images/ui/cal_day.png');
	$date_text = $long_date;
	$date_label = "<span>{$date_img} {$date_text}</span> <span>&#x25BC;</span>";
	$date_button = "<button
		type='button'
		class='filter-button'
		up-layer='new popup'
		up-size='medium'
		up-href='$date_url'
		up-history='false'
		up-target='.bookings-filter'
		up-preload=''
	>{$date_label}</button>";

}
?>

<?php if (isset($date_button)): ?>

	<?= $date_button ?>

<?php else: ?>

	<?php
	echo form_open($form_action, ['method' => 'get', 'id' => 'bookings_controls_day'], $query_params);
	?>

	<table>
		<tr>
			<td valign="middle"><label for="chosen_date"><strong>Date:</strong></label></td>
			<td valign="middle">
				<?php
				echo form_input(array(
					'class' => 'up-datepicker-input',
					'name' => 'date',
					'id' => 'date',
					'size' => '10',
					'maxlength' => '10',
					'tabindex' => tab_index(),
					'value' => $datetime ? $datetime->format('d/m/Y') : $this->input->get('date'),
				));
				?>
			</td>
			<td valign="middle">
				<?php
				echo img([
					'style' => 'cursor:pointer',
					'align' => 'top',
					'src' => base_url('assets/images/ui/cal_day.png'),
					'width' => 16,
					'height' => 16,
					'title' => 'Choose date',
					'class' => 'up-datepicker',
					'up-data' => html_escape(json_encode(['input' => 'date'])),
				]);
				?>
			</td>
			<td> &nbsp; <input type="submit" value=" Load " /></td>
		</tr>
	</table>

	<?= form_close() ?>

<?php endif; ?>
