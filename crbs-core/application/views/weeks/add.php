<?php

$week_id = NULL;

if (isset($week) && is_object($week)) {
	$week_id = set_value('week_id', $week->week_id);
}

echo form_open(current_url(), ['class' => 'cssform', 'id' => 'week_add'], ['week_id' => $week_id] );

?>

<fieldset>

	<legend accesskey="W" tabindex="<?= tab_index() ?>">Week</legend>

	<p>
		<label for="name" class="required">Name</label>
		<?php
		$field = 'name';
		$value = set_value($field, isset($week) ? $week->name : '', FALSE);
		echo form_input([
			'name' => $field,
			'id' => $field,
			'size' => '20',
			'maxlength' => '20',
			'tabindex' => tab_index(),
			'value' => $value,
		]);
		?>
	</p>
	<?php echo form_error($field) ?>

	<div class="input-group">
		<label for="bgcol" class="required">Colour</label>
		<?php
		$field = 'bgcol';
		$value = set_value($field, isset($week) ? $week->bgcol : '', FALSE);
		echo form_colour_picker([
			'name' => $field,
			'value' => $value,
			'attrs' => 'tabindex=' . tab_index(),
		]);
		?>
	</div>
	<?php echo form_error($field); ?>

</fieldset>

<?php

$this->load->view('partials/submit', array(
	'submit' => array('Save', tab_index()),
	'cancel' => array('Cancel', tab_index(), 'weeks'),
));

echo form_close();
