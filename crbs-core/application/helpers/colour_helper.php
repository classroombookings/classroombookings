<?php

/**
 * https://gist.github.com/andrewrcollins/4570993
 *
 */

function colour_mix($color_1 = array(0, 0, 0), $color_2 = array(0, 0, 0), $weight = 0.5)
{
	$f = function($x) use ($weight) { return $weight * $x; };
	$g = function($x) use ($weight) { return (1 - $weight) * $x; };
	$h = function($x, $y) { return round($x + $y); };
	return array_map($h, array_map($f, $color_1), array_map($g, $color_2));
}

function colour_tint($color, $weight = 0.5)
{
	$t = $color;
	if(is_string($color)) $t = colour_hex2rgb($color);
	$u = colour_mix($t, array(255, 255, 255), $weight);
	if(is_string($color)) return colour_rgb2hex($u);
	return $u;
}

function colour_tone($color, $weight = 0.5)
{
	$t = $color;
	if(is_string($color)) $t = colour_hex2rgb($color);
	$u = colour_mix($t, array(128, 128, 128), $weight);
	if(is_string($color)) return colour_rgb2hex($u);
	return $u;
}

function colour_shade($color, $weight = 0.5)
{
	$t = $color;
	if(is_string($color)) $t = colour_hex2rgb($color);
	$u = colour_mix($t, array(0, 0, 0), $weight);
	if(is_string($color)) return colour_rgb2hex($u);
	return $u;
}

function colour_hex2rgb($hex = "#000000")
{
	$f = function($x) { return hexdec($x); };
	return array_map($f, str_split(str_replace("#", "", $hex), 2));
}

function colour_rgb2hex($rgb = array(0, 0, 0))
{
	$f = function($x) { return str_pad(dechex($x), 2, "0", STR_PAD_LEFT); };
	return "#" . implode("", array_map($f, $rgb));
}


function colour_brightness($hex)
{
	$hex = str_replace('#', '', $hex);
	$hex = html_escape($hex);

	$c_r = hexdec(substr($hex, 0, 2));
	$c_g = hexdec(substr($hex, 2, 2));
	$c_b = hexdec(substr($hex, 4, 2));

	return (($c_r * 299) + ($c_g * 587) + ($c_b * 114)) / 1000;
}


