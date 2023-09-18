<?php
defined('BASEPATH') OR exit('No direct script access allowed');

return array(

	'config' => array(
		'base_url' => '{base_url}',
		'log_threshold' => 1,
		'index_page' => 'index.php',
		'uri_protocol' => 'REQUEST_URI',
	),

	'database' => array(
		'hostname' => '{db_host}',
		'port' => '{db_port}',
		'username' => '{db_user}',
		'password' => '{db_pass}',
		'database' => '{db_name}',
		'dbdriver' => '{db_driver}',
	),

);
