<?php

class MY_Form_validation extends CI_Form_validation
{

	public $CI;

	public function __construct($rules = array())
	{
		parent::__construct($rules);

		$this->CI =& get_instance();
	}


	public function valid_date($value)
	{
		$dt = datetime_from_string($value);

		if ( ! $dt) {
			$this->set_message('valid_date', '{field} must be a valid date.');
			return FALSE;
		}

		return TRUE;
	}


}
