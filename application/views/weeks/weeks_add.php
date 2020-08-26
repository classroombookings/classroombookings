<?php
$week_id = NULL;
if (isset($week) && is_object($week)) {
	$week_id = set_value('week_id', $week->week_id);
}

echo form_open('weeks/save', array('class' => 'cssform', 'id' => 'week_add'), array('week_id' => $week_id) );

?>


<fieldset>

	<legend accesskey="W" tabindex="<?= tab_index() ?>">Week Information</legend>

	<p>
		<label for="name" class="required">Name</label>
		<?php
		$field = 'name';
		$value = set_value($field, isset($week) ? $week->name : '', FALSE);
		echo form_input(array(
			'name' => $field,
			'id' => $field,
			'size' => '20',
			'maxlength' => '20',
			'tabindex' => tab_index(),
			'value' => $value,
		));
		?>
	</p>
	<?php echo form_error($field) ?>

	<p>
		<label for="bgcol" class="required">Background Colour</label>
		<?php
		$field = 'bgcol';
		$value = set_value($field, isset($week) ? $week->bgcol : '666666', FALSE);
		echo form_input(array(
			'name' => $field,
			'id' => $field,
			'size' => '7',
			'maxlength' => '7',
			'tabindex' => tab_index(),
			'value' => $value,
			'onchange' => '$(\'sample\').style.backgroundColor = this.value;',
		));
		?>
	</p>
	<?php echo form_error($field); ?>

	<p>
		<label for="fgcol" class="required">Foreground Colour</label>
		<?php
		$field = 'fgcol';
		$value = set_value($field, isset($week) ? $week->fgcol : 'FFFFFF', FALSE);
		echo form_input(array(
			'name' => $field,
			'id' => $field,
			'size' => '7',
			'maxlength' => '7',
			'tabindex' => tab_index(),
			'value' => $value,
			'onchange' => '$(\'sample\').style.color = this.value;',
		));
		?>
	</p>
	<?php echo form_error($field); ?>

</fieldset>


<fieldset>

	<legend accesskey="D" tabindex="6">Week Dates</legend>

	<div>Please select the week-commencing (Monday) dates within the current academic year that this week applies to.</div>

	<?php

	echo '<table width="100%" cellpadding="0" cellspacing="10">';
	echo '<tbody>';
	$row = 0;

	if ($weeks) {
		foreach ($weeks as $oneweek) {
			$weekdata[$oneweek->week_id]['fgcol'] = $oneweek->fgcol;
			$weekdata[$oneweek->week_id]['bgcol'] = $oneweek->bgcol;
		}
	}

	#print_r($weekdata);

	foreach ($mondays as $monday) {
		$checked = '';
		if (isset($monday['holiday']) && $monday['holiday'] == true) {
			$checkbox_disabled = '';	//' disabled="disabled" ';
			$cell_style = 'border:1px solid #888;';
		} else {
			$checkbox_disabled = '';
			$cell_style = '';
		}
		$fgcol = '#000';
		if (isset($monday['week_id']) && $monday['week_id'] != NULL) {
			$cell_style = "background:#{$weekdata[$monday['week_id']]['bgcol']};";
			$fgcol = '#'.$weekdata[$monday['week_id']]['fgcol'];
		}

		if ($row == 0) { echo '<tr>'; }

		$value = isset($week) ? $week->week_id : NULL;
		if (isset($monday['week_id']) && ($monday['week_id'] == $value && ! empty($value))) {
			$checked = 'checked="checked"';
		} else {
			$checked = '';
		}

		$weekscount = ($weekscount == 0 ? 1 : $weekscount);

		echo '<td style="'.$cell_style.'padding:4px;" width="'.round(100/$weekscount).'%">';
		$input = '<input type="checkbox" name="dates[]" value="'.$monday['date'].'" id="'.$monday['date'].'" '.$checkbox_disabled.' '.$checked.' /> ';
		echo '<label class="ni" for="'.$monday['date'].'" style="color:'.$fgcol.'">';
		echo $input;
		echo date("d M Y", strtotime($monday['date']));
		echo '</label>';
		echo '</td>';
		echo "\n";
		if($row == $weekscount-1){ echo "</tr>\n\n"; $row = -1; }
		$row++;
	}

	echo '</tbody>';
	echo '</table>';

	?>

</fieldset>


<?php

$this->load->view('partials/submit', array(
	'submit' => array('Save', tab_index()),
	'cancel' => array('Cancel', tab_index(), 'weeks'),
));

echo form_close();
