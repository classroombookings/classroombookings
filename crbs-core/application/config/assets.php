<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['js'] = [
	'htmx' => [
		'path' => 'assets/js/lib/htmx@2.0.0.min.js',
		'defer' => true,
		'version' => false,
	],
	'hyperscript' => [
		'path' => 'assets/js/lib/_hyperscript@0.9.12.min.js',
		'defer' => true,
		'version' => false,
		'location' => 'body',
	],
	'datepicker' => [
		'path' => 'assets/js/lib/datepicker@1.js',
		'defer' => true,
		'version' => false,
	],
	'huebee' => [
		// 'path' => 'assets/js/lib/huebee@1.0.1.pkgd.js',
		'path' => 'assets/js/lib/huebee@2.1.1.pkgd.min.js',
		'defer' => true,
		'version' => false,
	],
	'autocomplete' => [
		'path' => 'assets/js/lib/accessible-autocomplete@1.min.js',
		'defer' => true,
		'version' => false,
	],
	'es6-promise' => [
		'path' => 'assets/js/lib/es6-promise@4.2.8.auto.js',
		'defer' => true,
		'version' => false,
	],
	'sortable' => [
		'path' => 'assets/js/lib/Sortable@1.15.0.min.js',
		'defer' => true,
		'version' => false,
	],
	'toastify' => [
		'path' => 'assets/js/lib/toastify@1.12.0.js',
		'defer' => true,
		'version' => false,
	],
	'unpoly' => [
		'path' => 'assets/js/lib/unpoly@2.5.3.es5.min.js',
		'defer' => true,
		'version' => false,
	],
	'surreal' => [
		'path' => 'assets/js/lib/surreal@1.2.1.js',
		'defer' => true,
		'version' => false,
	],
	'main' => [
		'path' => 'assets/js/main.js',
		'defer' => true,
		'version' => true,
	],
];


$config['hs'] = [
	'listbuilder' => [
		'path' => 'assets/hs/listbuilder._hs',
		'defer' => true,
		'version' => true,
	],
	'checkall' => [
		'path' => 'assets/hs/checkall._hs',
		'defer' => true,
		'version' => true,
	],
];
