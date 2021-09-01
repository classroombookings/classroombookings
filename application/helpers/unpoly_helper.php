<?php

defined('BASEPATH') OR exit('No direct script access allowed');

function up_target()
{
	$CI =& get_instance();
	$target = $CI->input->get_request_header('x-up-target');
	return strlen($target) ? $target : FALSE;
}
