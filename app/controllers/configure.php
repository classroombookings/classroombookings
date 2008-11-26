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
#				die(print_r($_POST));
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
		error_reporting(E_ALL);
		#echo '<pre style="font-size:9pt;">';
		$this->_d('Testing for PHP LDAP module... ', FALSE);
		ob_flush();
		if(!function_exists('ldap_bind')){
			$this->_d("It appears you don't have the PHP LDAP module installed - cannot continue!");
			exit();
		} else {
			$this->_d('OK');
		}
		$ldaphost = $this->input->post('ldaphost');
		$ldapport = $this->input->post('ldapport');
		$ldapbase = $this->input->post('ldapbase');
		$ldaptestuser = $this->input->post('ldaptestuser');
		$ldaptestpass = $this->input->post('ldaptestpass');
		
		if(!$ldaphost && !$ldapport){
			$this->_d('No LDAP server and port combination were supplied - cannot continue.');
			exit();
		}
		
		if(!$ldapbase){
			$this->_d("No LDAP search base was specified - cannot continue.");
			exit();
		}
			
		$this->_d(sprintf("Trying to connect to '%s' on port %d... ", $ldaphost, $ldapport), FALSE);
		$connect = ldap_connect($ldaphost, $ldapport);
		
		if(!$connect){
			$this->_d('Failed!');
			exit();
		} else {
			$this->_d("OK!");
			$this->_d($connect);
		}
		
		if(!$ldaptestuser && !$ldaptestpass){
			$this->_d("You didn't specify a username and password to test the authentication with. It seems the connection was successful so far.");
			exit();
		}
		
		$this->_d("Trying bind with supplied credentials... ", FALSE);
		$bind = @ldap_bind($connect, $ldaptestuser, $ldaptestpass);
		
		if(!$bind){
			$this->_d("failed.");
			$this->_d($bind);
			exit();
		} else {
			$this->_d("seems successful.");
		}
		
		


		#echo "</pre>";
	}
	
	
	
	function _d($message, $br = TRUE){
		echo "$message";
		echo ($br == TRUE) ? '<br /><br />' : '';
		ob_flush();
	}
	
	
	
	
}


?>
