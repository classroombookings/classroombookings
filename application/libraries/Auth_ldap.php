<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_ldap
{

	public $enabled = FALSE;
	public $create_users = FALSE;

	public $server = '';
	public $port = 389;
	public $version = 3;
	public $use_tls = FALSE;
	public $ignore_cert = FALSE;
	public $bind_dn_format = '';
	public $base_dn = '';
	public $search_filter = '';
	public $timeout = 10;

	public $attr_firstname = '';
	public $attr_lastname = '';
	public $attr_displayname = '';
	public $attr_email = '';

	protected $connection = NULL;

	protected $CI;

	protected $errors = [];


	public function __construct($config = [])
	{
		$this->CI =& get_instance();

		$this->CI->load->model('users_model');

		$this->init([
			'timeout' => config_item('auth_ldap_timeout'),
		]);

		if ( ! empty($config)) {
			$this->init($config);
		} else {
			$this->init_from_settings();
		}
	}


	public function init($config)
	{
		$this->errors = [];

		foreach ($config as $key => $val) {
			if (isset($this->$key)) {
				switch ($key) {
					case 'enabled':
					case 'create_users':
					case 'use_tls':
					case 'ignore_cert':
						$val = boolval($val);
					break;
					case 'version':
					case 'port':
						$val = (int) $val;
					break;
				}
				$this->$key = $val;
			}
		}

		return $this;
	}


	public function init_from_settings()
	{
		$settings = $this->CI->settings_model->get_all('auth');

		$this->server = element('ldap_server', $settings);
		$this->port = intval(element('ldap_port', $settings));
		$this->version = intval(element('ldap_version', $settings));
		$this->use_tls = boolval(element('ldap_use_tls', $settings));
		$this->ignore_cert = boolval(element('ldap_ignore_cert', $settings));
		$this->bind_dn_format = element('ldap_bind_dn_format', $settings);
		$this->base_dn = element('ldap_base_dn', $settings);
		$this->search_filter = element('ldap_search_filter', $settings);

		$this->attr_firstname = element('ldap_attr_firstname', $settings);
		$this->attr_lastname = element('ldap_attr_lastname', $settings);
		$this->attr_displayname = element('ldap_attr_displayname', $settings);
		$this->attr_email = element('ldap_attr_email', $settings);

		$this->enabled = boolval(element('ldap_enabled', $settings));
		$this->create_users = boolval(element('ldap_create_users', $settings));

		return $this;
	}


	/**
	 * Check a given username and password against the LDAP server.
	 * Return a DB User row if successful.
	 *
	 * @param string $username
	 * @param string $password
	 * @return mixed FALSE on failure or DB User row on success.
	 *
	 */
	public function authenticate($username, $password)
	{
		$username = trim($username);
		if ( ! strlen($username) || ! strlen($password)) {
			$this->errors[] = 'no_username_or_password';
			return FALSE;
		}

		if ( ! $this->enabled) {
			// LDAP authentication isn't enabled.
			$this->errors[] = 'ldap_not_enabled';
			return FALSE;
		}

		// Get the user to see if they exist yet.
		// Pass FALSE to 'require_enabled' so we can deny access to those who are disabled.
		// Otherwise, it would seem like they don't exist, and would be (re-)created.
		$db_user = $this->CI->users_model->get_by_username($username, FALSE);

		if ($db_user && $db_user->enabled == 0) {
			// User exists, but are not enabled. Consider this an auth failure.
			$this->errors[] = 'user_not_enabled';
			return FALSE;
		}

		if ( ! $db_user && ! $this->create_users) {
			// User does not exist and they should not be created.
			$this->errors[] = 'user_not_found_no_create';
			return FALSE;
		}

		// Expect TRUE or an array of user attributes on success
		$verified = $this->verify($username, $password);

		if ($verified === FALSE) {
			return FALSE;
		}

		// Got user - create or update
		//

		// Array for local user data
		$user_data = [];

		// Check for LDAP attriutes. Update user properties if present.
		if (is_array($verified)) {
			$crbs_props = $this->map_user_attributes($verified);
			if (is_array($crbs_props)) {
				$user_data = array_merge($user_data, $crbs_props);
			}
		}

		if ($db_user) {

			// Get ID for later
			$user_id = $db_user->user_id;

			// Update, if there are attributes
			if ( ! empty($user_data)) {
				$this->CI->users_model->update($user_data, $db_user->user_id);
				log_message('info', "AuthLDAP: Updated profile details for {$username}.");
			}

		} else {

			// Create user
			$user_data['username'] = $username;
			$user_data['created'] = date('Y-m-d H:i:s');
			$user_data['authlevel'] = TEACHER;
			$user_data['enabled'] = 1;

			$user_id = $this->CI->users_model->insert($user_data);

			log_message('info', "AuthLDAP: Created new user account for {$username}.");

		}

		// Update the local password to one supplied here.
		// This keeps a copy locally so users can log in if LDAP can't be reached
		$this->CI->users_model->set_password($user_id, $password);

		return $this->CI->users_model->get_by_id($user_id);
	}


	/**
	 * Verify a username and password against the configured LDAP server.
	 *
	 * @param string $username Username
	 * @param string $password Password
	 * @return mixed FALSE on failure; TRUE on success but no search performed; Array of user attributes on success if search performed.
	 *
	 */
	public function verify($username, $password)
	{
		$connection = $this->get_connection();

		if ( ! $connection) {
			return FALSE;
		}

		$username = trim($username);
		if ( ! strlen($username) || ! strlen($password)) {
			$this->errors[] = 'no_username_or_password';
			return FALSE;
		}

		$bind_dn = $this->get_user_bind_dn($username);

		if ( ! $bind = @ldap_bind($this->connection, $bind_dn, $password)) {
			$error_number = ldap_errno($this->connection);
			$this->errors[] = "bind_error";
			$this->errors[] = ldap_err2str($error_number);
			return FALSE;
		}

		// Successful bind: quick exit here if no query filter supplied.
		if ( ! strlen($this->search_filter)) {
			return TRUE;
		}

		// Get user details using query filter.
		$user = $this->get_user_details($username);

		if ($user === FALSE) {
			return FALSE;
		}

		return $user;
	}


	/**
	 * Create an LDAP connection to the server.
	 *
	 */
	public function create_connection()
	{
		if ( ! strlen($this->server) || ! strlen($this->port)) {
			$this->errors[] = 'no_server_or_port';
			return FALSE;
		}

		$sock = @fsockopen($this->server, $this->port, $errno, $errstr, $this->timeout);
		if ( ! $sock) {
			$this->errors[] = "no_socket_connection";
			// $this->errors[] = $errstr;
			return FALSE;
		}

		fclose($sock);

		// Ignore cert flag
		if ($this->ignore_cert) {
			@putenv('LDAPTLS_REQCERT=never');
		}

		if ( ! $this->connection = @ldap_connect($this->get_ldap_uri())) {
			$this->errors[] = 'invalid_ldap_uri';
			return FALSE;
		}

		// Other options
		ldap_set_option($this->connection, LDAP_OPT_PROTOCOL_VERSION, $this->version);
		ldap_set_option($this->connection, LDAP_OPT_REFERRALS, 0);
		ldap_set_option($this->connection, LDAP_OPT_NETWORK_TIMEOUT, $this->timeout);

		if ($this->use_tls) {
			ldap_start_tls($this->connection);
		}

		return $this;
	}


	/**
	 * Map the array of user attributes (returned from search) to classroombookings properties.
	 *
	 * @return mixed Boolean FALSE on no attributes or when no mapping fields configured. Otherwise array.
	 *
	 */
	public function map_user_attributes($ldap_user_attrs)
	{
		if ( ! is_array($ldap_user_attrs)) {
			return FALSE;
		}

		// Get mapping config
		$mapping = [
			'firstname' => 'attr_firstname',
			'lastname' => 'attr_lastname',
			'displayname' => 'attr_displayname',
			'email' => 'attr_email',
		];

		// Check that at least one mapping field has been set.
		$all_fields = '';
		foreach ($mapping as $field_name => $template_prop) {
			$template = isset($this->$template_prop) ? trim($this->$template_prop) : '';
			$all_fields .= $template;
		}
		if ( ! strlen($all_fields)) {
			return FALSE;
		}

		// Final array of crbs prop => user values
		$out = [
			'firstname' => FALSE,
			'lastname' => FALSE,
			'displayname' => FALSE,
			'email' => FALSE,
		];

		// Create array of LDAP attributes with :keys and their values
		$vars = [];
		foreach ($ldap_user_attrs as $k => $v) {
			$vars[":{$k}"] = $v;
		}

		foreach ($mapping as $field_name => $template_prop) {

			$template = isset($this->$template_prop) ? trim($this->$template_prop) : '';

			// No template: remove from return data
			if ( ! strlen($template)) {
				unset($out[$field_name]);
				continue;
			}

			// Prepend colon for templating if the value is only a fieldname
			if (ctype_alpha($template)) {
				$template = ":{$template}";
			}

			$value = strtr($template, $vars);
			if (strlen($value)) {
				$out[$field_name] = $value;
			}
		}

		return $out;
	}


	/**
	 * Get the raw list of LDAP attributes used in the mapping configuration.
	 * This is needed to ensure we request these in the search.
	 *
	 * @return array Array of unique attributes to request from LDAP.
	 *
	 */
	public function get_mapping_attributes()
	{
		$attrs = [];

		$props = [
			'attr_firstname',
			'attr_lastname',
			'attr_displayname',
			'attr_email',
		];

		foreach ($props as $template_prop) {

			$template = isset($this->$template_prop) ? trim($this->$template_prop) : '';

			if ( ! strlen($template)) {
				continue;
			}

			if (ctype_alpha($template)) {
				$attrs[] = $template;
				continue;
			}

			// Find & extract all the :keys
			preg_match_all('/:[a-zA-Z0-9+]+/', $template, $matches);
			if (count($matches[0]) > 0) {
				foreach ($matches[0] as $match) {
					$attrs[] = ltrim($match, ':');
				}
			}

		}

		return array_unique($attrs);
	}


	/**
	 * Get the requested user details.
	 *
	 * @return mixed FALSE on failure or array on success.
	 *
	 */
	protected function get_user_details($username)
	{
		$connection = $this->get_connection();

		$user_data = [];

		// Fields to get
		$default_fields = [
			'dn',
		];

		$mapping_fields = $this->get_mapping_attributes();

		$fields = array_merge($default_fields, $mapping_fields);

		foreach ($fields as $field) {
			$user_data[$field] = '';
		}

		$filter = $this->get_user_search_filter($username);

		if ( ! $results = @ldap_search($connection, $this->base_dn, $filter, $fields)) {
			$this->errors[] = 'search_error';
			return FALSE;
		}

		if (ldap_count_entries($connection, $results) != 1) {
			$this->errors[] = 'search_num_results_error';
			return FALSE;
		}

		if ( ! $entry = ldap_first_entry($connection, $results)) {
			$this->errors[] = 'search_get_entry_error';
			return FALSE;
		}

		if ( ! $data = ldap_get_attributes($connection, $entry)) {
			$this->errors[] = 'search_get_attributes_error';
			return FALSE;
		}

		foreach ($fields as $field) {
			if (isset($data[$field])) {
				$user_data[$field] = $data[$field][0];
			}
		}

		$user_data['dn'] = ldap_get_dn($connection, $entry);

		return $user_data;
	}



	/**
	 * Get connection instance. Create if doesn't exist.
	 *
	 */
	public function get_connection()
	{
		if ($this->connection === NULL) {
			$this->create_connection();
		}

		return $this->connection;
	}


	/**
	 * Build the LDAP connection URI from the SSL option, server and port.
	 *
	 * @return string
	 *
	 */
	public function get_ldap_uri()
	{
		$scheme = $this->use_tls ? 'ldaps' : 'ldap';
		$uri = sprintf('%s://%s:%d', $scheme, $this->server, $this->port);
		return $uri;
	}


	public function get_user_bind_dn($username)
	{
		$vars = [
			':user' => $username,
		];

		return strtr($this->bind_dn_format, $vars);
	}


	public function get_user_search_filter($username)
	{
		$vars = [
			':user' => $username,
		];

		return strtr($this->search_filter, $vars);
	}


	/**
	 * Get any errors that have been set.
	 *
	 * @return array
	 *
	 */
	public function get_errors()
	{
		return $this->errors;
	}


}
