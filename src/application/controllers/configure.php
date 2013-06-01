<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Classroombookings. Hassle-free resource booking for schools. <http://classroombookings.com/>
 * Copyright (C) 2006-2011 Craig A Rodway <craig.rodway@gmail.com>
 *
 * This file is part of Classroombookings.
 * Classroombookings is licensed under the Affero GNU GPLv3 license.
 * Please see license-classroombookings.txt for the full license text.
 */
 
class Configure extends MY_Controller
{
	
	
	function __construct()
	{
		parent::__construct();
		$this->lang->load('configure');
		$this->lang->load('settings');
		$this->data['nav_current'][] = 'configure';
		
		$this->layout->add_breadcrumb(lang('configure'), 'configure');
	}
	
	
	
	/**
	 * PAGE: Configure
	 */
	function index()
	{
		$this->layout->set_title(lang('configure'));
	}
	
	
	
	
	/*
	 * Main settings page
	 */
	function settings()
	{
		$this->auth->restrict('crbs.configure.settings');
		
		$this->layout->add_breadcrumb(lang('settings'), 'configure/settings');
		
		if ($this->input->post())
		{
			$this->form_validation->set_rules('school_name', 'School name', 'required|max_length[100]|trim')
								  ->set_rules('school_url', 'Website address', 'required|max_length[255]|prep_url|trim')
								  ->set_rules('timetable_view', 'Timetable view', 'required')
								  ->set_rules('timetable_cols', 'Timteable columns', 'required');
			
			if ($this->form_validation->run())
			{
				$options = array(
					'school_name' => $this->input->post('school_name'),
					'school_url' => $this->input->post('school_url'),
					'timetable_view' => $this->input->post('timetable_view'),
					'timetable_cols' => $this->input->post('timetable_cols'),
				);
				
				if ($this->options_model->set($options))
				{
					$this->flash->set('success', 'Settings have been updated successfully.', TRUE);
					
					Events::trigger('settings_general_update', array('options' => $options));
					
					redirect(current_url());
				}
				else
				{
					$this->flash->set('error', 'The settings could not be updated. Please try again.');
				}
			}
		}
		
		$this->load->library('form');
		
		$this->data['settings'] = $this->options_model->get_all(TRUE);
	}
	
	
	
	
	/**
	 * FORM POST: Save main settings
	 */
	function save_settings()
	{
		$this->auth->check('crbs.configure.settings');
		
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
			
			if ($this->settings->save($data))
			{
				$this->flash->set('success', lang('configure_settings_save_success'));
			}
			else
			{
				$this->flash->set('error', lang('configure_settings_save_error'));
			}
			
			redirect('configure/settings');
		}
	}
	
	
	
	
}

/* End of file ./application/controllers/configure.php */