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
		$this->output->enable_profiler($this->config->item('profiler'));
	}
	
	
	
	
	function index($tab = 'conf-main'){
		$body['tab'] = ($this->session->flashdata('tab')) ? $this->session->flashdata('tab') : $tab;
		$body['conf']['main'] = $this->settings->get_all('main');
		$body['conf']['auth'] = $this->settings->get_all('auth');
		$body['conf']['groups'] = $this->security->get_groups_dropdown();
		
		$tpl['title'] = 'Configure';
		$tpl['pagetitle'] = 'Configure classroombookings';
		$tpl['body'] = $this->load->view('configure/conf.index.php', $body, TRUE);
		$this->load->view($this->tpl, $tpl);
	}
	
	
	
	
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
			
			$this->session->set_flashdata('flash', $this->msg->info($this->lang->line('CONF_MAIN_SAVE_OK')));
			$this->session->set_flashdata('tab', 'conf-main');
			redirect('configure');
			
		}
		
	}
	
	
	
	
	function save_auth(){
	
		$this->form_validation->set_rules('preauth', 'Pre-authentication enable');
		$this->form_validation->set_rules('ldap', 'LDAP enable');
		if($this->input->post('ldap') == '1'){
			// LDAP validation (only required if LDAP box is ticked)
			$this->form_validation->set_rules('ldaphost', 'LDAP host', 'required|max_length[50]|trim');
			$this->form_validation->set_rules('ldapport', 'LDAP TCP port', 'required|max_length[5]|integer|callback__port_check');
			$this->form_validation->set_rules('ldapbase', 'LDAP Base DN', 'required|max_length[65536]');
			$this->form_validation->set_rules('ldapfilter', 'LDAP filter', 'required|max_length[65536]');
			$this->form_validation->set_rules('ldapgroup_id', 'LDAP group', 'require|integer');
			$this->form_validation->set_rules('ldaptestuser', 'LDAP test username');
		}
		$this->form_validation->set_error_delimiters('<li>', '</li>');
		
		if($this->form_validation->run() == FALSE){
			
			// Validation failed
			$this->index('conf-auth');
			
		} else {
			
			// All fields were validated!
			
			$preauth = ($this->input->post('preauth') == '1') ? TRUE : FALSE;
			$preauthkey = ($this->input->post('preauthkey')) ? $this->input->post('preauthkey') : FALSE;
			
			// No existing preauthkey, and enabling it for the first time - generate one
			if($preauthkey == FALSE && $preauth == TRUE){
				// Make a new preauth key
				$data['preauthkey'] = sha1(uniqid(rand(), TRUE));
			}
			// Existing preauthkey, and now disabling it by removing it from DB
			if($preauthkey == TRUE && $preauth == FALSE){
				$data['preauthkey'] = NULL;
			}
			$data['ldap'] = ($this->input->post('ldap') == '1') ? 1 : 0;
			$data['ldaphost'] = $this->input->post('ldaphost');
			$data['ldapport'] = $this->input->post('ldapport');
			$data['ldapbase'] = $this->input->post('ldapbase');
			$data['ldapfilter'] = $this->input->post('ldapfilter');
			$data['ldapgroup_id'] = $this->input->post('ldapgroup_id');
			
			$save = $this->settings->save('auth', $data);
			
			if($save == FALSE){
				$this->msg->add('err', $this->lang->line('CONF_AUTH_SAVE_FAIL'));
			} else {
				$this->msg->add('info', $this->lang->line('CONF_AUTH_SAVE_OK'));
			}
			
			$this->session->set_flashdata('tab', 'conf-auth');
			redirect('configure');
			
		}
		
	}
	
	
	
	
	/**
	 * LDAP test function.
	 * Runs through the procedure one line at a time and tests for success.
	 */	 	
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
	
	
	
	
	/**
	 * Check TCP port given in the LDAP config is a valid TCP port number between 1-65536
	 */	 	
	function _port_check($port){
		$port = (int)$port;
		$check = ( ($port >= 1) && ($port <= 65536) );
		if($check == FALSE){
			$this->form_validation->set_message('_port_check', 'The %s must be between 1 and 65536.');
		}
		return $check;
	}
	
	
	
	
}


?>
