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
 
class Css extends MY_Controller
{


	function __construct()
	{
		parent::__construct();
	}
	
	
	
	
	public function index()
	{
		$this->view = FALSE;
		$this->output->set_content_type('text/css');
		$this->output->set_output('');
		
		
		// Load things needed to generate the CSS
		//$this->data['weeks'] = presenters('Week', $this->weeks_model->get());
		
		//$this->load->view('default/css/index', $this->data);
	}
	
	
	
	
}

/* End of file: ./application/controllers/css.php */