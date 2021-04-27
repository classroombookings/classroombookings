<?php

/**
 * Takes a DB result row object and converts dotted keys into nested classes.
 *
 */
function nest_object_keys($row)
{
	foreach ($row as $prop => $value) {
		if (strpos($prop, '.') !== FALSE) {
			list($key, $field) = explode('.', $prop);
			if ( ! isset($row->$key) || ! is_object($row->$key)) {
				$row->$key = new StdClass();
			}
			$row->$key->$field = $value;
			unset($row->$prop);
		}
	}

	return $row;
}
