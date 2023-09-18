<?php

/**
 * Takes a DB result row object and converts dotted keys into nested classes.
 *
 */
function nest_object_keys($row)
{
	foreach ($row as $prop => $value) {

		$sep = FALSE;

		if (strpos($prop, '.') !== FALSE) {
			$sep = '.';
		}
		if (strpos($prop, '__') !== FALSE) {
			$sep = '__';
		}

		if ( ! $sep) {
			continue;
		}

		list($key, $field) = explode($sep, $prop);

		if ( ! isset($row->$key) || ! is_object($row->$key)) {
			$row->$key = new StdClass();
		}

		$row->$key->$field = $value;

		unset($row->$prop);
	}

	return $row;
}




function results_to_assoc($results, $key_prop = '', $value_prop = '', $blank = NULL)
{
	$out = array();

	if ($blank !== null) {
		$out[''] = $blank;
	}

	if ( ! is_array($results)) return $out;

	foreach ($results as $row) {

		$key = (is_object($row))
			? $row->$key_prop
			: $row[$key_prop];

		if (is_callable($value_prop)) {
			$value = call_user_func($value_prop, $row);
		} else {
			$value = (is_object($row))
				? $row->$value_prop
				: $row[ $value_prop ];
		}

		$out[$key] = $value;
	}

	return $out;
}
