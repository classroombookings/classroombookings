<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AuthLDAP
{


	public $server = '';
	public $port = 389;
	public $version = 3;
	public $use_tls = FALSE;
	public $ignore_cert = FALSE;
	public $base_dn = '';
	public $user_attr = '';
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

		$this->init([
			'timeout' => config_item('auth_ldap_timeout'),
		]);

		if ( ! empty($config)) {
			$this->init($config);
		}
	}


	public function init($config)
	{
		$this->errors = [];

		foreach ($config as $key => $val) {
			if (isset($this->$key)) {
				switch ($key) {
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
	 * Check a given username and password against the LDAP server.
	 *
	 * @param string $username
	 * @param string $password
	 * @return mixed FALSE on failure or array of user details
	 *
	 */
	public function authenticate($username = '', $password = '')
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

		// Generate bind DN: attr=username, [base_dn]
		$bind_dn = sprintf('%s=%s,%s', $this->user_attr, $username, $this->base_dn);

		if ( ! $bind = @ldap_bind($this->connection, $bind_dn, $password)) {
			$this->errors[] = 'bind_error';
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
			$this->user_attr,
		];

		$mapping_fields = $this->get_mapping_attributes();

		$fields = array_merge($default_fields, $mapping_fields);

		foreach ($fields as $field) {
			$user_data[$field] = '';
		}

		$vars = [
			':attr' => $this->user_attr,
			':user' => $username,
		];

		$filter = strtr($this->search_filter, $vars);

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
