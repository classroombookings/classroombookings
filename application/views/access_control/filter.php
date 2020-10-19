<div class="filter-form">

	<?php

	$attrs = [
		'class' => 'cssform-stacked',
		'id' => 'access_control_filter',
		'method' => 'GET',
		// 'up-autosubmit' => '',
		'up-target' => '#access_control_list',
		'up-reveal' => 'false',
		// 'up-restore-scroll' => 'false',
		// 'up-history' => 'false',
	];

	echo form_open('access_control', $attrs);

	?>

	<div class="block-group">

		<div class="block b-20">
			<p class="input-group">
				<?php
				echo form_label('Room', 'room_id');
				$options = array('' => '(All)');
				if ($rooms) {
					foreach ($rooms as $room) {
						$options[$room->room_id] = html_escape($room->name);
					}
				}
				$value = element('room_id', $filter);
				echo form_dropdown([
					'name' => 'room_id',
					'id' => 'room_id',
					'options' => $options,
					'selected' => set_value('room_id', $value),
				]);
			?>
			</p>
		</div>

		<div class="block b-20">
			<p class="input-group">
				<?php
				echo form_label('Type', 'actor');
				$options = array('' => '(Any)');
				$options += Access_control_model::get_actors();
				$value = element('actor', $filter);
				echo form_dropdown([
					'name' => 'actor',
					'id' => 'actor',
					'options' => $options,
					'selected' => set_value('actor', $value),
					'up-switch' => '.actor-type',
				]);
			?>
			</p>
		</div>

		<div class="block b-20 actor-type" up-show-for="D">
			<p class="input-group">
				<?php
				echo form_label('Department', 'department_id');
				$options = array('' => '(Any)');
				foreach ($departments as $department) {
					$options[$department->department_id] = html_escape($department->name);
				}
				$value = element('department_id', $filter);
				echo form_dropdown([
					'name' => 'department_id',
					'id' => 'department_id',
					'options' => $options,
					'value' => set_value('department_id', $value),
				]);
			?>
			</p>
		</div>

		<div class="block b-20">
			<p class="input-group">
				<?php
				echo form_label('&nbsp;');

				echo form_button([
					'type' => 'submit',
					'content' => 'Filter',
				]);

				echo anchor('access_control', 'Clear', ['class' => 'button']);
			?>
			</p>
		</div>
 	</div>

	<?php echo form_close(); ?>

</div>
