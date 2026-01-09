<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Userauth
{


	// Codeigniter
	private $CI;

	// Logged in user
	public $user = NULL;

	// Logged-in user's permissions
	public $permissions = NULL;

	// Num active bookings
	public $num_bookings = FALSE;


	public function __construct()
	{
		$this->CI =& get_instance();

		if ($this->CI->config->item('is_installed')) {
			$this->CI->load->database();
			$this->CI->load->model('users_model');

			$this->CI->load->library('permission');
			require_once(APPPATH.'permissions/SystemPermissions.php');
			require_once(APPPATH.'permissions/BookingPermissions.php');
			$this->CI->permission->define_rules_from_class_methods(\app\permissions\SystemPermissions::class);
			$this->CI->permission->define_rules_from_class_methods(\app\permissions\BookingPermissions::class);
			$this->init_user();
		}
	}


	public function init_user()
	{
		if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {

			$user = $this->CI->users_model->get_by_id($_SESSION['user_id']);

			if ($user) {
				unset($this->user->password);
				$this->user = $user;
				$this->permissions = [];
				// $this->permissions = array_flip($this->CI->users_model->get_permissions($user->user_id));
				$_SESSION['force_password_reset'] = $this->user->force_password_reset ?? 0;
				$this->CI->permission->set_current_user($this->user);
			} else {
				$this->CI->permission->set_current_user(null);
				$this->log_out();
			}
		}
	}


	public function get_user(): ?object
	{
		return $this->user;
	}


	/**
	 * Logout user and reset session data
	 *
	 */
	public function log_out()
	{
		unset($_SESSION['user_id']);
		session_destroy();
		$this->user = NULL;
		return TRUE;
	}


	/**
	 * Given a username and password, authenticate the credentials and log the user in.
	 *
	 * @param string $username Username
	 * @param string $password Password
	 * @param bool $force Specify 'true' to force the session to be created for the user without a valid password.
	 *
	 */
	public function log_in($username, $password, $force = false)
	{
		// Valid user row
		$valid_user = FALSE;

		// Flag for which auth method was used to log them in.
		$auth_method = 'local';

		// Check settings
		$use_ldap = (setting('ldap_enabled', 'auth') == '1');

		// authenticate() on the local/LDAP libraries should return a valid DB User row on success.
		//

		if ($force === TRUE) {

			$valid_user = $this->CI->users_model->get_by_username($username);
			$auth_method = 'local';

		} elseif ($use_ldap) {

			log_message('info', "Trying LDAP authentication");

			$this->CI->load->library('auth_ldap');

			$valid_user = $this->CI->auth_ldap->authenticate($username, $password);
			$ldap_errors = $this->CI->auth_ldap->get_errors();
			$connection_error = in_array('no_socket_connection', $ldap_errors);
			$auth_method = 'ldap';

			if ( ! $valid_user && $connection_error) {
				// Try local instead
				$this->CI->load->library('auth_local');
				$valid_user = $this->CI->auth_local->authenticate($username, $password);
				$local_errors = $this->CI->auth_ldap->get_errors();
				$auth_method = 'local';
			}

		}

		if ( ! $valid_user && $force !== TRUE) {
			$this->CI->load->library('auth_local');
			$valid_user = $this->CI->auth_local->authenticate($username, $password);
			$local_errors = $this->CI->auth_local->get_errors();
			$auth_method = 'local';
		}

		if ($valid_user) {
			$this->setup_session($valid_user->user_id, $auth_method);
			$this->touch_last_login($valid_user->user_id);
			return TRUE;
		}

		$errors = [];
		if (isset($ldap_errors)) $errors = array_merge($errors, $ldap_errors);
		if (isset($local_errors)) $errors = array_merge($errors, $local_errors);
		$errors = array_unique($errors);

		log_message('info', "Userauth: Unsuccessful login for {$username}. Reasons: " . json_encode($errors));

		return FALSE;
	}


	/**
	 * Update the last login timestamp for the given user.
	 *
	 */
	private function touch_last_login($user_id)
	{
		$user_data = [
			'lastlogin' => date('Y-m-d H:i:s'),
		];

		$where = [
			'user_id' => $user_id,
		];

		return $this->CI->db->update('users', $user_data, $where);
	}


	/**
	 * Set up the session for the given user ID.
	 *
	 */
	private function setup_session($user_id, $auth_method = 'local')
	{
		// Set up session
		$_SESSION['loggedin'] = true;
		$_SESSION['user_id'] = $user_id;
		$_SESSION['auth_method'] = $auth_method;

		// Get & set language
		$lang = user_setting('language', $user_id);
		if ( ! empty($lang)) {
			$_SESSION['crbs_lang'] = $lang;
			set_cookie('crbs_lang', $lang, TIME_WEEK);
		}

		return true;
	}


	/**
	 * Check to see if a user is logged in.
	 * Determined by presence of local user object and an ID.
	 *
	 */
	public function logged_in()
	{
		return (is_object($this->user) && isset($this->user->user_id));
	}


}
