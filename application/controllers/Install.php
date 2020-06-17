<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Install extends MY_Controller
{


	/**
	 * Installation step. Populated from session if present.
	 *
	 * @var string
	 *
	 */
	private $step = 'config';


	/**
	 * Keep track of error messages during final install step.
	 *
	 * @var array
	 *
	 */
	private $errors = array();


	public function __construct()
	{
		parent::__construct();

		$this->load->model('crud_model');
		$this->load->helper('file');
		$this->load->helper('array');
		$this->load->library('userauth');

		// First, check if we are upgrading from v1.
		if ($this->check_upgrade()) {
			redirect('upgrade');
		}

		if (config_item('is_installed') && ! isset($_SESSION['install_complete'])) {
			$this->session->set_flashdata('saved', msgbox("info", "Classroombookings is already installed."));
			return redirect('');
		}

		$this->init();
	}


	private function check_upgrade()
	{
		return (is_dir(FCPATH . 'webroot') && is_dir(FCPATH . 'system'));
	}


	/**
	 * Get the available DB drivers
	 *
	 */
	private function get_db_drivers()
	{
		$drivers = array();

		if (extension_loaded('PDO') && extension_loaded('pdo_mysql')) {
			$drivers[] = 'pdo';
		}

		if (function_exists('mysqli_connect')) {
			$drivers[] = 'mysqli';
		}

		return $drivers;
	}


	/**
	 * Initialisation for all pages of installation process.
	 *
	 * Just check session, init vars and things that are needed.
	 *
	 */
	private function init()
	{
		if (isset($_SESSION['install_step'])) {
			$this->step = $_SESSION['install_step'];
		}

		if ( ! isset($_SESSION['data'])) {
			$_SESSION['data'] = array();
		}

		if ( ! isset($_SESSION['requirements'])) {
			$_SESSION['requirements'] = array();
		}

		if ( ! array_key_exists('install_complete', $_SESSION)) {
			$_SESSION['install_complete'] = FALSE;
		}
	}


	public function index()
	{
		redirect('install/' . $this->step);
	}


	public function config()
	{
		$notices = '';

		$drivers = $this->get_db_drivers();
		if (empty($drivers)) {
			$notices .= msgbox('error', "Your PHP configuration does not have any MySQL database drivers available.");
			$notices .= msgbox('error', "Install or enable extension 'mysqli' or 'pdo_mysql' (preferred).");
		}

		if (is_file(FCPATH . 'local/config.php') && ! empty($drivers)) {
			// Config file already present
			$local_config = require(FCPATH . 'local/config.php');
			if ( ! is_array($local_config)) {
				show_error("The 'local/config.php' file is not in a recognised format.");
			}
			$db_config = element('database', $local_config, array());
			$_SESSION['db_config'] = $db_config;
			$test_db = $this->load->database($db_config, TRUE);
			$res = $test_db->initialize();
			if ( ! $res) {
				$this->data['notice'] = msgbox('error', "Could not connect to the database using the details in your local/config.php file.");
			} else {
				$_SESSION['requirements']['database'] = array('status' => 'ok');
				$_SESSION['install_step'] = 'info';
				$_SESSION['step_config'] = TRUE;
				redirect('install/info');
			}
		}

		if ($this->input->post()) {

			$this->form_validation->set_rules('hostname', 'Hostname', 'required');
			$this->form_validation->set_rules('database', 'Database', 'required');
			$this->form_validation->set_rules('username', 'Username', 'required');
			$this->form_validation->set_rules('password', 'Password', 'required');
			$this->form_validation->set_rules('url', 'URL', 'required|valid_url');

			if ($this->form_validation->run() == FALSE) {

				$this->data['notice'] = validation_errors();

			} else {

				// Check DB
				$data = array(
					'driver' => defined('PDO::ATTR_DRIVER_NAME') ? 'pdo' : 'mysqli',
					'hostname' => $this->input->post('hostname'),
					'database' => $this->input->post('database'),
					'username' => $this->input->post('username'),
					'password' => $this->input->post('password'),
					'url' => $this->input->post('url'),
				);

				$db_config = array(
					'dbdriver' => $data['driver'],
					'hostname' => $data['hostname'],
					'database' => $data['database'],
					'username' => $data['username'],
					'password' => $data['password'],
					'subdriver' => ($data['driver'] == 'pdo' ? 'mysql' : ''),
				);

				$_SESSION['db_config'] = $db_config;

				$test_db = $this->load->database($db_config, TRUE);
				$res = $test_db->initialize();

				if ( ! $res) {
					$this->data['notice'] = msgbox('error', "Could not connect to the database with the provided values.");
				} else {
					if ( ! $err) {
						$_SESSION['requirements']['database'] = array('status' => 'ok');
						$_SESSION['data'] = array_merge($_SESSION['data'], $data);
						$_SESSION['install_step'] = 'info';
						$_SESSION['step_config'] = TRUE;
						redirect('install/info');
					}
				}
			}

		}

		$this->data['title'] = 'Install classroombookings - Configuration';
		$this->data['showtitle'] = $this->data['title'];

		$columns = array(
			'c1' => array(
				'content' => $this->load->view('install/config', $this->data, TRUE),
				'width' => '70%',
			),
			'c2' => array(
				'content' => $this->load->view('install/config_side', $this->data, TRUE),
				'width' => '30%',
			),
		);

		$this->data['body'] = $notices . $this->load->view('columns', $columns, TRUE);

		return $this->render();
	}


	public function info()
	{
		if ( ! isset($_SESSION['step_config'])) {
			return redirect('install/config');
		}

		if ($this->input->post()) {

			$this->form_validation->set_rules('name', 'School name', 'required');
			$this->form_validation->set_rules('admin_username', 'Username', 'required');
			$this->form_validation->set_rules('admin_password', 'Password', 'required');

			if ($this->form_validation->run() == FALSE) {

				$this->data['notice'] = validation_errors();

			} else {

				$data = array(
					'name' => $this->input->post('name'),
					'admin_username' => $this->input->post('admin_username'),
					'admin_password' => $this->input->post('admin_password'),
				);

				$_SESSION['data'] = array_merge($_SESSION['data'], $data);
				$_SESSION['install_step'] = 'checks';
				$_SESSION['step_info'] = TRUE;
				redirect('install/checks');
			}
		}

		$this->data['title'] = 'Install classroombookings - Information';
		$this->data['showtitle'] = $this->data['title'];

		$columns = array(
			'c1' => array(
				'content' => $this->load->view('install/info', $this->data, TRUE),
				'width' => '70%',
			),
			'c2' => array(
				'content' => $this->load->view('install/info_side', $this->data, TRUE),
				'width' => '30%',
			),
		);

		$this->data['body'] = $this->load->view('columns', $columns, TRUE);

		return $this->render();
	}


	public function checks()
	{
		if ( ! isset($_SESSION['step_info'])) {
			return redirect('install/info');
		}

		if ($this->input->post()) {

			$res = $this->start_install();

			if ( ! $res) {

				if (empty($this->errors)) {
					$msg = 'An unknown error occurred.';
				} else {
					$msg = implode("<br>", array_values($this->errors));
				}

				$this->data['notice'] = msgbox('error', $msg);

			} else {


				$_SESSION['install_complete'] = TRUE;
				$_SESSION['step_checks'] = TRUE;
				$_SESSION['install_step'] = 'complete';
				redirect('install/complete');

			}
		}

		// Check requirements (again). It will (re-)populate the session var with results
		$this->check_requirements();

		$this->data['requirements'] = $_SESSION['requirements'];

		$this->data['title'] = 'Install classroombookings - Check requirements';
		$this->data['showtitle'] = $this->data['title'];
		$this->data['body'] = $this->load->view('install/check', $this->data, TRUE);

		return $this->render();
	}


	public function complete()
	{
		if ( ! isset($_SESSION['step_checks']) && ! isset($_SESSION['install_complete'])) {
			return redirect('install/checks');
		}

		$this->cleanup();

		$this->load->database();
		$this->load->library('userauth');

		$this->data['title'] = 'Install classroombookings';
		$this->data['showtitle'] = $this->data['title'];
		$this->data['body'] = $this->load->view('install/complete', $this->data, TRUE);

		return $this->render();
	}


	private function cleanup()
	{
		unset($_SESSION['data']);
		unset($_SESSION['requirements']);
		unset($_SESSION['install_step']);
		unset($_SESSION['step_config']);
		unset($_SESSION['step_info']);
		unset($_SESSION['step_checks']);
		unset($_SESSION['db_config']);
	}


	private function check_requirements()
	{
		// PHP version
		//
		$_SESSION['requirements']['php_version'] = array('message' => 'Your PHP version is ' . PHP_VERSION . '.');
		$has_php = (version_compare(PHP_VERSION, '5.5.0', '>='));
		if ( ! $has_php) {
			$_SESSION['requirements']['php_version']['status'] = 'err';
		} else {
			$_SESSION['requirements']['php_version']['status'] = 'ok';
		}

		// PHP GD library
		//
		$has_gd = (extension_loaded('gd') && function_exists('imagecreate'));
		$message = '';
		$gd_status = ($has_gd ? 'ok' : 'err');
		if ( ! $has_gd) {
			$message = "Please install and/or enable the 'php_gd' module in your PHP configuration.";
		}
		$_SESSION['requirements']['php_module_gd'] = array('status' => $gd_status, 'message' => $message);

		// PHP LDAP module
		//
		$has_ldap = (extension_loaded('ldap'));
		$message = '';
		$ldap_status = ($has_ldap ? 'ok' : 'warn');
		if ( ! $has_ldap) {
			$message = "The 'php_ldap' module is only needed if you want to use LDAP authentication.";
		}
		$_SESSION['requirements']['php_module_ldap'] = array('status' => $ldap_status, 'message' => $message);

		// 'local' folder
		//
		$local_path = FCPATH . 'local';
		$message = '';
		$has_local = TRUE;
		if ( ! is_dir($local_path))
		{
			if ( ! mkdir($local_path, 0700, TRUE))
			{
				$message = "'local' folder does not exist and could not be created.";
				$has_local = FALSE;
			}
		}
		elseif ( ! is_writable($local_path))
		{
			$has_local = FALSE;
			$message = "'local' folder does not have writable permissions.";
		}

		$local_status = ($has_local ? 'ok' : 'err');

		$_SESSION['requirements']['folder_local'] = array('status' => $local_status, 'message' => $message);

		// 'uploads' folder
		//
		$uploads_path = FCPATH . 'uploads';
		$message = '';
		$has_uploads = TRUE;
		if ( ! is_dir($uploads_path))
		{
			if ( ! mkdir($uploads_path, 0700, TRUE))
			{
				$message = "'uploads' folder does not exist and could not be created.";
				$has_uploads = FALSE;
			}
		}
		elseif ( ! is_writable($uploads_path))
		{
			$has_uploads = FALSE;
			$message = "'uploads' folder does not have writable permissions.";
		}

		$uploads_status = ($has_uploads ? 'ok' : 'err');

		$_SESSION['requirements']['folder_uploads'] = array('status' => $uploads_status, 'message' => $message);

		// Database
		//
		$db_config = $_SESSION['db_config'];
		$test_db = $this->load->database($db_config, TRUE);
		$res = $test_db->initialize();
		$num_tables = count($test_db->list_tables());

		if ( ! $res) {
			$_SESSION['requirements']['database'] = array('status' => 'err', 'message' => 'Could not connect with the provided settings.');
		} else {
			$_SESSION['requirements']['database'] = array('status' => 'ok');
		}

		if ($num_tables > 0) {
			$_SESSION['requirements']['database_empty'] = array('status' => 'err', 'message' => "There are {$num_tables} tables in the database.");
		} else {
			$_SESSION['requirements']['database_empty'] = array('status' => 'ok');
		}
	}


	private function start_install()
	{
		// Set up temp DB connection
		//
		if (is_file(FCPATH . 'local/config.php')) {

			// Config file already present
			$local_config = require(FCPATH . 'local/config.php');
			if ( ! is_array($local_config)) {
				show_error("The 'local/config.php' file is not in a recognised format.");
			}
			$db_config = element('database', $local_config, array());
			$db_config['db_debug'] = FALSE;

		} else {

			$db_config = array(
				'dbdriver' => $_SESSION['db_config']['dbdriver'],
				'subdriver' => $_SESSION['db_config']['subdriver'],
				'hostname' => $_SESSION['db_config']['hostname'],
				'database' => $_SESSION['db_config']['database'],
				'username' => $_SESSION['db_config']['username'],
				'password' => $_SESSION['db_config']['password'],
				'db_debug' => FALSE,
			);

		}

		$this->load->database($db_config);
		$this->load->dbforge();

		// Each function should simply return TRUE/FALSE based on whether it succeeded.
		// If an error occurs, the function should populate var: $this->errors['section'] = 'ERROR_MESSAGE';
		//

		if ( ! $this->install_structure()) {
			log_message("error", "Install: structure failed.");
			return FALSE;
		}

		if ( ! $this->install_settings()) {
			log_message("error", "Install: settings failed.");
			return FALSE;
		}

		if ( ! $this->install_user()) {
			log_message("error", "Install: user failed.");
			return FALSE;
		}

		if ( ! $this->install_config()) {
			log_message("error", "Install: config failed.");
			return FALSE;
		}

		write_file(FCPATH . 'local/installed', date("Y-m-d H:i:s"));

		return TRUE;
	}


	/**
	 * Create the tables in the database.
	 *
	 */
	private function install_structure()
	{
		$errors = array();

		$this->load->model('install_model');
		$res = $this->install_model->run();

		if (is_array($res) && count($res) > 0) {
			log_message("error", "Error creating tables: " . json_encode($res));
			$this->errors['structure'] = "There was a problem creating the following tables: " . implode(', ', array_values($res));
			return FALSE;
		}

		return TRUE;
	}


	/**
	 * Insert rows for the initial settings - some default, some captured from the user.
	 *
	 */
	private function install_settings()
	{
		// Default settings + school name
		//
		$settings = array(
			'bia' => '0',
			'colour' => '468ED8',
			'displaytype' => 'day',
			'd_columns' => 'periods',
			'logo' => '',
			'website' => '',
			'name' => $_SESSION['data']['name'],
		);

		$this->load->model('settings_model');
		return $this->settings_model->set($settings);
	}


	/**
	 * Create initial user account for admin
	 *
	 */
	private function install_user()
	{
		// Create first admin user

		$user = array(
			'username' => $_SESSION['data']['admin_username'],
			'password' => password_hash($_SESSION['data']['admin_password'], PASSWORD_DEFAULT),
			'authlevel' => ADMINISTRATOR,
			'enabled' => '1',
		);

		$this->load->model('users_model');
		return $this->users_model->Add($user);
	}


	/**
	 * Save settings to config file
	 *
	 */
	private function install_config()
	{
		if (is_file(FCPATH . 'local/config.php')) {
			// Skip writing if it already exists
			return TRUE;
		}

		// Otherwise, get template, put values in it, and save.
		$config_tpl = file_get_contents(APPPATH . 'install/config.tpl.php');

		$data = array(
			'base_url' => $_SESSION['data']['url'],
			'db_dsn' => '',
			'db_host' => $_SESSION['db_config']['hostname'],
			'db_name' => $_SESSION['db_config']['database'],
			'db_user' => $_SESSION['db_config']['username'],
			'db_pass' => $_SESSION['db_config']['password'],
			'db_driver' => $_SESSION['db_config']['dbdriver'],
			'db_subdriver' => $_SESSION['db_config']['subdriver'],
		);

		$this->load->library('parser');
		$config_contents = $this->parser->parse_string($config_tpl, $data);

		$res = write_file(FCPATH . 'local/config.php', $config_contents);

		if ( ! $res) {
			log_message("error", "Unable to save config to file.");
			$this->errors['config'] = "Could not save config to file.";
		}

		return $res;
	}


}
