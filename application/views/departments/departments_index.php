<?php

echo $this->session->flashdata('saved');

$iconbar = iconbar(array(
	array('departments/add', 'Add Department', 'add.gif'),
));

echo $iconbar;

?>

<table width="100%" cellpadding="2" cellspacing="2" border="0" class="sort-table" id="jsst-departments">
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
	<tr class="tr<?php echo ($i & 1) ?>">
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

<?php echo $pagelinks ?>

<?php
$jsst['name'] = 'st1';
$jsst['id'] = 'jsst-departments';
$jsst['cols'] = array("Name", "Description", "None");
$this->load->view('partials/js-sorttable', $jsst);
