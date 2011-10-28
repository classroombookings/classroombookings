<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Classroombookings. Hassle-free resource booking for schools. <http://classroombookings.com/>
 * Copyright (C) 2006-2011 Craig A Rodway <craig.rodway@gmail.com>
 *
 * This file is part of Classroombookings.
 * Classroombookings is licensed under the Affero GNU GPLv3 license.
 * Please see license-classroombookings.txt for the full license text.
 */
 
class Configure extends Configure_Controller
{
	
	
	function __construct(){
		parent::__construct();
		//$this->load->model('security_model');
	}
	
	
	
	/**
	 * PAGE: Configure
	 */
	function index()
	{
		//$this->auth->check('configure');
		if ($this->auth->check('crbs.configure', true))
		{
			return $this->settings();
		}
		else
		{
			$data['body'] = '';
			$this->page($data);
		}
	}
	
	
	
	
	/*
	 * PAGE: Main settings
	 */
	function settings()
	{
		$this->auth->check('crbs.configure');
		
		// Retrieve settings
		$settings_list = array('school_name', 'school_url', 'timetable_view', 'timetable_cols');
		$body['settings'] = $this->settings->get($settings_list);
		
		$data['title'] = 'Configure';
		$data['body'] = $this->load->view('configure/settings', $body, true);
		$this->page($data);
	}
	
	
	
	
	/**
	 * FORM POST: Save main settings
	 */
	function save_settings()
	{
		$this->auth->check('crbs.configure');
		
		$this->form_validation->set_rules('school_name', 'School name', 'required|max_length[100]|trim');
		$this->form_validation->set_rules('school_url', 'Website address', 'max_length[255]|prep_url|trim');
		$this->form_validation->set_rules('timetable_view', 'Timetable view', 'required');
		$this->form_validation->set_rules('timetable_cols', 'Timteable columns', 'required');
		$this->form_validation->set_error_delimiters('<li>', '</li>');
		
		if ($this->form_validation->run() == false)
		{
			// Validation failed
			return $this->settings();
		}
		else
		{
			$data['school_name'] = $this->input->post('school_name');
			$data['school_url'] = $this->input->post('school_url');
			$data['timetable_view'] = $this->input->post('timetable_view');
			$data['timetable_cols'] = $this->input->post('timetable_cols');
			
			$this->settings->save($data);
			
			$this->session->set_flashdata('flash', 
				$this->msg->notice(lang('CONF_MAIN_SAVE_OK')));
			redirect('configure/settings');
		}
	}
	
	
	
	
}



/* End of file app/controllers/configure.php */
