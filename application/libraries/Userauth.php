<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Userauth
{


	// Codeigniter
	private $CI;

	// Logged in user
	public $user = NULL;


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


	function loggedin()
	{
		return ($this->user !== NULL);
	}


}
