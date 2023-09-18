<?php

$messages = $this->session->flashdata('saved');
echo "<div class='messages'>{$messages}</div>";

$attrs = [
	'class' => 'cssform',
	'id' => 'room_schedules_save',
];
$form_open = form_open('room_schedules/save/' . $session->session_id, $attrs);
$form_close = form_close();

?>

<?= $form_open ?>

<fieldset>

	<legend accesskey="S" tabindex="<?php echo tab_index() ?>">Schedules</legend>

	<?php
	$schedule_options = ['' => ''];
	foreach ($schedules as $schedule) {
		$schedule_options[ $schedule->schedule_id ] = html_escape($schedule->name);
	}

	// Render an input row for each room group.

	foreach ($room_groups as $group) {

		$field_name = sprintf('group_schedule[%d][room_group_id]', $group->room_group_id);
		$hidden = form_hidden($field_name, $group->room_group_id);

		$field_id = sprintf('group_%d_schedule', $group->room_group_id);
		$field_name = sprintf('group_schedule[%d][schedule_id]', $group->room_group_id);

		$data_key = sprintf('session_%d_group_%d', $session->session_id, $group->room_group_id);
		$data_val = isset($session_schedules[$data_key]) ? $session_schedules[$data_key] : '';
		$value = set_value($field_name, $data_val, FALSE);
		$input = form_dropdown([
			'name' => $field_name,
			'id' => $field_id,
			'options' => $schedule_options,
			'selected' => $value,
			'tabindex' => tab_index(),
		]);

		$label = form_label(html_escape($group->name), $field_id);

		echo "<p>{$label}{$hidden}{$input}</p>";
		echo form_error($field_name);

	}
	?>


</fieldset>


<?php

$this->load->view('partials/submit', array(
	'submit' => array('Save', tab_index()),
	// 'cancel' => array('Cancel', tab_index(), 'rooms'),
));

echo $form_close;
