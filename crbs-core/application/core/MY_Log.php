<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Log extends CI_Log
{


	public function __construct()
	{
		parent::__construct();
	}


	public function set_path($path)
	{
		file_exists($path) OR mkdir($path, 0755, TRUE);

		if ( ! is_dir($path) OR ! is_really_writable($path)) {
			return false;
		}

		$this->_log_path = $path;
	}


	public function write_log($level, $msg)
	{
		$parent_result = parent::write_log($level, $msg);
		if ($parent_result === false) return $parent_result;

		// Catch errors for managed error logging
		if (CRBS_MANAGED && $level === 'error' && strpos($msg, 'Airbrake') === FALSE) {
			if (class_exists(CI_Controller::class)) {
				$CI =& get_instance();
				if ( ! is_null($CI)) {
					$CI->load->library('airbrake');
					if (isset($CI->airbrake)) {
						$CI->airbrake->error($msg);
					}
				}
			}
		}

		return $parent_result;
	}


	/**
	 * Format the log line.
	 *
	 * This is for extensibility of log formatting
	 * If you want to change the log format, extend the CI_Log class and override this method
	 *
	 * @param	string	$level 	The error level
	 * @param	string	$date 	Formatted date string
	 * @param	string	$message 	The log message
	 * @return	string	Formatted log line with a new line character at the end
	 */
	protected function _format_line($level, $date, $message)
	{
		if (CRBS_MANAGED) {
			$code = isset($_SERVER['CRBS_TENANT_CODE'])
				? $_SERVER['CRBS_TENANT_CODE']
				: 'control';
			return "[{$code}] $level - $date --> $message".PHP_EOL;
		}
		return parent::_format_line($level, $date, $message);
	}


}
