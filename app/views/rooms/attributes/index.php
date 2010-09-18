<?php
if($attrs != 0){
?>

<table class="list" width="100%" cellpadding="0" cellspacing="0" border="0">
	<col /><col /><col />
	<thead>
	<tr class="heading">
		<td class="h" title="Name">Name</td>
		<td class="h" title="Type">Type</td>
		<td class="h" title="Options">Options</td>
		<td class="h" title="X">&nbsp;</td>
	</tr>
	</thead>
	<tbody>
	<?php
	$i = 0;
	foreach($attrs as $attr) {
	?>
	<tr>
		<td class="x"><?php echo anchor('rooms/attributes/edit/'.$attr->field_id, $attr->name) ?></td>
		<td class="x"><?php echo $fieldtypes[$attr->type] ?></td>
		<td class="x"><?php unset($attr->options[-1]); echo (isset($attr->options)) ? implode(", ", $attr->options) : ''; ?></td>
		<td class="il">
		<?php
		unset($actiondata);
		$actiondata[] = array('rooms/attributes/delete/'.$attr->field_id, ' ', 'cross_sm.gif' );
		$this->load->view('parts/linkbar', $actiondata);
		?></td>
	</tr>
	<?php $i++; } ?>
	</tbody>
</table>

<?php } else { ?>

<p>No fields currently exist!</p>

<?php } ?>