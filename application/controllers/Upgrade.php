<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Upgrade extends MY_Controller
{


	/**
	 * Installation step. Populated from session if present.
	 *
	 * @var string
	 *
	 */
	private $step = 'check';


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

		if (config_item('is_installed') && ! isset($_SESSION['install_complete'])) {
			$this->session->set_flashdata('saved', msgbox("info", "Classroombookings is already installed."));
			return redirect('');
		}
	}


	public function index()
	{
		redirect('upgrade/' . $this->step);
	}


	public function check()
	{
		if ($this->input->post()) {

			$this->form_validation->set_rules('url', 'URL', 'required|valid_url');

			if ($this->form_validation->run() == FALSE) {

				$this->data['notice'] = validation_errors();

			} else {

				$_SESSION['url'] = $this->input->post('url');

				$res = $this->start_upgrade();

				if ( ! $res) {

					if (empty($this->errors)) {
						$msg = 'An unknown error occurred.';
					} else {
						$msg = implode("<br>", array_values($this->errors));
					}

					$this->data['notice'] = msgbox('error', $msg);

				} else {

					$_SESSION['upgrade_complete'] = TRUE;
					$_SESSION['step_check'] = TRUE;
					$_SESSION['upgrade_step'] = 'complete';
					redirect('upgrade/complete');

				}

			}

		}

		// Check requirements (again). It will (re-)populate the session var with results
		$this->check_requirements();

		$this->data['requirements'] = $_SESSION['requirements'];

		$this->data['title'] = 'Upgrade classroombookings';
		$this->data['showtitle'] = $this->data['title'];
		$this->data['body'] = $this->load->view('upgrade/check', $this->data, TRUE);

		return $this->render();
	}


	public function complete()
	{
		if ( ! isset($_SESSION['step_check']) && ! isset($_SESSION['upgrade_complete'])) {
			return redirect('upgrade/check');
		}

		// Clear session things related to upgrade
		$this->cleanup();

		// Needed just to make sure layout loads OK - gets name/user info etc.
		$this->load->database();
		$this->load->library('userauth');

		$this->data['title'] = 'Upgrade classroombookings';
		$this->data['showtitle'] = $this->data['title'];
		$this->data['body'] = $this->load->view('upgrade/complete', $this->data, TRUE);

		return $this->render();
	}


	private function cleanup()
	{
		unset($_SESSION['upgrade_step']);
		unset($_SESSION['step_check']);
		unset($_SESSION['db_config']);
		unset($_SESSION['url']);
	}


	private function check_requirements()
	{
		// PHP version
		//
		$_SESSION['requirements']['php_version'] = array('message' => 'Your PHP version is ' . PHP_VERSION . '.');
		$has_php = (version_compare(PHP_VERSION, '5.5.0') >= 0);
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
		$db_config = $this->get_db_config();
		$test_db = $this->load->database($db_config, TRUE);
		$res = $test_db->initialize();

		if ( ! $res) {
			$_SESSION['requirements']['database'] = array('status' => 'err', 'message' => 'Could not connect with the provided settings.');
		} else {
			$_SESSION['requirements']['database'] = array('status' => 'ok', 'message' => $db_config['summary']);
		}

		// Check tables
		//

		$existing_tables = $test_db->list_tables();

		$expected_tables = array(
			'academicyears',
			'bookings',
			'departments',
			'holidays',
			'periods',
			'quotas',
			'roomfields',
			'roomoptions',
			'rooms',
			'roomvalues',
			'school',
			'users',
			'weekdates',
			'weeks',
		);

		$missing_tables = array_diff($expected_tables, $existing_tables);

		if (count($missing_tables) > 0) {
			$missing_str = implode(", ", $missing_tables);
			$_SESSION['requirements']['database_has_tables'] = array(
				'status' => 'err',
				'message' => "The database is missing the following tables: {$missing_str}",
			);
		} else {
			$_SESSION['requirements']['database_has_tables'] = array('status' => 'ok');
		}
	}


	/**
	 * Attempt to get the current database configuration.
	 *
	 */
	private function get_db_config()
	{
		$file_path = FCPATH . 'system/application/config/database.php';
		if ( ! is_file($file_path)) {
			show_error("The database.php config file could not be found.");
		}

		require($file_path);

		if ( ! isset($db) || ! is_array($db)) {
			show_error("The database.php config file could not be loaded or is not in the correct format.");
		}

		// Set DB config from existing file.
		$driver = (defined('PDO::ATTR_DRIVER_NAME') ? 'pdo' : 'mysqli');

		$db_config = array(
			'username' => $db['default']['username'],
			'password' => $db['default']['password'],
			'dbdriver' => $driver,
		);

		switch ($driver) {
			case 'pdo':
				$db_config['dsn'] = "mysql:host={$db['default']['hostname']};dbname={$db['default']['database']}";
			break;
			case 'mysqli':
				$db_config['hostname'] = $db['default']['hostname'];
				$db_config['database'] = $db['default']['database'];
			break;
		}

		$pass_star = str_repeat('*', strlen($db_config['password']));
		$db_config['summary'] = sprintf("%s:%s@%s/%s", $db_config['username'], $pass_star, $db['default']['hostname'], $db['default']['database']);

		return $db_config;
	}


	private function start_upgrade()
	{
		$db_config = $this->get_db_config();
		$this->load->database($db_config);
		$this->load->dbforge();

		// Each function should simply return TRUE/FALSE based on whether it succeeded.
		// If an error occurs, the function should populate var: $this->errors['section'] = 'ERROR_MESSAGE';
		//

		if ( ! $this->copy_images()) {
			log_message("error", "Upgrade: could not copy old images.");
			return FALSE;
		}

		if ( ! $this->do_migrations()) {
			log_message("error", "Upgrade: unable to do database migrations.");
			return FALSE;
		}

		if ( ! $this->install_config()) {
			log_message("error", "Upgrade: Setting up new config failed.");
			return FALSE;
		}

		write_file(FCPATH . 'local/installed', date("Y-m-d H:i:s"));

		return TRUE;
	}


	/**
	 * Find room photos & school logo files to copy.
	 *
	 */
	private function copy_images()
	{
		$this->load->helper('directory');

		$sources = array();

		$dirs = array(
			'room_photos' => FCPATH . "webroot/images/roomphotos/320",
			'school_logo' => FCPATH . "webroot/images/schoollogo/100",
		);

		foreach ($dirs as $type => $dir) {
			if ( ! is_dir($dir)) {
				log_message("info", "Upgrade: Images: {$type} dir '{$dir}' does not exist.");
				continue;
			}

			$files = directory_map($dir, 1);
			if (empty($files)) {
				log_message("info", "Upgrade: Images: {$type} dir does not have any files.");
				continue;
			}

			foreach ($files as $file_name) {
				$sources[] = realpath($dir . "/{$file_name}");
			}
		}

		if (empty($sources)) {
			// Nothing to copy, exit with success
			return TRUE;
		}

		$dest_dir = FCPATH . 'uploads';
		$errors = array();

		foreach ($sources as $source) {
			$dest = $dest_dir . "/" . basename($source);
			if ( ! copy($source, $dest)) {
				log_message("info", "Upgrade: Images: Unable to copy $source => $dest.");
				$errors[] = $source;
			} else {
				log_message("info", "Upgrade: Images: Copied $source => $dest.");
			}
		}

		return (empty($errors));
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

		// Get old DB config
		$db_config = $this->get_db_config();

		$data = array(
			'base_url' => $_SESSION['url'],
			'db_dsn' => '',
			'db_host' => '',
			'db_user' => $db_config['username'],
			'db_pass' => $db_config['password'],
			'db_name' => '',
		);

		switch ($db_config['dbdriver']) {
			case 'pdo':
				$data['db_driver'] = 'pdo';
				$data['db_dsn'] = $db_config['dsn'];
			break;
			case 'mysqli':
				$data['db_driver'] = 'mysqli';
				$data['db_host'] = $db_config['hostname'];
				$data['db_name'] = $db_config['database'];
			break;
		}

		$this->load->library('parser');
		$config_contents = $this->parser->parse_string($config_tpl, $data);

		$res = write_file(FCPATH . 'local/config.php', $config_contents);

		if ( ! $res) {
			log_message("error", "Unable to save config to file.");
			$this->errors['config'] = "Could not save config to file.";
		}

		return $res;
	}


	private function do_migrations()
	{
		$this->load->library('migration');
		if ( ! $this->migration->latest()) {
			log_message("error", "Upgrade: Migrations: Could not migrate to latest.");
			return FALSE;
		}

		return TRUE;
	}


}
