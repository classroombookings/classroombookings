<table width="100%" class="checks" cellpadding="0" cellspacing="0" border="0">
<tr class="h"><td colspan="2"><?php echo $category ?></td></tr>
<?php
foreach($options as $option){
?>
<tr>
	<td width="10">
	<?php echo form_checkbox(array(
		'name' => 'permission[]',
		'value' => $option[0],
		'id' => "p_{$option[0]}",
		'class' => 'c',
		'checked' => $option[3],
	));
	?>
	</td>
	<td><label for="p_<?php echo $option[0] ?>" <?php echo ($option[2] != NULL) ? 'title="'.$option[2].'"' : ''; ?>><?php echo $option[1] ?></label></td>
</tr>
<?php
}
?>
</table>