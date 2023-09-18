<?php

$room_group_id = NULL;

if (isset($group) && is_object($group)) {
	$room_group_id = set_value('room_group_id', $group->room_group_id);
}

echo form_open(current_url(), ['class' => 'cssform', 'id' => 'room_group_add'], ['room_group_id' => $room_group_id]);

?>

<fieldset>

	<legend accesskey="G" tabindex="<?= tab_index() ?>">Room Group</legend>

	<p>
		<label for="name" class="required">Name</label>
		<?php
		$field = 'name';
		$value = set_value($field, isset($group) ? $group->name : '', FALSE);
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
		<label for="description">Description</label>
		<?php
		$field = 'description';
		$value = set_value($field, isset($group) ? $group->description : '', FALSE);
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


<fieldset>

	<legend accesskey="R" tabindex="<?= tab_index() ?>">Rooms</legend>

	<div>Choose which rooms belong in this group.</div>
	<br>

	<?php

	$field = 'room_ids';

	foreach ($rooms as $_group => $_rooms) {

		if (isset($groups[$_group])) {
			$heading = html_escape($groups[$_group]->name);
		} else {
			$heading = 'Ungrouped';
		}

		echo "<p><label>{$heading}</label>";

		foreach ($_rooms as $_room) {

			$id = "{$field}_{$_room->room_id}";

			$title = html_escape($_room->name);
			$value = 0;
			if (!empty($room_group_id) && $room_group_id == $_room->room_group_id) {
				$value = 1;
			}
			$value = set_value("{$field}[{$_room->room_id}]", $value, FALSE);
			echo form_hidden("{$field}[{$_room->room_id}]", '0');
			$input = form_checkbox(array(
				'name' => "{$field}[{$_room->room_id}]",
				'id' => $id,
				'value' => '1',
				'tabindex' => tab_index(),
				'checked' => ($value == '1')
			));
			echo "<label for='{$id}' class='ni'>{$input} {$title}</label>";

		}

		echo "</p>";

	}

	?>


</fieldset>

<?php

$this->load->view('partials/submit', array(
	'submit' => array('Save', tab_index()),
	'cancel' => array('Cancel', tab_index(), 'room_groups'),
));

echo form_close();
