<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

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

/**
 * Library to handle logging of events
 */

class Logger
{
	
	
	protected $CI;		// CodeIgntier object
	
	
	public function __construct()
	{
		$this->CI =& get_instance();
		
		$this->CI->load->library('user_agent');
		$this->CI->load->model('log_model');
	}
	
	
	
	
	public function add($type = '', $data = array(), $u_id = 0)
	{
		// Use session user ID if not supplied
		if ($u_id === 0)
		{
			$u_id = $this->CI->session->userdata('u_id');
			$username = $this->CI->session->userdata('u_username');
		}
		
		if (strpos($type, '/') === FALSE)
		{
			$area = element('area', $data, 'system');
		}
		else
		{
			list ($area, $type) = explode('/', $type);
		}
		
		// Get description for this event
		$description = $this->_get_description($area, $type, $data);
		
		// Data to insert into database for this entry
		$log_data = array(
			'l_u_id' => $u_id,
			'l_username' => $username,
			'l_type' => $type,
			'l_area' => $area,
			'l_uri' => $this->CI->uri->uri_string(),
			'l_description' => $description,
			'l_datetime' => date('Y-m-d H:i:s'),
			'l_ua' => $this->CI->agent->agent_string(),
			'l_browser' => $this->CI->agent->browser() . ' ' . $this->CI->agent->version(),
			'l_ip' => $this->CI->input->ip_address(),
		);
		
		return $this->CI->log_model->insert($log_data);
	}
	
	
	
	
	/** 
	 * Get the description text for a given activity from the driver for that type
	 */
	private function _get_description($area = '', $type = '', $data = array())
	{
		// Get the source string from the language file
		$string = lang("{$area}_event_{$type}");
		
		// Parse the data array and put in an array for tag replacement in description texts
		$processed_data = $this->_process_data_array($data);
		
		return str_replace($processed_data['keys'], $processed_data['values'], $string);
	}
	
	
	
	
	private function _process_data_array($data = array())
	{
		$keys = array();
		$values = array();
		
		foreach ($data as $key => $value)
		{
			if ( ! is_array($value))
			{
				$keys[] = "[$key]";
				$values[] = $value;
			}
			else
			{
				$processed = $this->_process_data_array($value);
				$keys = array_merge($keys, $processed['keys']);
				$values = array_merge($values, $processed['values']);
			}
		}
		
		return array(
			'keys' => $keys,
			'values' => $values,
		);
		
	}
	
	
	
	
}

/* End of file: ./application/libaries/Logger.php */