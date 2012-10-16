<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Classroombookings. Hassle-free resource booking for schools. <http://classroombookings.com/>
 * Copyright (C) Craig A Rodway <craig.rodway@gmail.com>
 *
 * Licensed under the Open Software License version 3.0
 * 
 * This source file is subject to the Open Software License (OSL 3.0) that is
 * bundled with this package in the files license.txt. It is also available 
 * through the world wide web at this URL:
 * http://opensource.org/licenses/OSL-3.0
 */
 
class Home extends MY_Controller
{


	function __construct()
	{
		$this->data['nav_current'] = array('home');
		
		parent::__construct();
	}
	
	
	
	
	function index(){
		//$this->auth->check('crbs.dashboard.view');
		$tpl['title'] = 'Dashboard';
		$tpl['pagetitle'] = $tpl['title'];
		if($this->auth->logged_in() == TRUE){
			$body['active_users'] = $this->auth->active_users();
			$tpl['body'] = $this->load->view('dashboard/index', $body, TRUE);
		} else {
			//$tpl['body'] = 'You are not currently logged in. ' . anchor('account/login', 'Login now') . '.';
			redirect('account/login');
		}
		$this->page($tpl);
	}
	
	
	
	
	function error(){
		$tpl['title'] = 'An error occured';
		$tpl['body'] = '';
		$this->load->view($this->tpl, $tpl);
	}
	
	
	
	
}


?>