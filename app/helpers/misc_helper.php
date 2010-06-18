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