<?php
$items = $_ci_vars;
$items_count = count($items);

$html = "";
$html .= '<ul class="iconbar">';
$i = 0;
foreach($items as $item){
	
	// Class applied
	$class = ' class="ibl"';
	if($i == ($items_count-1)){
		$class = '';
	}
	$link = sprintf(
		'<li%4$s><a href="%1$s" style="background-image:url(img/ico/%2$s)">%3$s</a></li>',
		site_url($item[0]),
		$item[2],
		$item[1],
		$class
	);
	$html .= $link;
	$i++;
}
$html .= '</ul><br/>';

echo $html;
?>
