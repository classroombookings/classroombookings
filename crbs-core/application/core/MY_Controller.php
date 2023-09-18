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

		$this->crbs_startup();
		// $this->init_events();
		$this->load_main();
		$this->profiler();

		$this->data['scripts'] = [
			(ENVIRONMENT === 'development') ? 'assets/js/main.js' : 'assets/js/main.min.js',
		];
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


	private function crbs_startup()
	{
		if ( ! CRBS_MANAGED) {
			$this->load->driver('cache', [
				'adapter' => 'file',
			]);
			return;
		}

		$this->benchmark->mark('startup_start');

		$package_path = (CRBS_MANAGED)
			? ROOTPATH.'crbs-managed'
			: ROOTPATH.'packages';

		$this->load->add_package_path($package_path);
		$this->load->library('startup');

		$this->benchmark->mark('startup_end');
	}


	private function profiler()
	{
		$this->output->enable_profiler(config_item('show_profiler') === TRUE);

		if (CRBS_MANAGED && ENVIRONMENT !== 'production' && $this->input->get('profiler') == 1) {
			$this->output->enable_profiler(true);
		}
	}


	private function load_main()
	{
		if (get_class($this) == 'Install' || get_class($this) == 'Upgrade') return;

		if ( ! CRBS_MANAGED && ! config_item('is_installed')) {
			redirect('install');
		}

		$this->load->database();

		$tz = setting('timezone');
		if (!empty($tz)) {
			date_default_timezone_set($tz);
		}

		$this->load->library('session');
		$this->load->library('form_validation');
		$this->load->library('userauth');

		if ( ! CRBS_MANAGED) {
			$this->load->library('migration');
			$this->migration->latest();
		}

		$this->lang->load('crbs');

		$this->load->helper([
			'language',
			'user_file',
		]);
	}


}

if (is_file(ROOTPATH . 'crbs-managed/core/API_Controller.php')) {
	require_once(ROOTPATH . 'crbs-managed/core/API_Controller.php');
}
