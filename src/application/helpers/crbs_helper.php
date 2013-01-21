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
/*function lang($language_key = NULL, $variable = NULL){
	$CI =& get_instance();
	#$CI->lang->load('crbs2','English');
	
	if (!empty($variable)){
		return sprintf($CI->lang->line($language_key),$variable);
	}
	
	return $CI->lang->line($language_key);
}*/




/**
 * Get a single option that has been set in the 'options' category in the config
 */
function option($name)
{
	$CI =& get_instance();
	return $CI->config->item($name, 'options');
}




function flash($type = 'notice', $content = '')
{
	die("FLASH FUNCTION!");
}




/** 
 * Calls to tab_index() will return an incrementing number.
 */
function tab_index($reset = FALSE)
{
	static $t;
	
	if ($reset === TRUE OR ! is_numeric($t))
	{
		$t = 0;
	}
	
	$t++;
	return $t;
}