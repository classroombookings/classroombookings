<?php

echo $notice ?? '';
echo $this->session->flashdata('saved');

$room_group_id = NULL;

if (isset($group) && is_object($group)) {
	$room_group_id = set_value('room_group_id', $group->room_group_id);
}

echo form_open(current_url(), ['class' => 'cssform', 'id' => 'room_group_add'], ['room_group_id' => $room_group_id]);

?>

<fieldset>

	<legend accesskey="G" tabindex="<?= tab_index() ?>"><?= lang('room_group.room_group') ?></legend>

	<p>
		<label for="name" class="required"><?= lang('room_group.field.name') ?></label>
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
		<label for="description"><?= lang('room_group.field.description') ?></label>
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

	<legend accesskey="R" tabindex="<?= tab_index() ?>"><?= lang('room.rooms') ?></legend>

	<div><?= lang('room_group.rooms.hint') ?></div>
	<br>

	<?php

	$field = 'room_ids';

	foreach ($rooms as $_group => $_rooms) {

		if (isset($groups[$_group])) {
			$heading = html_escape($groups[$_group]->name);
		} else {
			$heading = lang('room_group.rooms.ungrouped');
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

	<?php echo form_error('room_ids[]'); ?>

</fieldset>

<?php

$this->load->view('partials/submit', array(
	'submit' => array(isset($group) ? lang('app.action.save') : lang('app.action.create'), tab_index()),
	'cancel' => array(lang('app.action.cancel'), tab_index(), 'setup/rooms/groups'),
));

echo form_close();
