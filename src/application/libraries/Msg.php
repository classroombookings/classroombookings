<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Classroombookings. Hassle-free resource booking for schools. <http://classroombookings.com/>
 * Copyright (C) Craig A Rodway <craig.rodway@gmail.com>
 *
 * Licensed under the Open Software License version 3.0
 * 
 * This source file is subject to the Open Software License (OSL 3.0) that is
 * bundled with this package in the files license.txt. It is also available 
 * through the world wide web at this URL:
 * http://opensource.org/licenses/OSL-3.0
 */

class Msg
{
	
	
	private $_CI;
	private $_msgs = '';
	private $_types = array('err', 'notice');


	function __construct()
	{
		// Load original CI object
		$this->_CI =& get_instance();
	}	
	
	
	/** 
	 * Add a notice to the flashdata
	 */
	function flash($type = 'notice', $text = '', $title = NULL)
	{
		if (in_array($type, $this->types))
		{
			$this->_msgs .= $this->compose($type, $text, $title) . "\n";
			$this->_CI->session->set_flashdata('flash', $this->_msgs);
		}
	}
	
	
	function show()
	{
		return $this->_msgs;
	}
	
	
	function show_one($type = 'notice', $text = '', $title = NULL)
	{
		if (in_array($type, $this->_types))
		{
			return $this->compose($type, $text, $title);
		}
	}
	
	
	function err($text = '', $title = NULL)
	{
		return $this->show_one('err', $text, $title);
	}
	
	
	function notice($text = '', $title = NULL)
	{
		return $this->show_one('notice', $text, $title);
	}
	
	
	private function compose($type = 'notice', $text = '', $title = NULL)
	{
		$data = array(
			'title' => $title,
			'text' => $text
		);

		return $this->_CI->load->view('msg/' . $type, $data, TRUE);
	}
	
	
}


/* End of file app/libraries/Msg.php */