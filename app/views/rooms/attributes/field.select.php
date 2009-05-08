<tr>
	<td class="caption">
		<label for="<?php echo $name ?>"><?php echo $attr->name ?></label>
	</td>
	<td class="field">
		<?php
		echo form_dropdown($name, $attr->options, @set_value($name, $values[$attr->field_id]), 'id="'.$name.'" tabindex="'.$t.'"');
		$t++;
		?>
	</td>
</tr>