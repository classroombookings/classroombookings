<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Classroombookings. Hassle-free resource booking for schools. <http://classroombookings.com/>
 * Copyright (C) 2006-2011 Craig A Rodway <craig.rodway@gmail.com>
 *
 * This file is part of Classroombookings.
 * Classroombookings is licensed under the Affero GNU GPLv3 license.
 * Please see license-classroombookings.txt for the full license text.
 */

class AuthHook
{


    var $CI;
    
	
    function AuthHook(){
		// Load original CI object to global CI variable
		$this->CI =& get_instance();
		
		// Load cookie helper as required by this library
		$this->CI->load->helper('cookie');
		$this->CI->load->library('session');
		
		#$this->CI->config->set_item('cookie_prefix', $_SERVER['SERVER_NAME']);
		
		// Timeout in minutes of an 'active' logged in user.
		$this->timeout = 10;
		
    }
	
	
	
	
	/**
	 * Check for a cookie - if so, login with it
	 */
	function cookiecheck(){
		$this->CI->load->helper('cookie');
		$cookie['crbs_key'] = get_cookie('crbs_key');
		$cookie['user_id'] = get_cookie('crbs_user_id');
		if($cookie['crbs_key'] != FALSE && !$this->CI->session->userdata('user_id')){
			$this->CI->auth->cookielogin($cookie['crbs_key']);
		}
	}
	
	
	
	
	/**
	 * Update timestamp for user activity.
	 * Remove expired users
	 */
	function activeuser(){
		
		if($this->CI->auth->logged_in() == true){
			
			// Get the logged in user ID and current time
			$user_id = (int)$this->CI->session->userdata('user_id');
			$now = time();
			
			// Update the current user in the usersactive table
			$sql = 'REPLACE INTO usersactive VALUES(?, ?)';
			$query = $this->CI->db->query($sql, array($user_id, $now));
			
			// Update 'last activity' time in the users table
			$sql = 'UPDATE users SET lastactivity = NOW() WHERE user_id = ?';
			$query = $this->CI->db->query($sql, array($user_id));
			
			// Remove dead entries
			$expiretime = strtotime("-{$this->timeout} minutes");
			$sql = 'DELETE FROM usersactive WHERE timestamp < ?';
			$query = $this->CI->db->query($sql, array($expiretime));
			
		}
		
	}
	
	
	
	
}




/* End of file app/hooks/AuthHook.php */