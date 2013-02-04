<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Auth_preauth extends CI_Driver {
	
	
	public $reason;
	
	
	
	
	public function auth($data = array())
	{
		if ( ! option('auth_preauth_enable'))
		{
			$this->reason = 'auth_preauth_disabled';
			return FALSE;
		}
		
		// Check for username
		if ( ! isset($data['username']))
		{
			$this->reason = 'auth_preauth_no_username';
			return FALSE;
		}
		
		if ( ! isset($data['timestamp']))
		{
			$this->reason = 'auth_preauth_no_timestamp';
			return FALSE;
		}
		
		if ( ! isset($data['preauth']))
		{
			$this->reason = 'auth_preauth_no_preauth';
			return FALSE;
		}
		
		// Work out current time and the tolerances/threshold
		$timestamp = now();
		$time_lower = strtotime("-5 minutes");
		$time_upper = strtotime("+5 minutes");
		
		// Check if the supplied timestamp is within the allowed threshold
		if ( ($data['timestamp'] < $time_lower) OR ($data['timestamp'] > $time_upper) )
		{
			$this->reason = 'auth_preauth_clock_skew';
			return FALSE;
		}
		
		// Get the current key from the database
		$preauth_key = option('auth_preauth_key');
		
		$expected = hash_hmac('sha1', "{$data['username']}|{$data['timestamp']}|{$data['create']}", $preauth_key);
		
		if ($expected === $data['preauth'])
		{
			return TRUE;
		}
		else
		{
			$this->reason = 'auth_preauth_failed';
			return FALSE;
		}
	}
	
	
	
	
	public function generate_key()
	{
		return hash('sha1', uniqid(config_item('encryption_key')));
	}
	
	
	
	
}

/* End of file: ./application/libaries/Auth/drivers/Auth_ldap.php */