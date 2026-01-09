<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class School extends MY_Controller
{


	public function __construct()
	{
		parent::__construct();

		$this->require_logged_in();
	}


	public function index()
	{
		redirect('settings/organisation');
	}


}
