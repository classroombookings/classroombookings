<div class="filter-form add-bottom">

	<?php

	$attrs = [
		'class' => 'cssform-stacked',
		'id' => 'users_filter',
		'method' => 'GET',
	];

	echo form_open('users', $attrs);

	?>

	<div class="block-group">

		<div class="block b-20">
			<p class="input-group">
				<?php
				echo form_label(lang('app.search'), 'q');
				echo form_input([
					'name' => 'q',
					'id' => 'q',
					'value' => $filter['search'] ?? '',
				]);
			?>
			</p>
		</div>

		<div class="block b-20">
			<p class="input-group">
				<?php
				echo form_label(lang('role.role'), 'role_id');
				$options = array('' => '(Any)');
				$value = element('role_id', $filter);
				echo form_dropdown([
					'name' => 'role_id',
					'id' => 'role_id',
					'options' => $role_options,
					'selected' => $filter['role_id'] ?? '',
				]);
			?>
			</p>
		</div>

		<div class="block b-20">
			<p class="input-group">
				<?php
				echo form_label(lang('department.department'), 'department_id');
				$options = array('' => '(Any)');
				$value = element('department_id', $filter);
				echo form_dropdown([
					'name' => 'department_id',
					'id' => 'department_id',
					'options' => $department_options,
					'selected' => $filter['department_id'] ?? '',
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
					'content' => lang('app.filter'),
				]);

				echo anchor('users', lang('app.clear'), ['class' => 'button']);
			?>
			</p>
		</div>
 	</div>

	<?php echo form_close(); ?>

</div>
