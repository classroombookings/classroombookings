<tr>
	<td class="caption">
		<label for="<?php echo $name ?>"><?php echo $attr->name ?></label>
	</td>
	<td class="field">
		<?php
		unset($input);
		$input['name'] = $name;
		$input['id'] = $name;
		$input['size'] = '30';
		$input['maxlength'] = '255';
		$input['tabindex'] = $t;
		$input['value'] = @set_value($name, $values[$attr->field_id]);
		echo form_input($input);
		?>
	</td>
</tr>