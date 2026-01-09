<?php

echo $this->session->flashdata('saved');

$iconbar = iconbar(array(
	array('departments/add', lang('department.add.action'), 'add.png'),
));

echo $iconbar;

?>

<table width="100%" cellpadding="2" cellspacing="2" border="0" class="border-table">
	<col /><col /><col />
	<thead>
	<tr class="heading">
		<th width="30%" title="<?= lang('department.field.name') ?>"><?= lang('department.field.name') ?></th>
		<th width="60%" title="<?= lang('department.field.description') ?>"><?= lang('department.field.description') ?></th>
		<th width="10%" title="<?= lang('app.actions') ?>"><?= lang('app.actions') ?></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=0;
	if ($departments) {
	foreach ($departments as $department) {
	?>
	<tr>
		<td><?php echo anchor('departments/edit/'.$department->department_id, html_escape($department->name)) ?></td>
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
		$msg = lang('department.no_items');
		echo '<td colspan="3" align="center" style="padding:16px 0">' . $msg . '</td>';
	}
	?>
	</tbody>
</table>

<?php

echo $pagelinks;
