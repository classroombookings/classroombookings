<?php


/**
 * Work out if a colour is dark or light - used for selecting text colour on a given background
 *
 * @param	string		Colour in hex format with or without #
 * @param	bool		TRUE if colour is dark, FALSE if light
 */
function isdark($colour){

	$colour = str_replace('#', '', $colour);
	
	$c_r = hexdec(substr($colour, 0, 2));
	$c_g = hexdec(substr($colour, 2, 2));
	$c_b = hexdec(substr($colour, 4, 2));

	$darkness = (($c_r * 299) + ($c_g * 587) + ($c_b * 114)) / 1000;
	
	return ($darkness < 125);
	
}




/**
 * Small image filename
 *
 * @param	string	Photo filename in database
 * @return	string	String with placeholder (#) replaced with sm
 */
function image_small($name){
	return str_replace('#', 'sm', $name);
}




/**
 * Large image filename
 *
 * @param	string	Photo filename in database
 * @return	string	String with placeholder (#) replaced with lg
 */
function image_large($name){
	return str_replace('#', 'lg', $name);
}




/**
 * Language helper. Just make it shorter!
 */
function lang($language_key = NULL, $variable = NULL){
	$CI =& get_instance();
	#$CI->lang->load('crbs2','English');
	
	if (!empty($variable)){
		return sprintf($CI->lang->line($language_key),$variable);
	}
	
	return $CI->lang->line($language_key);
}




/**
 * Menu link helper. Crates menu links.
 *
 * seg1 - the segment of the current URI at the position we want
 * href - path/to/url (gets truned into array)
 * text - text of link
 * i - index of href array to check uri segment to
 */
function dolink($seg, $href, $text, $i = 0){
	$hrefarr = (strpos($href, '/') === FALSE) ? array($href) : explode('/', $href);
	#echo $hrefarr[$i] . "/ ";
	#echo "Seg: $seg/ ";
	#$hrefarr = explode('/', $href);
	$link = '<li%s><a href="%s">%s</a></li>';
	$sel = ($seg == $hrefarr[$i]) ? ' class="current"' : '';
	return sprintf($link, $sel, site_url($href), $text);
}