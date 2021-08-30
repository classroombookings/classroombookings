
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

<br />
