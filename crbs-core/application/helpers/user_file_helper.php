<?php

function handle_uploaded_file($path)
{
	if ( ! CRBS_MANAGED) return;

	$CI =& get_instance();
	$CI->load->library('bunny');
	return $CI->bunny->upload($path);
}


function delete_user_file($filename)
{
	if (empty($filename)) return false;

	if ( ! CRBS_MANAGED) {
		$path = FCPATH . 'uploads/' . $filename;
		if ( ! is_file($path)) return false;
		return @unlink($path);
	}

	$CI =& get_instance();
	$CI->load->library('bunny');
	return $CI->bunny->delete($filename);
}


function image_url($filename)
{
	if (empty($filename)) return false;

	if ( ! CRBS_MANAGED) {
		$path = FCPATH . 'uploads/' . $filename;
		if ( ! is_file($path)) return false;
		return base_url('uploads/' . $filename);
	}

	$CI =& get_instance();
	$CI->load->library('bunny');
	return $CI->bunny->get_url($filename);
}
