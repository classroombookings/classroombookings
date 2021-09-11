<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{


	/**
	 * Global data for view
	 *
	 * @var array
	 *
	 */
	public $data = array();


	public function __construct()
	{
		parent::__construct();

		$this->output->enable_profiler(config_item('show_profiler') === TRUE);

		$this->load->library('session');
		$this->load->library('form_validation');

		$this->data['show_headway_widget'] = FALSE;

		if (get_class($this) !== 'Install' && get_class($this) !== 'Upgrade') {

			if ( ! config_item('is_installed')) {
				redirect('install');
			}

			$this->load->database();
			$this->load->library('userauth');

			$this->load->library('migration');
			$this->migration->latest();

			$this->lang->load('crbs');
			$this->load->helper('language');

			$tz = setting('timezone');
			if (strlen($tz)) {
				date_default_timezone_set($tz);
			}

			if ($this->userauth->is_level(ADMINISTRATOR) && setting('headway_widget_enabled')) {
				$this->data['show_headway_widget'] = TRUE;
			}
		}

		$this->data['scripts'] = array();
		$this->data['scripts'][] = 'assets/js/lib/sorttable.js';
		$this->data['scripts'][] = 'assets/js/lib/datepicker.js';
		$this->data['scripts'][] = 'assets/js/lib/es6-promise.auto.min.js';
		$this->data['scripts'][] = 'assets/js/lib/unpoly.min.js';
		$this->data['scripts'][] = 'assets/js/lib/accessible-autocomplete.min.js';
		$this->data['scripts'][] = 'assets/js/main.js';
	}


	public function require_logged_in($msg = TRUE)
	{
		// Check loggedin status
		if ( ! $this->userauth->logged_in()) {
			if ($msg) {
				$this->session->set_flashdata('auth', msgbox('error', $this->lang->line('crbs_mustbeloggedin')));
			}
			redirect('login');
		}
	}


	public function require_auth_level($level)
	{
		if ( ! $this->userauth->is_level($level)) {
			$this->session->set_flashdata('auth', msgbox('error', $this->lang->line('crbs_mustbeadmin')));
			redirect(site_url());
		}
	}


	public function render($view_name = 'layout')
	{
		$this->load->view($view_name, $this->data);
	}


	public function render_up()
	{
		$this->load->view('unpoly', $this->data);
	}


}
