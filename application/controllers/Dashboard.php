<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller
{


	public function __construct()
	{
		parent::__construct();
		$this->require_logged_in(FALSE);
	}


	/**
	* Dashboard
	*
	*/
	public function index()
	{
		$this->data['showtitle'] = 'Tasks';
		$this->data['title'] = setting('name');

		$this->data['body'] = '';

		$this->data['body'] .= $this->session->flashdata('auth');
		$this->data['body'] .= $this->load->view('dashboard/index', NULL, TRUE);

		return $this->render();
	}



}
