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
	
	
	function __construct()
	{
		parent::__construct();
		$this->lang->load('configure');
		$this->lang->load('authentication');
		$this->load->model(array('users_model', 'groups_model'));
		
		$this->layout->add_breadcrumb(lang('configure_authentication'), 'authentication');
		
		// This data is used in most sub-pages
		$this->data['settings'] = $this->options_model->get_all(TRUE);
		$this->data['users'] = $this->users_model->dropdown('u_id', 'u_username');
		$this->data['groups'] = $this->groups_model->dropdown('g_id', 'g_name');
		
		$this->data['subnav'] = array(
			array(
				'uri' => 'authentication',
				'text' => 'Global',
				'test' => TRUE,
			),
			array(
				'uri' => 'authentication/ldap',
				'text' => lang('authentication_ldap'),
				'test' => option('auth_ldap_enable'),
			),
			array(
				'uri' => 'authentication/ldap_groups',
				'text' => lang('authentication_ldap_groups'),
				'test' => option('auth_ldap_enable'),
			),
			array(
				'uri' => 'authentication/preauth',
				'text' => lang('authentication_preauth'),
				'test' => option('auth_preauth_enable'),
			),
		);
	}
	
	
	
	
	// -------------------------------------------------------------------------
	
	
	
	
	/**
	 * Global authentication settings
	 */
	function index()
	{
		$this->auth->restrict('crbs.configure.authentication');
		
		if ($this->input->post())
		{
			$this->form_validation->set_rules('auth_anon_u_id', 'Anonymous user', 'max_length[10]|integer')
								  ->set_rules('auth_ldap_enable', 'Enable LDAP', 'required|exact_length[1]')
							  	  ->set_rules('auth_preauth_enable', 'Enable pre-authentication', 'required|exact_length[1]');
			
			if ($this->form_validation->run())
			{
				$options = array(
					'auth_anon_u_id' => (int) $this->input->post('auth_anon_u_id'),
					'auth_ldap_enable' => (int) $this->input->post('auth_ldap_enable'),
					'auth_preauth_enable' => (int) $this->input->post('auth_preauth_enable'),
				);
				
				// Check if there is an existing pre-auth key. If not, make one.
				if ($options['auth_preauth_enable'] === 1 && strlen(option('auth_preauth_key')) === 0)
				{
					$options['auth_preauth_key'] = $this->auth->preauth->generate_key();
				}
				
				// Save options
				if ($this->options_model->set($options))
				{
					$this->flash->set('success', lang('authentication_save_success'), TRUE);
					redirect('authentication');
				}
				else
				{
					$this->flash->set('error', lang('authentication_save_error'));
				}
			}
		}
		
		$this->layout->set_title(lang('configure_authentication'));
		$this->load->library('form');
		$this->data['subnav_active'] = 'authentication';
	}
	
	
	
	
	/**
	 * LDAP settings
	 */
	function ldap()
	{
		$this->auth->restrict('crbs.configure.authentication');
		
		if ($this->input->post())
		{
			$this->form_validation->set_rules('auth_ldap_host', 'Server host', 'required|max_length[50]|trim')
								  ->set_rules('auth_ldap_port', 'Server port', 'required|max_length[5]|integer|valid_port')
								  ->set_rules('auth_ldap_base', 'Base DN', 'required|max_length[65535]')
								  ->set_rules('auth_ldap_filter', 'Query filter', 'required|max_length[65535]')
								  ->set_rules('auth_ldap_g_id', 'Default group', 'required|integer')
								  ->set_rules('auth_ldap_update', 'Login update', 'required|integer');
			
			if ($this->form_validation->run())
			{
				$options = array(
					'auth_ldap_host' => $this->input->post('auth_ldap_host'),
					'auth_ldap_port' => (int) $this->input->post('auth_ldap_port'),
					'auth_ldap_base' => $this->input->post('auth_ldap_base'),
					'auth_ldap_filter' => $this->input->post('auth_ldap_filter'),
					'auth_ldap_g_id' => (int) $this->input->post('auth_ldap_g_id'),
					'auth_ldap_update' => (int) $this->input->post('auth_ldap_update'),
				);
				
				// Save options
				if ($this->options_model->set($options))
				{
					$this->flash->set('success', lang('authentication_ldap_save_success'), TRUE);
					redirect('authentication/ldap');
				}
				else
				{
					$this->flash->set('error', lang('authentication_ldap_save_success'));
				}
			}
		}
		
		$this->layout->add_breadcrumb(lang('authentication_ldap'), 'authentication/ldap');
		$this->layout->set_title(lang('authentication_ldap'));
		$this->load->library('form');
		$this->data['subnav_active'] = 'authentication/ldap';
	}
	
	
	
	
	public function test_ldap()
	{
		$settings = array(
			'auth_ldap_host' => $this->input->post('auth_ldap_host'),
			'auth_ldap_port' => $this->input->post('auth_ldap_port'),
			'auth_ldap_base' => $this->input->post('auth_ldap_base'),
			'auth_ldap_filter' => $this->input->post('auth_ldap_filter'),
		);
		
		$this->load->library('ldap', $settings);
		
		if ($this->ldap->is_supported())
		{
			$username = $this->input->post('username');
			$password = $this->input->post('password');
			
			$response = $this->ldap->authenticate($username, $password, TRUE);
			
			if (is_array($response))
			{
				$this->json = array(
					'status' => 'ok',
					'user' => $response,
				);
			}
			else
			{
				$this->json = array(
					'status' => 'err',
					'reason' => $this->ldap->reason,
				);
			}
		}
		else
		{
			$this->json = array(
				'status' => 'err',
				'reason' => 'LDAP support is not enabled in PHP.',
			);
		}
	}
	
	
	
	
	/**
	 * FORM POST: Lookup and store the LDAP groups
	 */
	function get_ldap_groups()
	{
		$this->auth->restrict('crbs.configure.authentication');
		
		$base = $this->input->post('auth_ldap_base');
		$username = $this->input->post('username');
		$password = $this->input->post('password');
		
		$mode = $this->input->post('mode');
		
		$filter = '(&(objectCategory=group)(samaccounttype=268435456)(cn=*))';
		$fields = array('samaccountname');
		
		// Load LDAP library
		
		$settings = array(
			'auth_ldap_host' => option('auth_ldap_host'),
			'auth_ldap_port' => option('auth_ldap_port'),
			'auth_ldap_base' => $base,
			'auth_ldap_filter' => $filter,
		);
		
		$this->load->library('ldap', $settings);
		
		// Get the groups within the base DNs defined
		$this->ldap->get_groups($username, $password);
		
		/*
		
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
		*/
		
	}
	
	
	
	
	/* ========== PRE-AUTH ========== */
	
	
	
	
	/**
	 * FORM POST: Save pre-authentication settings
	 */
	function save_preauth()
	{
		$this->auth->restrict('crbs.configure.authentication');
		$this->auto_view = FALSE;
		
		if ($this->input->get('new_key'))
		{
			$new_key = $this->auth->preauth->generate_key();
			$options = array('auth_preauth_key' => $new_key);
			
			// Save options
			if ($this->options_model->set($options))
			{
				$this->flash->set('success', lang('CONF_AUTH_PREAUTH_NEWKEY'), TRUE);
			}
			else
			{
				$this->flash->set('error', 'The new key could not be saved.', TRUE);
			}
			
			redirect('authentication/index/preauth');
		}
		
		if ($this->input->post())
		{
		
			$this->form_validation->set_rules('auth_preauth_g_id', 'Default Classroombookings group', 'required|integer')
								  ->set_rules('auth_preauth_email_domain', 'Default email domain', 'required|max_length[100]|trim');
			
			if ($this->form_validation->run())
			{
				$email_domain = preg_replace('/^@/', '', $this->input->post('auth_preauth_email_domain'));
				
				$options = array(
					'auth_preauth_g_id' => (int) $this->input->post('auth_preauth_g_id'),
					'auth_preauth_email_domain' => $email_domain,
				);
				
				// Save options
				if ($this->options_model->set($options))
				{
					$this->flash->set('success', lang('CONF_AUTH_PREAUTH_SAVE_OK'), TRUE);
					redirect('authentication/index/preauth');
				}
				else
				{
					$this->flash->set('error', 'The settings could not be updated. Please try again.');
				}
			}
			else
			{
				return $this->index('preauth');
			}
			
		}
		
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