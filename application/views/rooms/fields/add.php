<?php
$field_id = NULL;
if (isset($field) && is_object($field)) {
	$field_id = set_value('field_id', $field->field_id);
}

echo "<!-- $field_id -->";

if ( ! empty($field_id)) {
	echo msgbox('exclamation', 'You cannot change the type of a field. Instead, delete the field and create a new one.');
}

echo form_open('rooms/save_field', array('class' => 'cssform', 'id' => 'fields_add'), array('field_id' => $field_id));
?>

<br />

<fieldset>

	<legend accesskey="F" tabindex="<?= tab_index() ?>">Field Details</legend>

	<p>
		<label for="name" class="required">Name</label>
		<?php
		$input_name = 'name';
		$value = set_value($input_name, isset($field) ? $field->name : '', FALSE);
		echo form_input(array(
			'name' => $input_name,
			'id' => $input_name,
			'size' => '30',
			'maxlength' => '64',
			'tabindex' => tab_index(),
			'value' => $value,
		));
		?>
	</p>
	<?php echo form_error($input_name); ?>

	<?php if ( ! isset($field)): ?>

	<p>
		<label for="type">Type</label>
		<?php

		$input_name = 'type';
		$value = set_value($input_name, isset($field) ? $field->type : '', FALSE);

		foreach ($options_list as $k => $v) {
			$id = "{$input_name}_{$k}";
			$input = form_radio(array(
				'name' => $input_name,
				'id' => $id,
				'value' => $k,
				'checked' => ($value == $k),
				'tabindex' => tab_index(),
				'up-switch' => '.dropdown_options',
			));
			echo "<label for='{$id}' class='ni'>{$input}{$v}</label>";
		}
	?>
	</p>
	<?php echo form_error($input_name); ?>

	<?php else: ?>

	<?php
	$input_name = 'type';
	$value = set_value($input_name, isset($field) ? $field->type : '');
	echo form_input(array(
		'type' => 'hidden',
		'name' => $input_name,
		'id' => $input_name,
		'value' => $value,
	));
	?>

	<?php endif; ?>

	<?php
	$options_attrs = '';
	if ( ! isset($field)) {
		$options_attrs .= ' up-show-for="SELECT" ';
	} elseif (isset($field) && $field->type != 'SELECT') {
		$options_attrs .= 'style="display:none"';
	}
	?>

	<div class="dropdown_options" <?= $options_attrs ?>>
		<p>
			<label for="items">Items</label>
			<?php
			$input_name = 'options';
			$options_str = '';
			if (isset($field) && is_array($field->options)) {
				$option_values = array();
				foreach ($field->options as $option) {
					$option_values[] = html_escape($option->value);
				}
				$options_str = implode("\n", $option_values);
			}
			$value = set_value($input_name, $options_str, FALSE);
			echo form_textarea(array(
				'name' => $input_name,
				'id' => $input_name,
				'rows' => '10',
				'cols' => '40',
				'tabindex' => tab_index(),
				'value' => $options_str,
			));
			?><p class="hint">Enter the selectable options for the dropdown list here; one on each line.</p>
		</p>
		<?php echo form_error($input_name); ?>
	</div>

</fieldset>


<?php

$this->load->view('partials/submit', array(
	'submit' => array('Save', tab_index()),
	'cancel' => array('Cancel', tab_index(), 'rooms/fields'),
));

echo form_close();
