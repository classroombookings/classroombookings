<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Classroombookings. Hassle-free resource booking for schools. <http://classroombookings.com/>
 * Copyright (C) 2006-2011 Craig A Rodway <craig.rodway@gmail.com>
 *
 * This file is part of Classroombookings.
 * Classroombookings is licensed under the Affero GNU GPLv3 license.
 * Please see license-classroombookings.txt for the full license text.
 */
 
class Authentication extends Configure_Controller
{
	
	
	function __construct(){
		parent::__construct();
		$this->load->model('security_model');
	}
	
	
	
	
	/**
	 * PAGE: Authentication index page (with tabs)
	 */
	function index($active_tab = null)
	{
		$this->auth->check('configure');
		
		// Body data for each tab
		$tab['settings'] = $this->settings->get();
		$tab['users'] = $this->security_model->get_users_dropdown();
		$tab['groups'] = $this->security_model->get_groups_dropdown();
		$tab['ldapgroups'] = $this->security_model->get_ldap_groups();		
		
		// List of tabs
		$tabs[] = array(	
			'id' => 'main',
			'title' => 'Global',
			'view' => $this->load->view('authentication/global', $tab, true),
		);
		if ($this->settings->get('auth_ldap_enable') == 1)
		{
			$tabs[] = array(
				'id' => 'ldap',
				'title' => 'LDAP',
				'view' => $this->load->view('authentication/ldap', $tab, true),
			);
			$tabs[] = array(
				'id' => 'ldapgroups',
				'title' => 'LDAP Groups',
				'view' => $this->load->view('authentication/ldap-groups', $tab, true),
			);
		}
		if ($this->settings->get('auth_preauth_enable') == 1)
		{
			$tabs[] = array(
				'id' => 'preauth',
				'title' => 'Pre-authentication',
				'view' => $this->load->view('authentication/preauth', $tab, true),
			);
		}
		
		// Tab data for main page
		$body['tabs'] = $tabs;
		$body['active_tab'] = ($active_tab == null) ? $this->session->flashdata('active_tab') : $active_tab;
		$body['active_tab'] = (empty($body['active_tab'])) ? 'main' : $body['active_tab'];
		
		$data['title'] = 'Configure';
		$data['body'] = $this->load->view('parts/tabs', $body, TRUE);
		$this->page($data);
	}
	
	
	
	
	/**
	 * FORM POST: Save global auth settings
	 */
	function save_main()
	{
		$this->form_validation->set_rules('auth_anonuserid', 'Anonymous user', 'required|max_length[10]|integer');
		$this->form_validation->set_rules('auth_ldap_enable', 'Enable LDAP', 'required|exact_length[1]');
		$this->form_validation->set_rules('auth_preauth_enable', 'Enable pre-authentication', 'required|exact_length[1]');
		$this->form_validation->set_error_delimiters('<li>', '</li>');
		
		if ($this->form_validation->run() == false)
		{
			// Validation failed
			return $this->index();
		}
		else
		{
			$data['auth_anonuserid'] = $this->input->post('auth_anonuserid');
			$data['auth_ldap_enable'] = $this->input->post('auth_ldap_enable');
			$data['auth_preauth_enable'] = $this->input->post('auth_preauth_enable');
			
			// Check if there is an existing pre-auth key. If not, make one.
			$preauthkey = $this->settings->get('auth_preauth_key');
			if (empty($preauthkey))
			{
				$data['auth_preauth_key'] = $this->_genpak();
			}

			$this->settings->save($data);

			$this->session->set_flashdata('flash', 
				$this->msg->notice(lang('CONF_AUTH_SAVE_OK')));
			redirect('authentication');
		}
	}
	
	
	
	
	/* ============= LDAP ========== */
	
	
	
	
	/**
	 * FORM POST: Save LDAP settings
	 */
	function save_ldap()
	{
		$this->form_validation->set_rules('auth_ldap_host', 'LDAP host', 'required|max_length[50]|trim');
		$this->form_validation->set_rules('auth_ldap_port', 'LDAP TCP port', 'required|max_length[5]|integer|callback__port_check');
		$this->form_validation->set_rules('auth_ldap_base', 'LDAP Base DN', 'required|max_length[65535]');
		$this->form_validation->set_rules('auth_ldap_filter', 'LDAP filter', 'required|max_length[65535]');
		$this->form_validation->set_rules('auth_ldap_groupid', 'LDAP group', 'required|integer');
		$this->form_validation->set_rules('ldaptestuser', 'LDAP test username');
		
		$this->form_validation->set_error_delimiters('<li>', '</li>');
		
		if($this->form_validation->run() == false)
		{
			$this->index('ldap');
		}
		else
		{
			// All fields were validated!
			$data['auth_ldap_host'] = $this->input->post('auth_ldap_host');
			$data['auth_ldap_port'] = $this->input->post('auth_ldap_port');
			$data['auth_ldap_base'] = $this->input->post('auth_ldap_base');
			$data['auth_ldap_filter'] = $this->input->post('auth_ldap_filter');
			$data['auth_ldap_groupid'] = $this->input->post('auth_ldap_groupid');
			$data['auth_ldap_loginupdate'] = $this->input->post('auth_ldap_loginupdate');
			
			$save = $this->settings->save($data);
			
			$this->session->set_flashdata('active_tab', 'ldap');
			$this->session->set_flashdata('flash', 
				$this->msg->notice(lang('CONF_AUTH_LDAP_SAVE_OK')));
			redirect('authentication');
		}
		
	}
	
	
	
	
	/**
	 * FORM POST: Test LDAP settings
	 *
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
		$ldaphost = $this->input->post('auth_ldap_host');
		$ldapport = $this->input->post('auth_ldap_port');
		$ldapbase = $this->input->post('auth_ldap_base');
		$ldapfilter = str_replace("%u", $this->input->post('ldaptestuser'), $this->input->post('auth_ldap_filter'));
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
		print_r($info);
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
	
	
	
	
	/**
	 * FORM POST: Lookup and store the LDAP groups
	 */
	function get_ldap_groups(){
		// Get auth settings
		$settings = $this->settings->get('auth.');
		// Set the tab for when returning to config page
		#$this->session->set_flashdata('tab', 'conf-ldap-groups');
		
		$base = $this->input->post('ldapbase');
		$user = $this->input->post('user');
		$pass = $this->input->post('pass');
		$clear = ($this->input->post('clear') == '1') ? TRUE : FALSE;
		$ignorespecial = ($this->input->post('ignorespecial') == '1') ? TRUE : FALSE;
		
		$filter = '(&(objectCategory=group)(samaccounttype=268435456)(cn=*))';
		$fields = array('samaccountname');
		
		// Connect to LDAP server
		$connect = ldap_connect($settings['auth.ldap.host'], $settings['auth.ldap.port']);
		if(!$connect){
			$this->msg->add('err', 'Could not connect to LDAP server.');
			redirect('configure/ldapgroups');
		}
		
		// Now go through the supplied DNs and see if we can bind as the user in them
		$dns = explode(";", $base);
		$ldap_groups = array();
		
		// Loop through the DNs
		foreach($dns as $dn){
			$thisdn = trim($dn);
			$bind = ldap_bind($connect, $user, $pass);
			$search = ldap_search($connect, $dn, $filter, $fields);
			$entries = ldap_get_entries($connect, $search);
			for($i = 0; $i < $entries['count']; $i++){
				//array_push($ldap_groups, $entries[$i]['samaccountname'][0]);
				$dn = $entries[$i]['dn'];
				$dnarray = explode(',', $dn);
				array_push($ldap_groups, str_replace('CN=', '', $dnarray[0]));
				unset($dnarray);
			}
		}
		
		#die(print_r($ldap_groups));
		
		// Empty the table if necessary before fetching the 'existing' groups
		if($clear == TRUE){
			$this->db->empty_table('ldapgroups');
			$this->db->empty_table('groups2ldapgroups');
			$this->db->empty_table('departments2ldapgroups');
		}
		
		// Fetch the groups we already have in our DB
		$existing_groups = $this->security->get_ldap_groups();
		
		// Now find out if there are new groups to add
		$groups_to_add = array();
		foreach($ldap_groups as $ldap_group){
			if(!in_array($ldap_group, $existing_groups)){
				if($ignorespecial == TRUE){
					if(preg_match('/^([-a-z0-9_-\s])+$/i', $ldap_group) !== 0){
						array_push($groups_to_add, $ldap_group);
					}
				} else {
					array_push($groups_to_add, $ldap_group);
				}
			}
		}
		
		echo "Existing groups:\n\n";
		#print_r($existing_groups);
		
		echo "\n\n\n\n";
		
		echo "LDAP groups:\n\n";
		print_r($ldap_groups);
		
		echo "\n\n\n\n";
		
		echo "New groups to add to db:\n\n";
		#print_r($groups_to_add);
		
		
		// Check if we need to add any groups to begin with...
		if(count($groups_to_add) > 0){
			
			$sql = 'INSERT INTO ldapgroups (name) VALUES ';
			foreach($groups_to_add as $group_to_add){
				$sql .= "('$group_to_add'),";
			}
			// Remove last comma
			$sql = preg_replace('/,$/', '', $sql);
			$query = $this->db->query($sql);
			
			if($query == FALSE){
				$this->msg->add('err', 'Could not insert LDAP groups into local database');
			} else {
				$this->msg->add('info', 'Groups were successfully imported.');
			}
			
		} else {
			
			$this->msg->add('note', 'There were no new groups to add.');
			
		}
		
		redirect('configure/ldapgroups');
		
	}
	
	
	
	
	/* ========== PRE-AUTH ========== */
	
	
	
	
	/**
	 * FORM POST: Save pre-authentication settings
	 */
	function save_preauth()
	{
		// Determine which button was clicked
		$actions['save'] = $this->input->post('action_save');
		$actions['newkey'] = $this->input->post('action_newkey');
		$actions = array_flip($actions);
		$submit = $this->input->post('submit');
		$action = $actions[$submit];
		
		if ($action == 'newkey')
		{
			// Generate a new key only. Don't alter settings
			
			$data['auth_preauth_key'] = $this->_genpak();
			$save = $this->settings->save($data);
			$msg = $this->msg->notice(lang('CONF_AUTH_PREAUTH_NEWKEY'));
			$this->session->set_flashdata('active_tab', 'preauth');
			$this->session->set_flashdata('flash', $msg);
			redirect('authentication');
		}
		elseif ($action == 'save')
		{
			// Save settings, don't generate a new key
			
			$this->form_validation->set_rules('auth_preauth_groupid', 'Preauth group', 'required|integer');
			$this->form_validation->set_rules('auth_preauth_emaildomain', 'Preauth email domain', 'required|max_length[50]');
			
			$this->form_validation->set_error_delimiters('<li>', '</li>');
		
			if($this->form_validation->run() == false)
			{
				$this->index('preauth');
			}
			else
			{
				$data['auth_preauth_groupid'] = $this->input->post('auth_preauth_groupid');
				$data['auth_preauth_emaildomain'] = str_replace('@', '', $this->input->post('auth_preauth_emaildomain'));
				$save = $this->settings->save($data);
				$msg = $this->msg->notice(lang('CONF_AUTH_PREAUTH_SAVE_OK'));
				$this->session->set_flashdata('active_tab', 'preauth');
				$this->session->set_flashdata('flash', $msg);
				redirect('authentication');
			}	// form validation else
		}	// $action elseif=save
	}
	
	
	
	
	/**
	 * Generate a new key for pre-authentication
	 */
	function _genpak()
	{
		return sha1($this->config->item('encryption_key').sha1(uniqid().time()));
	}
	
	
	
	
	function _d($message, $br = TRUE){
		echo $message;
		echo ($br == TRUE) ? '<br /><br />' : '';
		@ob_flush();
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




/* End of file app/controllers/authentication.php */