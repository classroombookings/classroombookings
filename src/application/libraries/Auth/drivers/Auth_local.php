<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Include the phpass library for secure password hashing
require_once(APPPATH . '/third_party/phpass.php');


class Auth_local extends CI_Driver {
	
	
	private $_phpass = NULL;		// reference to phpass library
	private $_phpass_iteration_count = 8;		// Hash iteration count
	private $_phpass_portable = FALSE;		// Use portable hashes? Should be true when using PHP <= 5.2
	
	public $reason;
	
	
	
	
	/**
	 * Authenticate a username and password combination for a local user
	 *
	 * @param string $username		Username to validate
	 * @param string $password		Password to validate for $username
	 * @return bool
	 */
	public function auth($username = '', $password = '')
	{
		$user = $this->CI->users_model->get_by_username($username);
		
		if ($user)
		{
			log_message('debug', "Auth->local->auth($username): User found.");
			
			if ($user['u_auth_method'] === 'local')
			{
				$response = $this->check_password($password, $user['u_password']);
				log_message('debug', "Auth->local->auth($username): Response: " . var_export($response, TRUE));
				$this->reason = 'Invalid password.';
				return $response;
			}
			else
			{
				log_message('debug', "Auth->local->auth($username): Auth method is: " . $user['u_auth_method']);
				$this->reason = 'Account not configured for local passwords.';
				return FALSE;
			}
		}
		else
		{
			log_message('debug', "Auth->local->auth($username): User not found.");
			$this->reason = 'User not found.';
			return FALSE;
		}
	}
	
	
	
	
	/**
	 * Publicly-accessible function for hashing a supplied plaintext password
	 *
	 * @param string $password		Plain text password
	 * @return string		Hashed password
	 */
	public function hash_password($password = '')
	{
		$this->_init_phpass();
		return $this->_phpass->HashPassword($password);
	}
	
	
	
	
	/**
	 * Check if a given plaintext password matches the (expected) hash
	 */
	public function check_password($password = '', $stored_hash = '')
	{
		$this->_init_phpass();
		return $this->_phpass->CheckPassword($password, $stored_hash);
	}
	
	
	
	
	/**
	 * Function to initialise phpass when required by the hashing functions
	 */
	private function _init_phpass()
	{
		if ($this->_phpass === NULL)
		{
			$this->_phpass = new PasswordHash($this->_phpass_iteration_count, $this->_phpass_portable);
		}
	}
	
	
}

/* End of file: ./application/libaries/Auth/drivers/Auth_local.php */