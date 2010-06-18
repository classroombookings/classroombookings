<?php
$items = $_ci_vars;
$items_count = count($items);

$html = "";
#$html .= '<ul class="iconbar">';
$i = 0;
foreach($items as $item){
	
	// Class applied
	$class = ' class="action"';
	/*if($i == ($items_count-1)){
		$class = '';
	}*/
	$class = (isset($item[3]) && $item[3] != NULL) ? ' class="'.$item[3].'"' : ''; 
	$link = sprintf(
		'<a href="%1$s" style="background-image:url(img/ico/%2$s)" %4$s %5$s>%3$s</a>',
		site_url($item[0]),
		$item[2],
		$item[1],
		$class,
		(isset($item[4])) ? $item[4] : ''
	);
	$html .= $link;
	$i++;
}
$html .= '';

echo $html;
?>
