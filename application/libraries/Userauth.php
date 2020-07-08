<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Userauth
{


	// Codeigniter
	private $CI;

	// Logged in user
	public $user = NULL;

	// Num active bookings
	public $num_bookings = FALSE;


	public function __construct()
	{
		$this->CI =& get_instance();

		if ($this->CI->config->item('is_installed')) {
			$this->CI->load->database();
			$this->CI->load->model('users_model');
			$this->init_user();
		}
	}


	public function init_user()
	{
		if (isset($_SESSION['user_id']) && strlen($_SESSION['user_id'])) {

			$user = $this->CI->users_model->get_by_id($_SESSION['user_id']);

			if ($user) {
				unset($this->user->password);
				$this->user = $user;
			} else {
				$this->log_out();
			}
		}
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

		log_message('error', "Userauth: Unsuccessful login for {$username}. Reasons: " . json_encode($errors));

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
		$_SESSION['loggedin'] = TRUE;
		$_SESSION['user_id'] = $user_id;
		$_SESSION['auth_method'] = $auth_method;

		return TRUE;
	}


	/**
	 * Check to see if the user is the given authorisation level.
	 *
	 * @param string $level Authorisation level to check (admin or teacher)
	 * @return bool
	 *
	 */
	public function is_level($level)
	{
		if ( ! $this->logged_in() || ! strlen($level)) {
			return FALSE;
		}

		return ($this->user->authlevel == $level);
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


	/**
	 * Check if the logged in user can create a booking.
	 *
	 */
	public function can_create_booking($on_date = '')
	{
		$status = new StdClass();

		$status->is_admin = ($this->is_level(ADMINISTRATOR));
		$status->in_quota = TRUE;
		$status->is_future_date = TRUE;
		$status->date_in_range = TRUE;
		$status->result = FALSE;

		// Quick exit for admins
		if ($status->is_admin) {
			$status->result = TRUE;
			return $status;
		}

		// Check max allowed bookings
		// 0: no limit
		//
		$max_active_bookings = (int) abs(setting('num_max_bookings'));
		if ($max_active_bookings > 0) {
			// Get number of user's active bookings
			if ($this->num_bookings === FALSE) {
				$this->num_bookings = $this->CI->bookings_model->CountScheduledByUser($this->user->user_id);
			}

			// In quota = user has fewer bookings than maximum permitted.
			$status->in_quota = ($this->num_bookings < $max_active_bookings);
		}

		if ( ! empty($on_date)) {

			// Check date boundaries

			$today = strtotime(date("Y-m-d"));
			$booking_date = strtotime($on_date);

			$status->is_future_date = ($booking_date >= $today);

			$advance = (int) abs(setting('bia'));
			if ($advance > 0) {
				$max_date = strtotime("+{$advance} days", $today);
				$status->date_in_range = ($booking_date <= $max_date);
			}
		}

		if ($status->in_quota && $status->is_future_date && $status->date_in_range) {
			$status->result = TRUE;
		}

		return $status;
	}


}
