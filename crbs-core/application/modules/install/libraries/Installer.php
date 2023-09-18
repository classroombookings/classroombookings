<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Installer
{


	// Codeigniter
	private $CI;

	private $db_config = [];
	private $db_connection;
	private $initial_user = [];
	private $name;
	private $errors = [];


	public function __construct()
	{
		$this->CI =& get_instance();

		$this->set_db_config([
			'dbdriver' => 'mysqli',
			'db_debug' => ENVIRONMENT === 'development',
			'char_set' => 'utf8mb4',
			'dbcollat' => 'utf8mb4_unicode_ci',
		]);
	}


	public function get_db_drivers()
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


	public function get_settings()
	{
		$timezone = date_default_timezone_get() ?? ini_get('date.timezone') ?? 'Europe/London';
		if ($timezone === 'UTC') $timezone = 'Europe/London';

		return [
			'crbs' => [
				'bia' => '0',
				'bookings_show_user_recurring' => '1',
				'bookings_show_user_single' => '1',
				'colour' => '468ED8',
				'date_format_long' => 'l jS F Y',
				'date_format_weekday' => 'jS M',
				'displaytype' => 'day',
				'd_columns' => 'periods',
				'login_message_enabled' => '0',
				'login_message_text' => '',
				'logo' => '',
				'maintenance_mode' => '0',
				'maintenance_mode_message' => '',
				'name' => $this->name,
				'num_max_bookings' => '0',
				'session_auto_set_current_ts' => '',
				'timezone' => $timezone,
				'time_format_period' => 'g:i',
				'website' => '',
			],
			'auth' => [
				'ldap_attr_displayname' => 'cn',
				'ldap_attr_email' => 'mail',
				'ldap_attr_firstname' => '',
				'ldap_attr_lastname' => '',
				'ldap_base_dn' => 'dc=example,dc=com',
				'ldap_bind_dn_format' => 'uid=:user,dc=example,dc=com',
				'ldap_create_users' => '1',
				'ldap_enabled' => '0',
				'ldap_ignore_cert' => '1',
				'ldap_port' => '389',
				'ldap_search_filter' => '(&(uid=:user)(objectClass=person))',
				'ldap_server' => '',
				'ldap_user_attr' => 'uid',
				'ldap_use_tls' => '0',
				'ldap_version' => '3',
			],
		];
	}


	public function set_db_config($data)
	{
		$this->db_config = array_merge($this->db_config, $data);
		return $this;
	}


	public function set_db_connection()
	{
		$user = $this->db_config['username'] ?? null;
		$pass = $this->db_config['password'] ?? null;
		$db = $this->db_config['database'] ?? null;

		if (empty($user) || empty($pass) || empty($db)) {
			$this->errors[] = 'No database connection details provided.';
			return false;
		}

		try {
			$db_conn = @$this->CI->load->database($this->db_config, true);
			$res = @$db_conn->initialize();
		} catch (Exception $e) {
			$this->errors[] = $e->getMessage();
			return false;
		}

		if ( ! $res) {
			$this->errors[] = 'Could not connect to the database server.';
			return false;
		}

		// Need to load default CI database because Migration library uses it.
		//
		if (CRBS_MANAGED) {
			$_SERVER['DB_NAME'] = $db;
			$_SERVER['DB_USER'] = $user;
			$_SERVER['DB_PASS'] = $pass;
			$this->CI->load->database('default');
		} else {
			$this->CI->load->database($this->db_config);
		}
		$this->CI->db->initialize();

		$this->db_connection = $db_conn;
		return $res;
	}


	public function is_installed()
	{
		if ( ! $this->db_connection) {
			$this->errors[] = 'No db connection.';
			return null;
		}

		$is_installed = $this->db_connection->table_exists('users');
		if ($is_installed) {
			$this->errors[] = 'Database table already exists.';
		}

		return $is_installed;
	}


	public function set_name($name)
	{
		$this->name = $name;
		return $this;
	}


	public function set_initial_user($user)
	{
		$this->initial_user = $user;
		return $this;
	}


	/**
	 * Run some checks and perform installation.
	 *
	 */
	public function execute()
	{
		$out = ['success' => false];

		$db_ok = $this->set_db_connection();
		if ( ! $db_ok) return $out;


		$is_installed = $this->is_installed();
		if ($is_installed === true) return $out;

		$has_name = (!empty($this->name));
		if ( ! $has_name) {
			$this->errors[] = 'No name provided.';
			return $out;
		}

		$username = $this->initial_user['username'] ?? null;
		$password = $this->initial_user['password'] ?? null;
		$has_user = !empty($username) && !empty($password);
		if ( ! $has_user) {
			$this->errors[] = 'No user provided.';
			return $out;
		}

		$structure_ok = $this->create_structure();
		if ( ! $structure_ok) return $out;

		$migration_ok = $this->install_migration();
		if ( ! $migration_ok) return $out;

		$migrate_ok = $this->run_migrations();
		if ( ! $migrate_ok) return $out;

		$user_ok = $this->create_user();
		if ( ! $user_ok) return $out;

		$settings_ok = $this->install_settings();
		if ( ! $settings_ok) return $out;


		$out['success'] = true;

		return $out;
	}


	private function create_structure()
	{
		$filepath = APPPATH . 'modules/install/resources/structure.sql';
		if ( ! is_file($filepath)) {
			$this->errors[] = 'structure.sql file not found.';
			return false;
		}

		$sql_file = file_get_contents($filepath);
		$queries = $this->parse_sql($sql_file);

		$success = 0;
		$total = count($queries);

		foreach ($queries as $sql) {
			try {
				$res = $this->db_connection->query($sql);
			} catch (Exception $e) {
				$this->errors[] = $e->getMessage();
			}

			if ($res) {
				$success++;
			}

		}

		return ($success == $total);
	}


	private function install_migration()
	{
		$filepath = APPPATH . 'modules/install/resources/migration';
		if ( ! is_file($filepath)) {
			$this->errors[] = 'Migration file not found.';
			return false;
		}

		$version = file_get_contents($filepath);

		return $this->db_connection->insert('migrations', ['version' => $version]);
	}


	private function run_migrations()
	{
		$this->CI->load->library('migration');
		return $this->CI->migration->latest();
	}


	private function create_user()
	{
		$user_data = [
			'username' => $this->initial_user['username'],
			'password' => password_hash($this->initial_user['password'], PASSWORD_DEFAULT),
			'authlevel' => ADMINISTRATOR,
			'enabled' => '1',
			'department_id' => null,
		];

		$res = $this->db_connection->insert('users', $user_data);
		$id = $this->db_connection->insert_id();

		if ( ! $res && ! is_numeric($id)) {
			$this->errors[] = 'Could not create user.';
			return false;
		}

		return $id;
	}


	private function install_settings()
	{
		$settings = $this->get_settings();

		$data = [];

		foreach ($settings as $group => $items) {
			foreach ($items as $k => $v) {
				$data[] = [
					'name' => $k,
					'value' => $v,
					'group' => $group,
				];
			}
		}

		$res = $this->db_connection->insert_batch('settings', $data);
		if ( ! $res) {
			$this->errors[] = $this->db_connection->error();
		}

		return $res;
	}


	public function get_errors()
	{
		return $this->errors;
	}


	// https://stackoverflow.com/a/69695857
	protected function parse_sql($content)
	{
		$sql_list = [];
		$query = "";
		$lines = explode("\n", $content);

		foreach ($lines as $sql_line) {

			$sql_line = trim($sql_line);

			if (($sql_line === "")
				|| (strpos($sql_line, "--") === 0)
				|| (strpos($sql_line, "#") === 0)
			) {
				continue;
			}

			$query .= $sql_line;
			// Checking whether the line is a valid statement
			if (preg_match("/(.*);$/", $sql_line)) {
				$query = trim($query);
				$query = substr($query, 0, strlen($query) - 1);
				$sql_list[] = $query;
				//reset the variable
				$query = "";
			}
		}

		return $sql_list;
	}


}
