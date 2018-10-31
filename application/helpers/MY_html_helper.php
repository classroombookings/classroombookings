<?php
defined('BASEPATH') OR exit('No direct script access allowed');


function field($validation, $database = NULL, $last = ''){
	$value = (isset($validation)) ? $validation : ( (isset($database)) ? $database : $last);
	return $value;
}




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



function tab_index($reset = NULL)
{
	static $_tab_index;

	if ( ! strlen($_tab_index) || $_tab_index === 0)
	{
		$_tab_index = 0;
	}
	else
	{
		$_tab_index++;
	}

	return $_tab_index;
}
