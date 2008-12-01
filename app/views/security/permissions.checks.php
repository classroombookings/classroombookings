<table width="100%" class="checks" cellpadding="0" cellspacing="0" border="0">
<tr class="h"><td colspan="2"><?php echo $category ?></td></tr>
<?php
foreach($options as $option){
?>
<tr>
	<td width="10">
	<?php
		unset($check);
		$check['name'] = 'permission[]';
		$check['value'] = $option[0];
		$check['id'] = "p_{$group_id}_{$option[0]}";
		$check['class'] = 'c';
		$check['value'] = NULL;
		echo form_checkbox($check);
	?>
	</td>
	<td>
		<?php
		$label = '<label for="%s"%s>%s</label>';
		echo sprintf($label,
			"p_{$group_id}_{$option[0]}",
			(isset($option[2])) ? 'title="' . $option[2] . '"' : '',
			$option[1]
		);
		?></td>
</tr>
<?php
}
?>
</table>