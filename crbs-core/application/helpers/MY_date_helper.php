<?php

defined('BASEPATH') OR exit('No direct script access allowed');


function datetime_from_string($value) {

	if ($value instanceof DateTime) {
		return $value;
	}

	if (empty($value) || ! is_string($value)) {
		return false;
	}

	switch (true) {

		case (str_contains($value, '-') && strlen($value) === 10):
			$dt = DateTime::createFromFormat("!Y-m-d", $value);
			break;

		case (str_contains($value, '/') && strlen($value) === 10):
			$dt = DateTime::createFromFormat('!d/m/Y', $value);
			break;

		case (str_contains($value, '/') && strlen($value) < 10):
			$parts = explode('/', $value);
			if (count($parts) !== 3) return false;
			[$dd, $mm, $yyyy] = $parts;
			$is_numeric = (is_numeric($dd) && is_numeric($mm) && is_numeric($yyyy));
			if ( ! $is_numeric) return false;
			$dt = new DateTime();
			$dt->setDate($yyyy, $mm, $dd);
			break;

		case (str_contains($value, ':') && strlen($value) === 8):
			$dt = DateTime::createFromFormat('!H:i:s', $value);
			break;

		default:
			try {
				$dt = new DateTime($value);
			} catch (\Exception) {
				return false;
			}

	}

	$errors = DateTime::getLastErrors();

	if ( ! empty($errors)) {
		if ($errors['warning_count'] > 0 || $errors['error_count'] > 0) {
			return false;
		}
	}

	return $dt;
}



if ( ! function_exists('date_output')) {
	function date_output(string|DateTime $date, string $format)
	{
		$CI =& get_instance();
		$CI->load->library('dates');
		return $CI->dates->format($date, $format);
	}
}


if ( ! function_exists('date_output_long')) {
	function date_output_long(string|DateTime $date)
	{
		$CI =& get_instance();
		$CI->load->library('dates');
		return $CI->dates->format($date, Dates::FORMAT_LONG);
	}
}


if ( ! function_exists('date_output_weekday')) {
	function date_output_weekday(string|DateTime $date)
	{
		$CI =& get_instance();
		$CI->load->library('dates');
		return $CI->dates->format($date, Dates::FORMAT_WEEKDAY);
	}
}


if ( ! function_exists('date_output_time')) {
	function date_output_time(string|DateTime $date)
	{
		$CI =& get_instance();
		$CI->load->library('dates');
		return $CI->dates->format($date, Dates::FORMAT_TIME);
	}
}



if ( ! function_exists('highlight_weekday')) {
	/**
	 * If a weekday (in english or current language) is found in $value, highlight it by wrapping in $tag_open and $tag_close.
	 * Used in bookings grid to format the day + date on separate lines.
	 *
	 */
	function highlight_weekday(string $value, string $tag_open = '<strong>', string $tag_close = '</strong>')
	{
		if (empty($value)) return $value;

		$day_names = [
			'Mo',
			'Mon',
			'Monday',
			'Tu',
			'Tue',
			'Tuesday',
			'We',
			'Wed',
			'Wednesday',
			'Th',
			'Thu',
			'Thursday',
			'Fr',
			'Fri',
			'Friday',
			'Sa',
			'Sat',
			'Saturday',
			'Su',
			'Sun',
			'Sunday',
		];


		$lang = config_item('language');
		if ($lang != 'english') {
			foreach ($day_names as $day) {
				$key = sprintf('cal_%s', strtolower($day));
				$day_names[] = lang($key);
			}
		}
		$phrase = implode('|', $day_names);
		return preg_replace('/('.$phrase.')[.\s]/i'.(UTF8_ENABLED ? 'u' : ''), $tag_open.'\\1'.$tag_close, $value);
	}
}
