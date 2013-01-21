<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

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

class Flash
{
	
	
	protected $CI;		// CodeIgntier object
	
	private $_msgs;		// Array of messages
	
	
	public function __construct()
	{
		$this->CI =& get_instance();
	}
	
	
	
	
	/**
	 * Set a new flash message
	 */
	public function set($type = 'success', $text = '', $session = FALSE)
	{
		// Get the markup for this flash message and store in internal array
		$this->_msgs[] = $this->string($type, $text);
		
		if ($session == TRUE)
		{
			// Set the flashdata to all of the messages combined
			$this->CI->session->set_flashdata('flash', implode('', $this->_msgs));
		}
		
	}
	
	
	
	
	/**
	 * Get the flash message
	 */
	public function get()
	{
		$msgs = '';
		if (empty($this->_msgs))
		{
			$msgs = $this->CI->session->flashdata('flash');
		}
		else
		{
			$msgs = implode('', $this->_msgs);
		}
		return $msgs;
	}
	
	
	
	
	/**
	 * Get a markup-formatted flash message.
	 *
	 * Useful for showing inline in the page without redirecting
	 *
	 * @param string $type		Type of message (error|success|info)
	 * @param string $text		Text of message to display
	 * @return string		HTML markup for message
	 */
	public function string($type = 'success', $text = '')
	{
		$data = array(
			'type' => $type,
			'text' => $text,
		);
		
		$html = $this->CI->load->view('parts/flash', $data, TRUE);
		
		return $html;
	}
	
	
}

/* End of file: ./application/libaries/Flash.php */