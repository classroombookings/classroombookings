<?php

/**
 * Generate a coloured dot for the given week
 *
 */
function week_dot($week, $size = 'md')
{
	$col = $week->bgcol;
	$col = str_replace('#', '', $col);
	$col = '#' . $col;
	$col = html_escape($col);

	$out = "<span class='dot dot-week dot-size-{$size}' style='background-color:{$col}'></span>";
	return $out;
}


/**
 * Generate CSS for a timetable week.
 *
 * @param $week DB row object for week entry.
 * @return string Raw CSS to place inside <style> tag.
 *
 */
function week_calendar_css($week)
{
	$CI =& get_instance();

	// $CI->benchmark->mark('week_calendar_css_start');

	$template = $CI->load->view('css/session-calendar.css', [], TRUE);
	$CI->load->library('parser');
	$CI->load->helper('colour');

	$fgcol = html_escape($week->fgcol);
	$bgcol = html_escape($week->bgcol);

	$vars = [
		'week_id' => (int) $week->week_id,
		'fg_col' => $fgcol,
		'bg_col' => $bgcol,
		'range_bg' => colour_tint($bgcol, .1),
		'range_fg' => colour_shade($bgcol, .60),
	];

	$out = $CI->parser->parse_string($template, $vars, TRUE) . "\n";

	// $CI->benchmark->mark('week_calendar_css_end');

	return $out;
}

