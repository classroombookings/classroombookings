<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	https://codeigniter.com/user_guide/general/hooks.html
|
*/


/**
 * Initialise events
 *
 */
$hook['post_controller_constructor'][] = function() {

	$CI =& get_instance();
	$CI->benchmark->mark('events_start');
	require_once(APPPATH . 'events/EventType.php');

	$dirs = [
		APPPATH . 'events',
		ROOTPATH . 'local/events',
		ROOTPATH . 'crbs-managed/events',
	];

	foreach ($dirs as $dir) {
		$realpath = realpath($dir);
		if ( ! $realpath) continue;
		foreach (glob($dir . '/*_events.php') as $file) {
			include_once($file);
			$class_name = basename($file, '.php');
			if (class_exists($class_name)) {
				new $class_name($CI);
			}
		}
	}

	$CI->benchmark->mark('events_end');
};
