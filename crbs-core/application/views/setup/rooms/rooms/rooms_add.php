<?php

echo $notice ?? '';
echo $this->session->flashdata('saved');

$room_id = NULL;
if (isset($room) && is_object($room)) {
	$room_id = set_value('room_id', $room->room_id);
}

echo form_open_multipart(current_url(), array('class' => 'cssform', 'id' => 'rooms_add'), array('room_id' => $room_id) );

?>

<fieldset>

	<legend accesskey="R" tabindex="<?php echo tab_index() ?>"><?= lang('room.room_details') ?></legend>

	<p>
		<label for="name" class="required"><?= lang('room.field.name') ?></label>
		<?php
		$field = 'name';
		$value = set_value($field, isset($room) ? $room->name : '', FALSE);
		echo form_input(array(
			'name' => $field,
			'id' => $field,
			'size' => '20',
			'maxlength' => '20',
			'tabindex' => tab_index(),
			'value' => $value,
			'autofocus' => true,
		));
		?>
	</p>
	<?php echo form_error($field); ?>

	<p>
		<label for="room_group_id" class="required"><?= lang('room_group.group') ?></label>
		<?php
		$group_options = ['' => ''];
		foreach ($groups as $group) {
			$group_options[ $group->room_group_id ] = html_escape($group->name);
		}
		$field = 'room_group_id';
		$value = set_value($field, isset($room) ? $room->room_group_id : $group_id, FALSE);
		echo form_dropdown([
			'name' => $field,
			'id' => $field,
			'options' => $group_options,
			'selected' => $value,
			'tabindex' => tab_index(),
		]);
		?>
	</p>
	<?php echo form_error($field); ?>


	<p>
		<label for="location"><?= lang('room.field.location') ?></label>
		<?php
		$field = 'location';
		$value = set_value($field, isset($room) ? $room->location : '', FALSE);
		echo form_input(array(
			'name' => $field,
			'id' => $field,
			'size' => '30',
			'maxlength' => '40',
			'tabindex' => tab_index(),
			'value' => $value,
		));
		?>
	</p>
	<?php echo form_error($field); ?>

	<p>
		<label for="user_id"><?= lang('room.field.user_id') ?></label>
		<?php
		$userlist = array('' => '(None)');
		foreach ($users as $user) {
			$label = empty($user->displayname) ? $user->username : $user->displayname;
			$userlist[ $user->user_id ] = html_escape($label);
		}
		$field = 'user_id';
		$value = set_value($field, isset($room) ? $room->user_id : '', FALSE);
		echo form_dropdown($field, $userlist, $value, 'tabindex="'.tab_index().'"');
		?>
	</p>
	<?php echo form_error($field); ?>

	<p>
		<label for="notes"><?= lang('room.field.notes') ?></label>
		<?php
		$field = 'notes';
		$value = set_value($field, isset($room) ? $room->notes : '', FALSE);
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

	<p>
		<label for="bookable"><?= lang('room.field.bookable') ?></label>
		<?php
		$field = 'bookable';
		$value = isset($room) ? $room->bookable : '1';
		$checked = set_checkbox($field, '1', $value == '1');
		echo form_hidden($field, '0');
		echo form_checkbox(array(
			'name' => $field,
			'id' => $field,
			'value' => '1',
			'tabindex' => tab_index(),
			'checked' => $checked,
		));
		?>
		<p class="hint"><?= lang('room.field.bookable.hint') ?></p>
	</p>

</fieldset>


<fieldset>

	<legend accesskey="P" tabindex="7"><?= lang('room.field.photo') ?></legend>

	<br>
	<div><?= lang('room.field.photo.summary') ?></div>
	<br>

	<p>
		<label><?= lang('room.field.current_photo') ?></label>
		<?php
		if (isset($room) && isset($room->photo) && ! empty($room->photo)) {
			$image_url = image_url($room->photo);
			if ($image_url) {
				$img = img($image_url, false, [
					'width' => '200',
					'style' => 'width:200px;height:auto;max-width:200px;padding:1px;border:1px solid #ccc',
				]);
				echo anchor($image_url, $img, ['target' => '_blank']);
			} else {
				echo sprintf('<em>%s</em>', lang('app.none'));
			}
		} else {
			echo sprintf('<em>%s</em>', lang('app.none'));
		}
		?>
	</p>

	<p>
		<label for="userfile"><?= lang('app.upload.file_upload') ?></label>
		<?php
		echo form_upload(array(
			'name' => 'userfile',
			'id' => 'userfile',
			'size' => '30',
			'maxlength' => '255',
			'tabindex' =>tab_index(),
			'value' => '',
		));
		?>
		<br>
		<br>
		<p class="hint"><?= lang('app.upload.max_filesize') ?> <span><?php echo $max_size_human ?></span>.</p>
	</p>

	<?php
	if ($this->session->flashdata('image_error') != '' ) {
		$err = $this->session->flashdata('image_error');
		echo "<p class='hint error'><span>{$err}</span></p>";
	}
	?>

	<?php if (isset($room) && ! empty($room->photo)): ?>

	<p>
		<label for="photo_delete"><?= lang('room.field.current_photo.delete') ?></label>
		<?php
		$field = 'photo_delete';
		echo form_hidden($field, '0');
		echo form_checkbox(array(
			'name' => $field,
			'id' => $field,
			'value' => '1',
			'tabindex' => tab_index(),
			'checked' => FALSE,
		));
		?>
		<p class="hint"><?= lang('room.field.current_photo.delete.hint') ?></p>
	</p>

	<?php endif; ?>

</fieldset>


<?php if (isset($fields) && is_array($fields)): ?>

<fieldset>

	<legend accesskey="F" tabindex="<?php echo tab_index() ?>"><?= lang('custom_field.custom_fields') ?></legend>

	<?php

	foreach ($fields as $field) {

		$id = sprintf('f%s', $field->field_id);
		echo '<p>';
		echo form_label(html_escape($field->name), $id);

		switch ($field->type) {

			case Rooms_model::FIELD_TEXT:

				$input = "f{$field->field_id}";
				$value = set_value($input, element($field->field_id, $fieldvalues), FALSE);
				echo form_input(array(
					'name' => $input,
					'id' => $input,
					'size' => '30',
					'maxlength' => '255',
					'tabindex' => tab_index(),
					'value' => $value,
				));

			break;


			case Rooms_model::FIELD_SELECT:

				$input = "f{$field->field_id}";
				$value = set_value($input, element($field->field_id, $fieldvalues), FALSE);
				$options = $field->options;
				$opts = array();
				foreach ($options as $option) {
					$opts[$option->option_id] = $option->value;
				}
				echo form_dropdown([
					'name' => $input,
					'id' => $input,
					'options' => html_escape($opts),
					'selected' => $value,
					'tabindex' => tab_index(),
				]);

			break;


			case Rooms_model::FIELD_CHECKBOX:

				$input = "f{$field->field_id}";
				$checked = set_checkbox($input, '1', element($field->field_id, $fieldvalues) == '1');
				echo form_hidden($input, '0');
				echo form_checkbox(array(
					'name' => $input,
					'id' => $input,
					'value' => '1',
					'tabindex' => tab_index(),
					'checked' => $checked,
				));

			break;

		}
		echo '</p>';

	}

	?>

</fieldset>

<?php endif; ?>


<?php

$this->load->view('partials/submit', array(
	'submit' => array(isset($room) ? lang('app.action.save') : lang('app.action.create'), tab_index()),
	'cancel' => array(lang('app.action.cancel'), tab_index(), 'setup/rooms/groups/view/'.$group_id),
));

echo form_close();
