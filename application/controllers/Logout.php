<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Logout extends MY_Controller
{


	public function __construct()
	{
		parent::__construct();
	}


	function index()
	{
		$this->userauth->logout();
		redirect('/');
	}


}
