<?php

echo $this->session->flashdata('saved');

$iconbar = iconbar(array(
	array('weeks/add', 'Add Week', 'add.gif'),
	array('weeks/academicyear', 'Academic Year', 'school_manage_weeks_academicyear.gif'),
));

echo $iconbar;

?>

<table width="100%" cellpadding="2" cellspacing="2" border="0" class="sort-table" id="jsst-weeks">
	<col /><col /><col />
	<thead>
		<tr class="heading">
			<td class="h" title="Name">Name</td>
			<td class="h" title="Colour">Colour</td>
			<td class="n" title="X">&nbsp;</td>
		</tr>
	</thead>
	<tbody>
		<?php
		$i=0;
		if ($weeks) {
		foreach ($weeks as $week) {
		?>
		<tr class="tr<?php echo ($i & 1) ?>">
			<td><?php echo html_escape($week->name) ?></td>
			<td>
			<?php echo sprintf('<span style="padding:2px;background:#%s;color:#%s">%s</span>', $week->bgcol, $week->fgcol, html_escape($week->name)); ?></td>
			<td width="45" class="n"><?php
				$actions['edit'] = 'weeks/edit/'.$week->week_id;
				$actions['delete'] = 'weeks/delete/'.$week->week_id;
				$this->load->view('partials/editdelete', $actions);
				?>
			</td>
		</tr>
		<?php $i++; }
		} else {
			echo '<td colspan="4" align="center" style="padding:16px 0">No weeks defined!</td>';
		}
		?>
	</tbody>
</table>

<?php
echo $iconbar;

$jsst['name'] = 'st1';
$jsst['id'] = 'jsst-weeks';
$jsst['cols'] = array("Name", "Colour", "None");
$this->load->view('partials/js-sorttable', $jsst);
