<?php

echo $this->session->flashdata('saved');

$iconbar = iconbar(array(
	array('departments/add', 'Add Department', 'add.png'),
));

echo $iconbar;

$sort_cols = ["Name", "Description", "None"];

?>

<table width="100%" cellpadding="2" cellspacing="2" border="0" class="zebra-table sort-table" id="jsst-departments" up-data='<?= json_encode($sort_cols) ?>'>
	<col /><col /><col />
	<thead>
	<tr class="heading">
		<td class="h" title="Name">Name</td>
		<td class="h" title="Description">Description</td>
		<td class="n" title="X">&nbsp;</td>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=0;
	if ($departments) {
	foreach ($departments as $department) {
	?>
	<tr>
		<td><?php echo html_escape($department->name) ?></td>
		<td><?php echo html_escape($department->description) ?></td>
		<td width="45" class="n"><?php
			$actions['edit'] = 'departments/edit/'.$department->department_id;
			$actions['delete'] = 'departments/delete/'.$department->department_id;
			$this->load->view('partials/editdelete', $actions);
			?>
		</td>
	</tr>
	<?php $i++; }
	} else {
		echo '<td colspan="4" align="center" style="padding:16px 0">No departments exist!</td>';
	}
	?>
	</tbody>
</table>

<?php

echo $pagelinks;
