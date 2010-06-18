<?php
if($departments != 0){
?>

<table class="list" width="100%" cellpadding="0" cellspacing="0" border="0">
	<col /><col /><col />
	<thead>
	<tr class="heading">
		<td class="h" title="Colour">Colour</td>
		<td class="h" title="Name">Name</td>
		<td class="h" title="Description">Description</td>
		<td class="h" title="Users">Number of people</td>
		<td class="h" title="X">&nbsp;</td>
	</tr>
	</thead>
	<tbody>
	<?php
	$i = 0;
	foreach ($departments as $department) {
	?>
	<tr>
		<td class="x" align="center" width="20"><div style="width:14px;height:14px;background-color:<?php echo $department->colour ?>">&nbsp;</div></td>
		<td class="x"><?php echo anchor('departments/edit/'.$department->department_id, $department->name) ?></td>
		<td class="x"><span title="<?php echo $department->description ?>"><?php echo word_limiter($department->description, 5) ?>&nbsp;</span></td>
		<td class="x"><?php #echo $department->usercount ?>&nbsp;</td>
		<td class="il">
		<?php
		$actiondata[0] = array('departments/delete/'.$department->department_id, 'Delete', 'cross_sm.gif' );
		$this->load->view('parts/listactions', $actiondata);
		#$this->load->view('parts/delete', array('url' => 'security/users/delete/'.$user->user_id));
		?></td>
	</tr>
	<?php $i++; } ?>
	</tbody>
</table>

<?php } else { ?>

<p>No departments currently exist!</p>

<?php } ?>