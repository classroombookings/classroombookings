<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Classroombookings. Hassle-free resource booking for schools. <http://classroombookings.com/>
 * Copyright (C) 2006-2011 Craig A Rodway <craig.rodway@gmail.com>
 *
 * This file is part of Classroombookings.
 * Classroombookings is licensed under the Affero GNU GPLv3 license.
 * Please see license-classroombookings.txt for the full license text.
 */

class CB_Controller extends CI_Controller
{
	
	private $_tpl;
	
	function __construct()
	{
		parent::__construct();
		// Set template
		// TODO: determine whether or not to use mobile or desktop
		$this->_tpl = 'template/layout-desktop';
		// Enable profiling or not
		$this->output->enable_profiler($this->config->item('profiler'));
		// Set up session if need to
		$this->_init_session();
	}
	
	
	function page($data)
	{
		// Top left menu
		$header_left['menu'] = $this->menu_model->main();
		$default['header_left'] = $this->load->view('template/layout-desktop.header-left.php', $header_left, true);
		$default['header_right'] = $this->load->view('template/layout-desktop.header-right.php', null, true);
		
		$default['sidebar'] = '';
		$default['body'] = '';
		
		$data = array_merge($default, $data);
		$this->load->view($this->_tpl, $data);
	}
	
	
	private function _init_session()
	{
		log_message('debug', 'Creating anonymous session if required.');
		$user_id = $this->session->userdata('user_id');
		if (empty($user_id))
		{
			log_message('debug', '_init_session(): Session user_id is empty.');
			$session_made = $this->auth->session_create_anon();
			if (!$session_made)
			{
				show_error($this->auth->lasterr, 500);
			}
		}
	}
	
}




class Configure_Controller extends CB_Controller
{


	public function __construct()
	{
		parent::__construct();
	}
	
	
	
	public function page($data)
	{
		$sidebar['menu'] = $this->menu_model->configure();
		$data['sidebar'] = $this->load->view('configure/sidebar', $sidebar, true);
		parent::page($data);
	}


}