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

			$this->CI->db->where(array(
				'user_id' => $_SESSION['user_id'],
				'enabled' => 1,
			));
			$query = $this->CI->db->get('users', 1);

			if ($query->num_rows() == 1) {
				$this->user = $query->row();
			} else {
				unset($_SESSION['user_id']);
				$this->user = NULL;
			}

		}
	}


	/**
	 * Logout user and reset session data
	 *
	 */
	function logout()
	{
		unset($_SESSION['user_id']);
		$this->user = NULL;
		return TRUE;
	}


	/**
	 * Try and validate a login and optionally set session data
	 *
	 * @param		string		$username					Username to login
	 * @param		string		$password					Password to match user
	 * @param		bool			$session (true)		Set session data here. False to set your own
	 */
	function trylogin($username, $password)
	{
		if (empty($username) || empty($password)) {
			return FALSE;
		}

		// Get user
		$this->CI->db->where(array(
			'username' => $username,
			'enabled' => 1,
		));
		$query = $this->CI->db->get('users', 1);
		if ($query->num_rows() != 1) {
			return FALSE;
		}

		// Flag to determine if password should be updated, if successful.
		$upgrade_password = FALSE;

		$user = $query->row();
		$password_hash = $user->password;

		// Check for old password format
		if (substr($password_hash, 0, 5) === 'sha1:') {
			// user password value is password_hash() of old sha1 value
			$password_hash = substr($password_hash, 5);
			$upgrade_password = TRUE;
			$verified = password_verify(sha1($password), $password_hash);
		} else {
			// user password value is direct output of password_hash() of their password.
			$verified = password_verify($password, $password_hash);
		}

		if ( ! $verified) {
			return FALSE;
		}

		// Update user
		$user_data = array(
			'lastlogin' => date('Y-m-d H:i:s'),
		);

		// If we need to upgrade their password to new storage without sha1, do it now
		if ($upgrade_password) {
			$user_data['password'] = password_hash($password, PASSWORD_DEFAULT);
		}

		$this->CI->db->where('user_id', $user->user_id);
		$this->CI->db->update('users', $user_data);

		// Set up session
		$_SESSION['user_id'] = $user->user_id;
		$_SESSION['loggedin'] = TRUE;

		return TRUE;
	}


	public function is_level($level)
	{
		if (empty($this->user) || empty($level)) {
			return FALSE;
		}

		return ($this->user->authlevel == $level);
	}


	public function loggedin()
	{
		return ($this->user !== NULL);
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
