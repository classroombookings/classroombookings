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
		<td align="center" width="20">
			<?php $img = ($year->active == 1) ? 'ico/f_yes.gif' : 's.gif'; ?>
			<img src="img/<?php echo $img ?>" width="16" height="16" alt="" />
		</td>
		<td><?php echo anchor('academic/years/edit/'.$year->year_id, $year->name) ?></td>
		<td><?php echo date("l jS F Y", todate($year->date_start)) ?></td>
		<td><?php echo date("l jS F Y", todate($year->date_end)) ?></td>
		<td class="il">
		<?php
		unset($actiondata);
		if($year->active != 1){
			$actiondata[] = array('academic/years/activate/'.$year->year_id, ' ', 'tick_sm.gif', 'Make active year');
		}
		$actiondata[] = array('academic/years/delete/'.$year->year_id, ' ', 'cross_sm.gif', 'Delete year');
		$this->load->view('parts/linkbar', $actiondata);
		?></td>
	</tr>
	<?php $i++; } ?>
	</tbody>
</table>

<?php } else { ?>

<p>No years currently exist!</p>

<?php } ?>
