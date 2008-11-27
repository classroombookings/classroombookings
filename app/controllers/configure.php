<?php
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


class Configure extends Controller {
	
	
	var $tpl;
	
	
	function Configure(){
		parent::Controller();
		$this->load->model('security');
		$this->tpl = $this->config->item('template');
	}
	
	
	
	
	function index($tab = 'conf-main'){
		/*if($this->session->flashdata('tab')){
			$body['tab'] = $this->session->flashdata('tab');
		} else {
			$body['tab'] = $tab;
		}*/
		
		$body['conf']['main'] = $this->settings->get_all('main');
		$body['conf']['auth'] = $this->settings->get_all('auth');
		$body['conf']['groups'] = $this->security->get_groups_dropdown();
		
		$tpl['title'] = 'Configure';
		$tpl['pagetitle'] = 'Configure classroombookings';
		$tpl['body'] = $this->load->view('configure/conf.index.php', $body, TRUE);
		$this->load->view($this->tpl, $tpl);
	}
	
	
	
	
	/* function save(){
		print_r($_POST);
		$tpl['title'] = 'Form error';
		$section = $this->input->post('form_id');
		
		if(!$section){
			$tpl['body'] = $this->msg->err('No form data was submitted');
		} else {
			switch($section){
				case 'conf-main': return $this->save_main(); break;
				case 'conf-ldap': return $this->save_ldap(); break;
				default:
					$tpl['body'] = $this->msg->err('No valid form was submitted');
				break;
			}
		}
		$this->load->view($this->tpl, $tpl);
	} */
	
	
	
	
	function save_main(){
		#print_r($_POST);
		$this->form_validation->set_rules('schoolname', 'School name', 'required|max_length[100]|trim');
		$this->form_validation->set_rules('schoolurl', 'Website address', 'max_length[255]|prep_url|trim');
		$this->form_validation->set_rules('bd_mode', 'Booking display mode', 'required');
		$this->form_validation->set_rules('bd_col', 'Booking display columns', 'required');
		$this->form_validation->set_error_delimiters('<li>', '</li>');

		if($this->form_validation->run() == FALSE){
			
			// Validation failed
			$this->index('conf-main');
			
		} else {
			
			#$this->load->view('formsuccess');
			$data['schoolname']		= $this->input->post('schoolname');
			$data['schoolurl']		= $this->input->post('schoolurl');
			$data['bd_mode'] 		= $this->input->post('bd_mode');
			$data['bd_col']			= $this->input->post('bd_col');
			
			$this->settings->save('main', $data);
			
			$this->session->set_flashdata('flash', $this->msg->info($this->lang->line('CONF_MAIN_SAVEOK')));
			$this->session->set_flashdata('tab', 'conf-main');
			redirect('configure');
			
		}
		
	}
	
	
	
	
	function save_ldap(){
		die(print_r($_POST));

		
		$this->form_validation->set_rules('preauth', 'Pre-authentication');
		$this->form_validation->set_rules('ldap', 'LDAP enable');
		
		if($this->form_validation->run() == FALSE){
		
			// Validation failed
			$this->index('conf-auth');
			
		} else {
		
			$this->session->set_flashdata('flash', $this->msg->info($this->lang->line('CONF_AUTH_SAVEOK')));
			$this->session->set_flashdata('tab', 'conf-auth');
			redirect('configure');
			
		}
		
	}
	
	
	
	
	function test_ldap(){
		$this->_d('Testing for PHP LDAP module... ', FALSE);
		if(!function_exists('ldap_bind')){
			$this->_d("It appears you don't have the PHP LDAP module installed - cannot continue!");
			exit();
		} else {
			$this->_d('OK');
		}
		
		// Get form values
		$ldaphost = $this->input->post('ldaphost');
		$ldapport = $this->input->post('ldapport');
		$ldapbase = $this->input->post('ldapbase');
		$ldapfilter = str_replace("%u", $this->input->post('ldaptestuser'), $this->input->post('ldapfilter'));
		$ldaptestuser = "cn=" . $this->input->post('ldaptestuser');
		$ldaptestpass = $this->input->post('ldaptestpass');
		
		if(!$ldaphost){
			$this->_d('No LDAP server was specified - cannot continue.');
			exit();
		}
		if(!$ldapport){
			$this->_d('No LDAP server port was specified - cannot continue.');
			exit();
		}
		if(!$ldapbase){
			$this->_d("No LDAP search base was specified - cannot continue.");
			exit();
		}
		if(!$ldapfilter){
			$this->_d("No LDAP search query filter was specified - cannot continue.");
			exit();
		}
		
		// Try to connect to server
		$this->_d(sprintf("Trying to connect to '%s' on port %d... ", $ldaphost, $ldapport), FALSE);
		$connect = ldap_connect($ldaphost, $ldapport);
		if(!$connect){
			$this->_d('Failed!');
			exit();
		} else {
			$this->_d("OK!");
			#$this->_d($connect);
		}
		
		// Connected... now see if we have user & pass.
		if(!$ldaptestuser){
			$this->_d("You didn't specify a username to test the authentication with.");
			exit();
		}
 		if(!$ldaptestpass){
 			$this->_d("You didn't specify a password to test the authentication with.");
 			exit();
 		}
		
		// Now go through the supplied DNs and see if we can bind as the user in them
		$dns = explode(";", $ldapbase);
		$found = FALSE;
		
		foreach($dns as $dn){
			if($found == FALSE){ 
				$thisdn = trim($dn);
				$this->_d("Trying $ldaptestuser,$thisdn... ", FALSE);
				$bind = @ldap_bind($connect, "$ldaptestuser,$thisdn", $ldaptestpass);
				if(!$bind){ 
					$this->_d("Failed."); 
				} else {
					$this->_d("Success!");
					$correctdn = $thisdn;
					$found = TRUE;
				}
			}
		}
		
		if($found == FALSE){
			$this->_d("Failed to bind with the given user in any of the supplied base DNs");
			exit();
		}
		
		$this->_d("The user authentication was OK. Going to find their details...");
		
		$search = @ldap_search($connect, $correctdn, $ldapfilter);
		if(!$search){
			$this->_d("Could not find the user's details - the query filter is probably incorrect.");
			exit();
		}
		 
		$info = ldap_get_entries($connect, $search); 
		$user['displayname'] = $info[0]['displayname'][0];
		$user['email'] = $info[0]['mail'][0];
		$user['memberof'] = $info[0]['memberof'];
		//echo var_export($user, true);
		$str = "<strong>Username:</strong> {$this->input->post('ldaptestuser')}<br />";
		$str = "<strong>Display name:</strong> {$user['displayname']}<br />";
		$str .= "<strong>Email:</strong> {$user['email']}<br />";
		$str .= "<strong>Member of:</strong><ul>";
		unset($info[0]['memberof']['count']);
		foreach($info[0]['memberof'] as $group){
			$str .= "<li>$group</li>";
		}
		$str .= "</ul>";
		$this->_d($str);
		
	}
	
	
	
	
	function _d($message, $br = TRUE){
		echo $message;
		echo ($br == TRUE) ? '<br /><br />' : '';
		ob_flush();
	}
	
	
	
	
}


?>
