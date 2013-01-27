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
	
	
	
	
	// =======================================================================
	// Auth configuration pages
	// =======================================================================
	
	
	
	
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
					$this->flash->set('error', lang('authentication_ldap_save_error'));
				}
			}
		}
		
		$this->layout->add_breadcrumb(lang('authentication_ldap'), 'authentication/ldap');
		$this->layout->set_title(lang('authentication_ldap'));
		$this->layout->set_js('views/authentication/ldap');
		$this->load->library('form');
		$this->data['subnav_active'] = 'authentication/ldap';
	}
	
	
	
	
	/**
	 * LDAP Groups
	 */
	function ldap_groups()
	{
		$this->auth->restrict('crbs.configure.authentication');
		
		$this->load->model('ldap_groups_model');
		
		if ($this->input->post())
		{
			$this->form_validation->set_rules('username', lang('Username'), 'required|max_length[100]|trim')
								  ->set_rules('password', lang('Password'), 'required|max_length[100]')
								  ->set_rules('auth_ldap_base', lang('authentication_ldap_base'), 'max_length[65535]')
								  ->set_rules('mode', lang('authentication_ldap_groups_mode'), 'required');
			
			if ($this->form_validation->run())
			{
				$base = $this->input->post('auth_ldap_base');
				$username = $this->input->post('username');
				$password = $this->input->post('password');
				$mode = $this->input->post('mode');
				
				// Load LDAP library with provided settings
				$this->load->library('ldap', array(
					'auth_ldap_host' => option('auth_ldap_host'),
					'auth_ldap_port' => option('auth_ldap_port'),
					'auth_ldap_base' => $base,
				));
				
				if ($mode == 'sync')
				{
					$result = $this->ldap->sync_groups($username, $password);
					$flash_success = lang('authentication_ldap_groups_sync_success');
				}
				elseif ($mode == 'reload')
				{
					$result = $this->ldap->reload_groups($username, $password);
					$flash_success = lang('authentication_ldap_groups_reload_success');
				}
				
				if ($result)
				{
					$this->flash->set('success', $flash_success, TRUE);
					redirect(current_url());
				}
				else
				{
					$this->flash->set('error', $this->ldap->reason);
				}
			}
		}
		
		$this->ldap_groups_model->order_by('lg_name', 'asc');
		$this->data['ldap_groups'] = $this->ldap_groups_model->get_all();	
		
		$this->layout->add_breadcrumb(lang('authentication_ldap_groups'), 'authentication/ldap_groups');
		$this->layout->set_title(lang('authentication_ldap_groups'));
		$this->load->library('form');
		$this->data['subnav_active'] = 'authentication/ldap_groups';
	}
	
	
	
	
	/**
	 * Pre-authentication
	 */
	function preauth()
	{
		$this->auth->restrict('crbs.configure.authentication');
		
		if ($this->input->post('new_key'))
		{
			$options = array('auth_preauth_key' => $this->auth->preauth->generate_key());
			
			// Save options
			if ($this->options_model->set($options))
			{
				$this->flash->set('success', lang('authentication_preauth_new_key_success'), TRUE);
				redirect('authentication/preauth');
			}
			else
			{
				$this->flash->set('error', lang('authentication_preauth_new_key_error'));
			}
		}
		
		if ($this->input->post())
		{
			$this->form_validation->set_rules('auth_preauth_g_id', 'Default Classroombookings group', 'integer')
								  ->set_rules('auth_preauth_email_domain', 'Default email domain', 'required|max_length[100]|trim');
			
			if ($this->form_validation->run())
			{
				$options = array(
					'auth_preauth_g_id' => (int) $this->input->post('auth_preauth_g_id'),
					'auth_preauth_email_domain' => preg_replace('/^@/', '', $this->input->post('auth_preauth_email_domain')),
				);
				
				// Save options
				if ($this->options_model->set($options))
				{
					$this->flash->set('success', lang('authentication_preauth_save_success'), TRUE);
					redirect('authentication/preauth');
				}
				else
				{
					$this->flash->set('error', lang('authentication_preauth_save_error'));
				}
			}
		}
		
		$this->layout->add_breadcrumb(lang('authentication_preauth'), 'authentication/preauth');
		$this->layout->set_title(lang('authentication_preauth'));
		$this->load->library('form');
		$this->data['subnav_active'] = 'authentication/preauth';
		
	}
	
	
	
	
	// =======================================================================
	// AJAX
	// =======================================================================
	
	
	
	
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
	
	
	
	
}

/* End of file ./application/controllers/authentication.php */