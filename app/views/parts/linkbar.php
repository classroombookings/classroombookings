<?php
/*
	0: url
	1: title
	2: active true/false
*/
$items = $_ci_vars;
$items_count = count($items);

$html = "";
$html .= '<ul class="linkbar">';
$i = 0;
foreach($items as $item){
	
	// Class applied
	$class = ($i < $items_count -1) ? ' class="ibl"' : '';
	// Format
	$item[2] = (isset($item[2])) ? $item[2] : FALSE;
	$format = ($item[2] == TRUE) ? '<li%3$s>%2$s</li>' : '<li%3$s><a href="%1$s">%2$s</a>';
	$link = sprintf($format, site_url($item[0]), $item[1], $class);
	$html .= $link;
	$i++;
}
$html .= '</ul><br/>';

echo $html;
?>
