<table width="100%" class="checks" cellpadding="0" cellspacing="0" border="0">
<?php if(!empty($category)){ ?><tr class="h"><td colspan="2"><?php echo $category ?></td></tr><?php } ?>
<?php
foreach($options as $option){
?>
<tr>
	<td width="10">
	<?php
		unset($check);
		$check['name'] = 'permissions_'.$group_id.'[]';
		$check['value'] = $option[0];
		$check['id'] = "p_{$group_id}_{$option[0]}";
		$check['class'] = 'c';
		$check['checked'] = set_checkbox($check['name'], $check['value'], @in_array($option[0], $group_permissions[$group_id]));
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
