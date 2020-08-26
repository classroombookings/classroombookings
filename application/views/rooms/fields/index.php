<?php

echo $this->session->flashdata('saved');

$iconbar = iconbar(array(
	array('rooms/add_field', 'Add Field', 'add.png'),
	array('rooms', 'Rooms', 'school_manage_rooms.png'),
));

echo $iconbar;

$sort_cols = ["Name", "Type", "Options", "None"];

?>

<table width="100%" cellpadding="2" cellspacing="2" border="0" class="zebra-table sort-table" id="jsst-roomfields" up-data='<?= json_encode($sort_cols) ?>'>
	<col /><col /><col /><col />
	<thead>
	<tr class="heading">
		<td class="h" title="Name">Name</td>
		<td class="h" title="Type">Type</td>
		<td class="h" title="Options">Options</td>
		<td class="n" title="X"></td>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=0;
	if ($fields) {
	foreach ($fields as $field) { ?>
	<tr>
		<td><?php echo html_escape($field->name) ?></td>
		<td><?php echo $options_list[$field->type] ?></td>
		<td><?php
		if (isset($field->options) && is_array($field->options)) {
			$values = array();
			foreach ($field->options as $option) {
				$label = trim($option->value);
				if (empty($label)) continue;
				$values[] = html_escape($label);
			}
			echo implode(", ", $values);
		}
		?></td>
		<td width="45" class="n"><?php
			$actions['edit'] = 'rooms/edit_field/'.$field->field_id;
			$actions['delete'] = 'rooms/delete_field/'.$field->field_id;
			$this->load->view('partials/editdelete', $actions);
			?>
		</td>
	</tr>
	<?php $i++; }
	} else {
		echo '<td colspan="4" align="center" style="padding:16px 0">No room fields exist!</td>';
	}
	?>
	</tbody>
</table>

<?php

echo $iconbar;
