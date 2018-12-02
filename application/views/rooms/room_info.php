<table width="100%">
	<tr>
		<?php
		if ( ! empty($room->photo)) {
			$path = "uploads/{$room->photo}";
			if (is_file(FCPATH . $path)) {
				$url = base_url($path);
				$img = img($path, FALSE, "width='200' style='width:320px;height:auto;max-width:320px;padding:1px;border:1px solid #ccc'");
				echo $img;
			}
		}
		?>

		<td valign="top">

			<table width="100%" cellpadding="4" cellspacing="0" border="0">

			<tr>
			<td width="50%" valign="top"><strong>Location:</strong></td>
			<td><?php echo html_escape($room->location) ?></td>
			</tr>

			<?php if($room->username){ ?>
			<tr>
			<td width="50%" valign="top"><strong>Teacher:</strong></td>
			<td><?php echo html_escape($room->username) ?></td>
			</tr>
			<?php } ?>

			<?php if($room->notes){ ?>
			<tr>
			<td width="50%" valign="top"><strong>Notes:</strong></td>
			<td><?php echo html_escape($room->notes) ?></td>
			</tr>
			<?php } ?>

			<tr><td colspan="2"><hr size="1" /></td></tr>

			<?php
			$i=0;
			if (! empty($fields))
			{
				foreach($fields as $field){
					echo '<tr class="tr'.($i & 1).'">';
					echo '<td valign="top" width="50%"><strong>'.html_escape($field->name).':</strong></td>';
					$value = '';
					switch($field->type){
						case 'TEXT':
							$value = html_escape($fieldvalues[$field->field_id]);
						break;
						case 'CHECKBOX':
							$img = ($fieldvalues[$field->field_id] == 1) ? 'enabled.png|Yes' : 'no.png|No';
							$img = explode('|', $img);
							$value = sprintf('<img src="webroot/images/ui/%1$s" width="16" height="16" alt="%2$s" title="%2$s" />', $img[0], $img[1]);
						break;
						case 'SELECT':
							$options = $field->options;
							foreach($options as $option){
								$opts[$option->option_id] = html_escape($option->value);
							}
							#$value = $opts[$fieldvalues[$field->field_id]];
							#$value = var_export($opts,true) . $fieldvalues[$field->field_id];
							$value = $opts[$fieldvalues[$field->field_id]];

							unset($opts);
						break;
					}
					echo '<td>'.$value.'</td>';
					echo '</tr>';
					$i++;
				}
			}
			?>

			</table>
		</td>
	</tr>
</table>
