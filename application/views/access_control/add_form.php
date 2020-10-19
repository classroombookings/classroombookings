<?php

$attrs = [
	'class' => 'cssform',
	'id' => 'access_control_add',
	'up-target' => '#access_control_add',
	'up-history' => 'false',
];

echo form_open('access_control/save', $attrs);

?>

<fieldset>

	<legend accesskey="A">Access Control Entry</legend>

	<?php
	echo form_hidden('target', 'R');
	?>

	<?php echo form_error('reference'); ?>

	<?php $field = 'target_id'; ?>
	<p>
		<label for="target_id">Room</label>
		<?php
		$options = array('' => '');
		if ($rooms) {
			foreach ($rooms as $room) {
				$options[$room->room_id] = html_escape($room->name);
			}
		}
		echo form_dropdown([
			'name' => 'target_id',
			'id' => 'target_id',
			'options' => $options,
			'value' => set_value('target_id'),
		]);
		?>
	</p>
	<?php echo form_error($field); ?>

	<?php $field = 'actor'; ?>
	<p data-xax="on change add .hidden to .actor-choice
			then if event.target.value == 'D' then remove .hidden from .actor-choice-D">
		<label for="who">Who</label>
		<?php
		// $options = array(
		// 	'A' => 'Any logged-in user',
		// 	'D' => 'Department',
		// );
		$options = [
			['value' => 'A', 'label' => 'Any logged-in user'],
			['value' => 'D', 'label' => 'Department'],
		];

		foreach ($options as $opt) {
			$id = "{$field}_{$opt['value']}";
			$input = form_radio(array(
				'name' => $field,
				'id' => $id,
				'value' => $opt['value'],
				'checked' => (set_value($field, 'A') == $opt['value']),
				'up-switch' => '.actor-choice',
			));
			echo "<label for='{$id}' class='ni'>{$input}{$opt['label']}</label>";
		}
		?>
	</p>
	<?php echo form_error($field); ?>

	<?php $field = 'department_id'; ?>
	<div class="actor-choice" up-show-for="D">
		<p>
			<label for="department">Department</label>
			<?php
			$options = array('' => '(None)');
			if ($departments) {
				foreach ($departments as $department) {
					$options[$department->department_id] = html_escape($department->name);
				}
			}
			$value = FALSE;
			echo form_dropdown([
				'name' => 'department_id',
				'id' => 'department_id',
				'options' => $options,
				'value' => set_value('department_id'),
			]);
			?>
		</p>
		<?php echo form_error($field); ?>
	</div>


	<div class="submit" style="border-top:0px;">
		<?php
		echo form_submit(array('value' => 'Add'));
		echo anchor('access_control?' . http_build_query($this->input->get()), 'Cancel', [
			'up-target' => "#access_control_add",
			'up-history' => "false",
		]);
		?>
	</div>

</fieldset>


<?php

echo form_close();
