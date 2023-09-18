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

		case (strpos($value, '-') !== false && strlen($value) === 10):
			$dt = DateTime::createFromFormat("!Y-m-d", $value);
			break;

		case (strpos($value, '/') !== false && strlen($value) === 10):
			$dt = DateTime::createFromFormat('!d/m/Y', $value);
			break;

		case (strpos($value, '/') !== false && strlen($value) < 10):
			$parts = explode('/', $value);
			if (count($parts) !== 3) return false;
			list($dd, $mm, $yyyy) = $parts;
			$is_numeric = (is_numeric($dd) && is_numeric($mm) && is_numeric($yyyy));
			if ( ! $is_numeric) return false;
			$dt = new DateTime();
			$dt->setDate($yyyy, $mm, $dd);
			break;

		case (strpos($value, ':') !== false && strlen($value) === 8):
			$dt = DateTime::createFromFormat('!H:i:s', $value);
			break;

		default:
			try {
				$dt = new DateTime($value);
			} catch (\Exception $e) {
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
