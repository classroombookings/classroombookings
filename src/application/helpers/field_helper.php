<?php
function field($validation, $database = null, $last = '')
{
	$value = (isset($validation)) ? $validation : ( (isset($database)) ? $database : $last);
	return $value;
}
?>
