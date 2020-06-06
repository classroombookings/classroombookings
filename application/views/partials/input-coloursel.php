<?php
$data = $vars;
echo form_input(array(
		'name' => $data['name'],
		'id' => $data['name'],
		'size' => '7',
		'maxlength' => '7',
		'tabindex' => $data['tabindex'],
		'value' => $data['value'],
	));
	echo '<img onclick="showColorPicker(this,$(\''.$data['name'].'\'))" style="border:1px solid #ccc;cursor:pointer;" align="top" src="webroot/images/ui/coloursel.png" width="16" height="16" />';
?>
