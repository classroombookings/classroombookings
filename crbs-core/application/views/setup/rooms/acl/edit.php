<?php

$attrs = [
	'hx-post' => current_url(),
	'hx-target' => 'this',
];


$hidden = [];
$hidden['action'] = 'edit';
$hidden['acl_id'] = $acl->acl_id;

echo form_open(current_url(), $attrs, $hidden);


?>

	<div class="cssform">

		<?php

		$toggle_hs = <<<EOS
on click
	set parent to the closest parent <p/>
	set firstcheck to the first <input.permission-check/> in parent
	set checkstatus to firstcheck.checked
	if checkstatus is true then set newstatus to false else set newstatus to true end
	set allchecks to <input.permission-check/> in parent
	repeat for input in allchecks
		set input.checked to newstatus
	end
	halt
EOS;

		$toggle_link = '<a
			href="#"
			role="button"
			style="font-weight:normal;display:inline-block;margin-top:4px"
			data-script="'.$toggle_hs.'"
		>Toggle all</a>';

		$prefix = sprintf('acl_%d', $acl->acl_id);

		$field = 'permissions';
		echo form_hidden("{$field}[]");

		foreach ($all_permissions as $_group => $_group_permissions) {

			$group_label = lang(sprintf('permission.group.%s', $_group));
			$group_label = html_escape($group_label);


			echo "<p>";
			echo "<label>{$group_label}<br>{$toggle_link}</label>";

			foreach ($_group_permissions as $_id => $_name) {

				$field_id = "{$prefix}_{$field}_{$_id}";

				$checked = in_array($_id, set_value('permissions', array_keys($acl->permissions), false));

				$title = lang(sprintf('permission.%s', $_name));
				if (empty($title)) {
					$title = $_name;
				} else {
					$title = html_escape($title);
				}

				$input = form_checkbox([
					'name' => "{$field}[]",
					'id' => $field_id,
					'value' => $_id,
					'tabindex' => tab_index(),
					'checked' => $checked,
					'class' => 'permission-check',
				]);
				echo "<label for='{$field_id}' class='ni'>{$input} {$title}</label>";

			}

			echo "</p>";

		}

		?>

</div>

<br>

<div class="flex justify-content-between align-items-center">
	<div><?php
	echo form_button([
		'type' => 'submit',
		'content' => '&check; ' . lang('app.action.save'),
	]);
	?></div>
	<div>
		<?php
		echo form_button([
			'type' => 'button',
			'content' => '&times; ' . lang('app.action.delete'),
			'style' => 'color:darkred',
			'hx-post' => site_url('setup/rooms/acl/delete/'.$acl->acl_id),
			'hx-confirm' => sprintf(lang('acl.delete.confirm'), html_escape($acl->entity_label), html_escape($acl->context_label)),
			'hx-target' => 'closest details',
			'hx-swap' => 'outerHTML',
		]);
		?>
	</div>
</div>

<?php
echo form_close();

