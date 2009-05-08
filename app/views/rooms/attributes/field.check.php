<tr>
	<td class="caption">
		<label for="<?php echo $name ?>"><?php echo $attr->name ?></label>
	</td>
	<td class="field">
		<label for="<?php echo $name ?>" class="check">
		<?php
		unset($check);
		$check['name'] = $name;
		$check['id'] = $name;
		$check['value'] = '1';
		$check['checked'] = @set_checkbox($check['name'], $check['value'], ($values[$attr->field_id] == 1));
		$check['tabindex'] = $t;
		echo form_checkbox($check);
		?>
		</label>
	</td>
</tr>