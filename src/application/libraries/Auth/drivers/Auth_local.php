<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auth_local extends CI_Driver {

	
	public function auth($username = '', $password = '')
	{
		// Do local user lookup
		return TRUE;
	}
	
	
}

/* End of file: ./application/libaries/Auth/drivers/Auth_local.php */