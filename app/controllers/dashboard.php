<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Classroombookings. Hassle-free resource booking for schools. <http://classroombookings.com/>
 * Copyright (C) 2006-2011 Craig A Rodway <craig.rodway@gmail.com>
 *
 * This file is part of Classroombookings.
 * Classroombookings is licensed under the Affero GNU GPLv3 license.
 * Please see license-classroombookings.txt for the full license text.
 */
 
class Dashboard extends CB_Controller
{


	function __construct(){
		parent::__construct();
	}
	
	
	
	
	function index(){
		$this->auth->check('dashboard');
		$tpl['title'] = 'Dashboard';
		$tpl['pagetitle'] = $tpl['title'];
		if($this->auth->logged_in() == TRUE){
			$body['active_users'] = $this->auth->active_users();
			$tpl['body'] = $this->load->view('dashboard/index', $body, TRUE);
		} else {
			$tpl['body'] = 'You are not currently logged in. ' . anchor('account/login', 'Login now') . '.';
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