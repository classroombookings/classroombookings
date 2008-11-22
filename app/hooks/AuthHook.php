<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
	This file is part of Classroombookings.

	Classroombookings is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	Classroombookings is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with Classroombookings.  If not, see <http://www.gnu.org/licenses/>.
*/


class AuthHook{


    var $CI;
    
	
    function AuthHook(){
		// Load original CI object to global CI variable
		$this->CI =& get_instance();
		
		// Load cookie helper as required by this library
		$this->CI->load->helper('cookie');
    }
	
	
	
	
	/**
	 * Check for a cookie - if so, login with it
	 */
	function cookiecheck(){
		$this->CI->load->helper('cookie');
		$cookie['crbs_key'] = get_cookie('crbs_key');
		$cookie['user_id'] = get_cookie('user_id');
		if($cookie['crbs_key'] != FALSE && !$this->CI->session->userdata('user_id')){
			$this->CI->auth->cookielogin($cookie['crbs_key']);
		}
	}
	
	
	
	
	/*function check($action_name = NULL){
	
		#$sessdata['permissions'] = array(1,5,6,7,9);
		
		// Get username. We need this to find out if user is logged in or not.
		$username = $this->CI->session->userdata('username');
		
		// Get our user's group_id fro msession. If empty, they're anonymous (Group ID 0)
		$group_id = $this->CI->session->userdata('group_id');
		$sessdata['group_id'] = ($group_id === FALSE) ? 0 : $group_id;
		
		// Ok, now we need to get the permissions allowed for this group (eg. what the user IS allowed to do)
		$permissions = $this->CI->session->userdata('permissions');
		if(is_array($permissions) && !empty($permissions)){
			// Permissions in session is OK!
		} else {
			$sessdata['permissions'] = $this->CI->auth->get_group_permission_ids($group_id);
		}
		
		// Now put the required stuff in the session
		if(isset($sessdata) && is_array($sessdata)){
			$this->CI->session->set_userdata($sessdata);
		}
		
		// Check for current action. If not, we need to find it!
		/* if($action_name == NULL){
			
			// Get it from the URI
			$request = implode('/', $this->CI->uri->rsegments);
			$request = str_replace('/index', '', $request);
			
			$segs = array();
			$ls = "";
			foreach($this->CI->uri->rsegments as $segment){
				$thisone = "$ls$segment";
				$segs[] = $thisone;
				$ls = "$thisone/";
			}
	
			
			#die(var_dump($this->CI));
			$permission = $this->CI->auth->get_permission_by_url($segs);
			die(var_export($permission));
			
		} */
		
		// OK, now go to the Auth library and check if the group has permissions on the action
		#$return = $this->CI->auth->check($action_name, $group);
		
		// Get what permissions this group has
		#$arrperms = $this->CI->auth->get_group_permissions($group_id);
	#}*/
	
	
	
	
	/*function checklevel(){

		$user = $this->CI->session->userdata('authlevel');
		$request = $this->CI->uri->uri_string();
		$request = preg_replace('/^\/|\/$/e', '', $request);
		die($request);
		
		if(!$this->CI->auth->checklevel($request, $user, TRUE)){
			$msg = $this->CI->load->view('msg/err', 'You are required to login to access this area.', TRUE);
			$this->CI->session->set_flashdata('msg', $msg);
			$this->CI->session->set_userdata('uri', $this->CI->uri->uri_string());
			redirect('account/login');
		}
	}
	
	*/
    
	
	
	
}
?>