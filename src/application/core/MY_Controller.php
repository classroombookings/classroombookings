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

class MY_Controller extends CI_Controller
{
	
	
	protected $data;		// global data available to all views
	protected $json;		// array of data to return as JSON
	protected $view = NULL;		// primary content view file to use
	protected $auto_view = TRUE;		// automatically load view
	
	
	function __construct()
	{
		parent::__construct();
		
		$this->load->driver('auth');
		
		if ( ! isset($this->data['nav_current']))
		{
			$this->data['nav_current'] = array();
		}
		
		// Get school info
		$school = $this->_get_school();
		
		// Store school ID in config
		$this->config->set_item('s_id', $school['s_id']);
		
		// Load the options model to retrieve the school options
		$this->load->model('options_model');
		
		// Get options for school and store using CI config 
		$school_options = $this->options_model->get_all();
		$this->config->set_item('options', $school_options);
		
		// Load all other models necessary for running core app
		$this->load->model(array('permissions_model', 'roles_model', 'weeks_model', 'users_model'));
		
		// Configure layout and default assets
		$css = array('normalise', 'global', 'amazium', 'crbs', site_url('css'));
		
		$js = array(
			'libraries/jquery-1.8.2.min',
			'libraries/ICanHaz.min',
			'plugins/jquery.cookie',
			'plugins/jquery.simplemodal.1.4.4.min',
			'plugins/jquery.hotkeys',
			'views/default',
		);
		
		$template = 'default';
		
		// set layout
		$this->layout->set_css($css)
					 ->set_js($js)
					 ->set_template($template);
		
		// Enable profiler in development mode only and when GET param is present
		$this->output->enable_profiler(ENVIRONMENT === 'development' && $this->input->get('profiler'));
		
		// Do routine maintenance for active users
		$this->_manage_active_users();
	}
	
	
	
	
	/**
	 * Remap the CI request, running the requested method and (auto-)loading the view
	 */
	public function _remap($method, $arguments)
	{
		if (method_exists($this, $method))
		{
			// Requested method exists in the class - run it
			call_user_func_array(array($this, $method), array_slice($this->uri->rsegments, 2));
		}
		else
		{
			// Doesn't exist - show 404 error.
			show_404(strtolower(get_class($this)) . '/' . $method);
		}
		
		// The class function has ran, done its stuff and set $this->data vars...
		// ... now auto-load the view.
		$this->_load_view();
	}
	
	
	
	
	/** 
	 * Auto-load the view based on path.
	 *
	 * If $view is FALSE, then don't.
	 */
	protected function _load_view()
	{
		// Back out if we've explicitly set the view to FALSE
		if ($this->view === FALSE)
		{
			return;
		}
		
		// If the JSON data is set, respond with JSON data instead of whole page
		if (is_array($this->json))
		{
			$this->output->set_content_type('text/json');
			$this->output->set_output(json_encode($this->json, JSON_NUMERIC_CHECK));
			return;
		}
		
		if ($this->auto_view === TRUE)
		{
			if ( ! ($this->layout->has_content('content') OR $this->layout->has_view('content')))
			{
				$view = $this->router->directory . $this->router->class . '/' . $this->router->method;
				if (file_exists(APPPATH . "views/default/$view.php"))
				{
					$this->layout->set_view('content', "default/$view");
				}
				else
				{
					$content = $this->flash->string('error', '<strong>System Error</strong> - required view file ' . $view . ' not found.');
					$this->layout->set_content('content', '<br>' . $content);
				}
			}
		}
		
		// Load the navigation
		$this->load->config('nav');
		$nav = config_item('nav');
		
		$this->data['nav']['primary'] = $this->layout->get_nav_level($nav, 0, $this->data['nav_current']);
		
		// Load the variables from $this->data so they can be accessed in the layout view
		$this->load->vars($this->data);
		
		// Set content for flash messages
		$flash = $this->flash->get();
		$validation_errors = validation_errors('<li>', '</li>');
		if ( ! empty($validation_errors))
		{
			$flash .= $this->flash->string('error', '<ul>' . $validation_errors . '</ul>');
		}
		
		$this->layout->set_content('flash', $flash);
		
		// Finally load the template as the final view (it should echo $content at least)
		$this->load->view($this->layout->get_template());
	}
	
	
	
	
	/**
	 * Get the school being used.
	 *
	 * In multi-site mode, the first part of the hostname should refer to a school.
	 * In single-site mode, get the first (and only) school in the DB.
	 *
	 * @return array
	 */
	private function _get_school()
	{
		$this->load->model('schools_model');
		$this->schools_model->limit(1);
		
		log_message('debug', 'MY_Controller: _get_school(): Mode: ' . config_item('mode'));
		
		// Check which mode the app is running in to determine how to load
		if (config_item('mode') === 'multi')
		{
			$host = preg_replace('/www\./', '', $this->input->server('HTTP_HOST'));
			
			log_message('debug', 'MY_Controller: _get_school(): Host: ' . $host);
			
			$subdomain = current(explode($host));
			
			log_message('debug', 'MY_Controller: _get_school(): Subdomain: ' . $subdomain);
			
			$school = $this->schools_model->get_by('sch_subdomain', $subdomain);
			
			if ( ! $school)
			{
				show_error("School $subdomain not found.", 404);
			}
		}
		elseif (config_item('mode') === 'single')
		{
			// Single site. Just get the only school
			$schools = $this->schools_model->get_all();
			
			if ( ! $schools)
			{
				show_error('School not configured');
			}
			$school = $schools[0];
		}
		
		log_message('debug', 'MY_Controller: _get_school(): Name: ' . $school['s_name']);
		
		// Return school
		return $school;
	}
	
	
	
	
	private function _manage_active_users()
	{
		if ($this->auth->is_logged_in())
		{
			//echo "Updating active user entry. ";
			$this->users_model->set_active($this->session->userdata('u_id'), $this->session->userdata('active_token'));
		}
		
		//echo $this->db->last_query();
		
		$this->users_model->prune_active();
	}
	
	
}




class Configure_Controller extends MY_Controller
{


	public function __construct()
	{
		parent::__construct();
		$this->data['nav_current'][] = 'configure';
		$this->layout->add_breadcrumb(lang('configure'), 'configure');
	}


}