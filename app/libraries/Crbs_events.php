<?php defined('BASEPATH') or exit('No direct script access allowed');

class CRBS_Events
{
	
	
	public function __construct()
	{
		$this->CI =& get_instance();
		Events::register('users.import.end', array($this, 'users_import_end'));
	}
	
	
	
	
	/**
	 * User import completion or cancellation
	 *
	 * Remove unnecessary data from session
	 */
	public function users_import_end()
	{
		$csv = $this->CI->session->userdata('csvimport');
		$file = $csv['full_path'];
		@unlink($file);
		$this->CI->session->unset_userdata('csvimport');
		$this->CI->session->unset_userdata('importdef');
		$this->CI->session->unset_userdata('users');
		return true;
	}
	
	
	
	
}