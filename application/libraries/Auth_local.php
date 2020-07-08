<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_local
{


	protected $CI;

	protected $errors = [];


	public function __construct($config = [])
	{
		$this->CI =& get_instance();

		$this->CI->load->model('users_model');

		if ( ! empty($config)) {
			$this->init($config);
		}
	}


	public function init($config)
	{
		$this->errors = [];

		foreach ($config as $key => $val) {
			if (isset($this->$key)) {
				$this->$key = $val;
			}
		}

		return $this;
	}




	/**
	 * Check a given username and password against the local users table.
	 *
	 * @param string $username
	 * @param string $password
	 * @return mixed FALSE on failure or DB User row on success.
	 *
	 */
	public function authenticate($username = '', $password = '')
	{
		return $this->verify($username, $password);
	}


	/**
	 * Check to see if a given username and password are valid.
	 *
	 * @param string $username
	 * @param string $password
	 * @return mixed FALSE on failure or DB User row on success.
	 *
	 */
	public function verify($username, $password)
	{
		$username = trim($username);

		if ( ! strlen($username) || ! strlen($password)) {
			$this->errors[] = 'no_username_or_password';
			return FALSE;
		}

		$user = $this->CI->users_model->get_by_username($username, FALSE);

		if ( ! $user) {
			$this->errors[] = 'user_not_found';
			return FALSE;
		}

		if ($user->enabled == 0) {
			$this->errors[] = 'user_not_enabled';
			return FALSE;
		}

		// Flag to determine if password should be updated, if successful.
		$upgrade_password = FALSE;

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
			$this->errors[] = 'password_incorrect';
			return FALSE;
		}

		// If we need to upgrade their password to new storage without sha1, do it now
		if ($upgrade_password) {
			$this->CI->users_model->set_password($user->user_id, $password);
		}

		return $this->CI->users_model->get_by_id($user->user_id);
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
