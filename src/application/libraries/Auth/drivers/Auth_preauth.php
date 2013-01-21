<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Auth_preauth extends CI_Driver {
	
	
	public $reason;
	
	
	
	
	public function auth()
	{
		// @TODO authenticate
	}
	
	
	
	
	public function generate_key()
	{
		return hash('sha1', uniqid(config_item('encryption_key')));
	}
	
	
	
	
}

/* End of file: ./application/libaries/Auth/drivers/Auth_ldap.php */