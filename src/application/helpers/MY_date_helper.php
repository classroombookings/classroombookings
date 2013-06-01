<?php

/**
 * Format date in specified format
 *
 * @param string $date		Date to format, in Y-m-d format or anything recognised by strtotime
 * @param string $format		Format to use for formatting the date (for date())
 * @return string
 */
function date_fmt($date = '', $format = 'd/m/Y')
{
	if (empty($date) || $date === '0000-00-00' || $date === '0000-00-00 00:00:00') return NULL;
	return date($format, strtotime($date));
}