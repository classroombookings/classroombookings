<?php

defined('BASEPATH') OR exit('No direct script access allowed');


if ( ! function_exists('parse_sort')) {
	function parse_sort(?string $value = null, array $field_config = [])
	{
		if (is_null($value)) return null;
		if (empty($field_config)) return null;
		$out = [];
		$sorts = explode(',', $value);
		if (empty($sorts)) return null;
		foreach ($sorts as $sort) {
			$sort = trim($sort);
			$order = str_starts_with($sort, '-') ? 'DESC' : 'ASC';
			$key = ltrim($sort, '-');
			if ( ! array_key_exists($key, $field_config)) continue;
			$map = $field_config[$key];
			if ($map !== $key) {
				$key = $field_config[$key];
			}
			$out[] = sprintf('%s %s', $key, $order);
		}
		return empty($out) ? null : implode(', ', $out);
	}
}
