<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller
{


	public function __construct()
	{
		parent::__construct();
		redirect('settings/general');
	}


	public function index()
	{
	}



}
