<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Classroombookings. Hassle-free resource booking for schools. <http://classroombookings.com/>
 * Copyright (C) 2006-2011 Craig A Rodway <craig.rodway@gmail.com>
 *
 * This file is part of Classroombookings.
 * Classroombookings is licensed under the Affero GNU GPLv3 license.
 * Please see license-classroombookings.txt for the full license text.
 */

class Msg
{

	var $CI;
	var $msgs;
	var $types = array('err', 'notice');


	function __construct()
	{
		// Load original CI object
		$this->CI =& get_instance();
	}
	
	
	function add($type = 'note', $text, $title = NULL){
		if (in_array($type, $this->types))
		{
			$data['title'] = $title;
			$data['text'] = $text;
			$thismsg = $this->CI->load->view('msg/'.$type, $data, TRUE);
			$this->msgs .= $thismsg . "\n";
			$this->CI->session->set_flashdata('flash', $this->msgs);
		}
	}
	
	
	function show(){
		return $this->msgs;
	}
	
	
	function showone($type = 'info', $text, $title = NULL)
	{
		if (in_array($type, $this->types))
		{
			$data['title'] = $title;
			$data['text'] = $text;
			$thismsg = $this->CI->load->view('msg/'.$type, $data, TRUE);
			return $thismsg;
		}
	}
	
	
	function err($text, $title = NULL)
	{
		return $this->showone('err', $text, $title);
	}
	
	
	function notice($text, $title = NULL)
	{
		return $this->showone('notice', $text, $title);
	}
	
	
	function fail($title, $text = '')
	{
		$error =& load_class('Exceptions');
		echo $error->show_error($text);
		exit;
	}
	
	
}


/* End of file app/libraries/Msg.php */