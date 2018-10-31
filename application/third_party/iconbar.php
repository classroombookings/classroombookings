<?php
function iconbar( $data ){
	$items = count($data);
	$html = '<p class="iconbar">';
	for( $z=0; $z<$items; $z++){
		$link = $data[$z][0];
		$name = $data[$z][1];
		$icon = $data[$z][2];
		$html .= '<img src="webroot/img/ui/'.$icon.'" alt="'.$name.'" align="top" hspace="6" border="0" /> ';
		$html .= anchor($link, $name);
		if( $z != ($items-1) ){
			$html .= '<img src="webroot/img/sep.gif" alt="|" align="top" hspace="0" border="0" style="margin-left:6px;" />';
		}
	}
	$html .= '</p>';
	return $html;
}
?>
