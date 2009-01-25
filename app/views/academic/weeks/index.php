<?php
if($weeks != 0){
?>

<table class="list" width="100%" cellpadding="0" cellspacing="0" border="0">
	<col /><col /><col />
	<thead>
	<tr class="heading">
		<td class="h" title="Colour">Colour</td>
		<td class="h" title="Name">Name</td>
		<td class="h" title="X">&nbsp;</td>
	</tr>
	</thead>
	<tbody>
	<?php
	$i = 0;
	foreach ($weeks as $week) {
	?>
	<tr>
		<td class="x" align="center" width="20"><div style="width:14px;height:14px;background-color:<?php echo $week->colour ?>">&nbsp;</div></td>
		<td class="x"><?php echo anchor('academic/weeks/edit/'.$week->week_id, $week->name) ?></td>
		<td class="il">
		<?php
		$actiondata[0] = array('academic/weeks/delete/'.$week->week_id, 'Delete', 'cross_sm.gif' );
		$this->load->view('parts/listactions', $actiondata);
		#$this->load->view('parts/delete', array('url' => 'security/users/delete/'.$user->user_id));
		?></td>
	</tr>
	<?php $i++; } ?>
	</tbody>
</table>

<?php } else { ?>

<p>No weeks currently exist!</p>

<?php } ?>