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


/**
 * Form layout class
 *
 * Makes it easier to build repetitive forms
 *
 * @author		Craig A Rodway
 */
class Form
{
	
	// Handle to CI
	private $CI;
	
	// Form section data
	private $sections = array();
	private $inputs = array();
	private $hints = array();
	private $buttons = array();
	
	// Opening form string
	private $form_open_str = '';
	
	/**
	 * Create a new form. Params like form_open()
	 */
	public function __construct($action_uri = '', $params = array(), $hidden = array())
	{
		$this->form_open_str = form_open($action_uri, $params, $hidden);
		$this->CI =& get_instance();
	}
	
	
	
	
	public function add_section($id = '', $title = '', $hint = '')
	{
		$this->sections[$id]['title'] = $title;
		
		if ($hint !== '')
		{
			$this->sections[$id]['hint'] = $hint;
		}
		
		return $this;
	}
	
	
	
	
	public function add_input($section = '', $id = '', $label = '')
	{
		$this->inputs[$section][$id]['label'] = $label;
		return $this;
	}
	
	
	
	
	public function set_content($section = '', $id = '', $content = '')
	{
		$this->inputs[$section][$id]['content'] = $content;
		return $this;
	}
	
	
	
	
	public function set_hint($section = '', $id = '', $hint = '')
	{
		$this->inputs[$section][$id]['hint'] = $hint;
	}
	
	
	
	
	public function add_button($content = '')
	{
		$this->buttons[] = $content;
	}
	
	
	
	public function render()
	{
		$output = $this->form_open_str . "\n";
		
		$data = array(
			'sections' => $this->sections,
			'inputs' => $this->inputs,
			'hints' => $this->hints,
			'buttons' => $this->buttons,
		);
		
		$output .= $this->CI->load->view('parts/form', $data, TRUE);
		
		$output .= "</form>\n";
		
		return $output;
	}
	
}

/* End of file: ./application/libraries/Form.php */