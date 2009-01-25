<?php


function isdark($colour){

	$colour = str_replace('#', '', $colour);
	
	$c_r = hexdec(substr($colour, 0, 2));
	$c_g = hexdec(substr($colour, 2, 2));
	$c_b = hexdec(substr($colour, 4, 2));

	$darkness = (($c_r * 299) + ($c_g * 587) + ($c_b * 114)) / 1000;
	
	return ($darkness < 125);
	
}