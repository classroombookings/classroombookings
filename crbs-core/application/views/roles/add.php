<?php

$role_id = NULL;

if (isset($role) && is_object($role)) {
	$role_id = set_value('role_id', $role->role_id);
}

echo form_open(current_url(), ['class' => 'cssform', 'id' => 'role_add'], ['role_id' => $role_id]);

?>

<fieldset>

	<legend accesskey="R" tabindex="<?= tab_index() ?>"><?= lang('role.role') ?></legend>

	<p>
		<label for="name" class="required"><?= lang('role.field.name') ?></label>
		<?php
		$field = 'name';
		$value = set_value($field, isset($role) ? $role->name : '', FALSE);
		echo form_input(array(
			'name' => $field,
			'id' => $field,
			'size' => '25',
			'maxlength' => '32',
			'tabindex' => tab_index(),
			'value' => $value,
		));
		?>
	</p>
	<?php echo form_error('name'); ?>

	<p>
		<label for="description"><?= lang('role.field.description') ?></label>
		<?php
		$field = 'description';
		$value = set_value($field, isset($role) ? $role->description : '', FALSE);
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

	<legend accesskey="C" tabindex="<?= tab_index() ?>"><?= lang('role.constraints') ?></legend>

	<div><?= lang('role.constraints.hint.1') ?></div><br>
	<div><?= lang('role.constraints.hint.2') ?></div><br>

	<p>
		<label for="max_active_bookings"><?= lang('constraint.max_active_bookings') ?></label>
		<?php
		$field = 'max_active_bookings';
		$value = set_value($field, isset($role) ? $role->{$field} : '', FALSE);
		echo form_input(array(
			'name' => $field,
			'id' => $field,
			'size' => '15',
			'tabindex' => tab_index(),
			'value' => $value,
		));
		?>
		<p class="hint"><?= lang('constraint.max_active_bookings.hint') ?></p>
	</p>
	<?php echo form_error('max_active_bookings'); ?>

	<p>
		<label for="range_min"><?= lang('constraint.range_min') ?></label>
		<?php
		$field = 'range_min';
		$value = set_value($field, isset($role) ? $role->{$field} : '', FALSE);
		echo form_input(array(
			'name' => $field,
			'id' => $field,
			'size' => '15',
			'tabindex' => tab_index(),
			'value' => $value,
		));
		?>
		<p class="hint"><?= lang('constraint.range_min.hint') ?></p>
	</p>
	<?php echo form_error('range_min'); ?>

	<p>
		<label for="range_max"><?= lang('constraint.range_max') ?></label>
		<?php
		$field = 'range_max';
		$value = set_value($field, isset($role) ? $role->{$field} : '', FALSE);
		echo form_input(array(
			'name' => $field,
			'id' => $field,
			'size' => '15',
			'tabindex' => tab_index(),
			'value' => $value,
		));
		?>
		<p class="hint"><?= lang('constraint.range_max.hint') ?></p>
	</p>
	<?php echo form_error('range_max'); ?>

	<p>
		<label for="recur_max_instances"><?= lang('constraint.recur_max_instances') ?></label>
		<?php
		$field = 'recur_max_instances';
		$value = set_value($field, isset($role) ? $role->{$field} : '', FALSE);
		echo form_input(array(
			'name' => $field,
			'id' => $field,
			'size' => '15',
			'tabindex' => tab_index(),
			'value' => $value,
		));
		?>
		<p class="hint"><?= lang('constraint.recur_max_instances.hint') ?></p>
	</p>
	<?php echo form_error('recur_max_instances'); ?>

</fieldset>

<?php

$field = 'permissions';

foreach ($all_permissions as $scope => $groups) {

	$scope_label = lang("permission.scope.{$scope}.title");
	$legend = sprintf('<legend tabindex="%d">%s</legend>', tab_index(), $scope_label);

	$summary = sprintf('<div>%s</div><br>', lang("permission.scope.{$scope}.summary"));

	$inputs = '';

	foreach ($groups as $_group => $_group_permissions) {

		$group_label = lang(sprintf('permission.group.%s', $_group));
		$group_label = html_escape($group_label);

		$inputs .= "<p>";
		$inputs .= "<label>{$group_label}</label>";

		foreach ($_group_permissions as $_id => $_name) {

			$id = "{$field}_{$_id}";

			$checked = in_array($_id, set_value('permissions', $role->permission_ids ?? [], false));

			$title = lang(sprintf('permission.%s', $_name));
			if (empty($title)) {
				$title = $_name;
			} else {
				$title = html_escape($title);
			}

			$input = form_checkbox(array(
				'name' => "{$field}[]",
				'id' => $id,
				'value' => $_id,
				'tabindex' => tab_index(),
				'checked' => $checked,
			));
			$inputs .= "<label for='{$id}' class='ni'>{$input} {$title}</label>";

		}

		$inputs .= "</p>";

	}

	$out = "<fieldset>{$legend}{$summary}{$inputs}</fieldset>";
	echo $out;

}


$this->load->view('partials/submit', array(
	'submit' => array(isset($role) ? lang('app.action.save') : lang('app.action.create'), tab_index()),
	'cancel' => array(lang('app.action.cancel'), tab_index(), 'roles'),
));

echo form_close();
