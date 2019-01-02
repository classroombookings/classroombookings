<?php
$data = $vars;
$items = count($data);
$html = '<p class="iconbar">';
for( $z=0; $z<$items; $z++){
	$link = $data[$z][0];
	$name = $data[$z][1];
	$icon = $data[$z][2];
	$html .= '<a href="'.site_url($link).'">';
	$html .= '<img src="webroot/images/ui/'.$icon.'" alt="'.$name.'" align="top" hspace="0" border="0" /> ';
	$html .= $name . '</a>';
	if( $z != ($items-1) ){
		$html .= '<img src="webroot/images/sep.gif" alt="|" align="top" hspace="0" border="0" style="margin:0px 6px;" />';
	}
}
$html .= '</p>';
echo $html;
?>
