<?php

$attrs = [
	'class' => 'cssform',
	'hx-post' => current_url(),
	'hx-target' => 'this',
];

$hidden = [];
$hidden['action'] = 'add';
$hidden['entity_type'] = $entity_type;
$hidden['entity_id'] = $entity_id;
$hidden['context_type'] = $context_type;

echo form_open(current_url(), $attrs, $hidden);

?>

	<fieldset>

		<legend><?= lang('acl.access_control_entry') ?></legend>

		<?php

		$this->load->view($context_input);
		echo "<br>";

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
		>' . lang('app.action.toggle_all') . '</a>';

		$prefix = sprintf('acl_new_%s', uniqid());

		$field = 'permissions';
		echo form_hidden("{$field}[]");

		foreach ($all_permissions as $_group => $_group_permissions) {

			$group_label = lang(sprintf('permission.group.%s', $_group));
			$group_label = html_escape($group_label);

			echo "<p>";
			echo "<label>{$group_label}<br>{$toggle_link}</label>";

			foreach ($_group_permissions as $_id => $_name) {

				$field_id = "{$prefix}_{$field}_{$_id}";

				$checked = in_array($_id, set_value('permissions', [], false));

				$title = lang(sprintf('permission.%s', $_name));
				if (empty($title)) {
					$title = $_name;
				} else {
					$title = html_escape($title);
				}

				$input = form_checkbox(array(
					'name' => "{$field}[]",
					'id' => $field_id,
					'value' => $_id,
					'tabindex' => tab_index(),
					'checked' => $checked,
					'class' => 'permission-check',
				));
				echo "<label for='{$field_id}' class='ni'>{$input} {$title}</label>";

			}

			echo "</p>";

		}

		echo "<br>";
		$this->load->view('partials/submit', array(
			'submit' => array('&check; ' . lang('app.action.add'), tab_index()),
			'cancel' => array(lang('app.action.cancel'), tab_index(), $cancel_url),
		));

		?>


	</fieldset>

<?php
echo form_close();

