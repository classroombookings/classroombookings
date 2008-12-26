<?php
if($years != 0){
?>

<table class="list" width="100%" cellpadding="0" cellspacing="0" border="0">
	<col /><col /><col />
	<thead>
	<tr class="heading">
		<td class="h" title="Active">Active</td>
		<td class="h" title="Name">Name</td>
		<td class="h" title="DateStart">Start Date</td>
		<td class="h" title="DateEnd">End Date</td>
		<td class="h" title="X">&nbsp;</td>
	</tr>
	</thead>
	<tbody>
	<?php
	$i = 0;
	foreach($years as $year) {
	?>
	<tr>
		<td align="center" width="20"><?php if($year->active == 1){ ?><img src="img/ico/f_yes.gif" width="16" height="16" alt="Active" /><?php } ?></td>
		<td><?php echo anchor('academic/years/edit/'.$year->year_id, $year->name) ?></td>
		<td><?php echo date("l jS F Y", todate($year->date_start)) ?></td>
		<td><?php echo date("l jS F Y", todate($year->date_end)) ?></td>
		<td class="il">
		<?php
		if($year->active != 1){
			$actiondata[0] = array('academic/years/activate/'.$year->year_id, 'Make active', 'tick_sm.gif');
		}
		$actiondata[1] = array('academic/years/delete/'.$year->year_id, 'Delete', 'cross_sm.gif');
		$this->load->view('parts/listactions', $actiondata);
		unset($actiondata);
		?></td>
	</tr>
	<?php $i++; } ?>
	</tbody>
</table>

<?php } else { ?>

<p>No years currently exist!</p>

<?php } ?>