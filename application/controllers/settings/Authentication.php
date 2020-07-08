<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Authentication extends MY_Controller
{


	public function __construct()
	{
		parent::__construct();

		$this->require_auth_level(ADMINISTRATOR);

		$this->lang->load('auth');

		$this->data['showtitle'] = 'Authentication';
	}


	/**
	* LDAP Configuration
	*
	*/
	public function ldap()
	{
		$ldap_available = extension_loaded('ldap');

		$this->data['title'] = 'LDAP';

		$this->data['settings'] = $this->settings_model->get_all('auth');

		if ($ldap_available) {

			$columns = array(
				'c1' => array(
					'content' => $this->load->view('settings/authentication/ldap', $this->data, TRUE),
					'width' => '70%',
				),
				'c2' => array(
					'content' => $this->load->view('settings/authentication/ldap_test', $this->data, TRUE),
					'width' => '30%',
				),
			);

			$body = $this->session->flashdata('saved');
			$body .= $this->load->view('columns', $columns, TRUE);

		} else {
			$body = msgbox('error', 'The PHP LDAP module is not installed or enabled.');
		}

		$this->data['body'] = '<h2>LDAP</h2>' . $body;

		if ($this->input->post()) {
			$this->save_ldap();
		}

		return $this->render();
	}


	public function ldap_test()
	{
		$config = [
			'server' => $this->input->post('ldap_server'),
			'port' => $this->input->post('ldap_port'),
			'version' => $this->input->post('ldap_version'),
			'use_tls' => $this->input->post('ldap_use_tls'),
			'ignore_cert' => $this->input->post('ldap_ignore_cert'),
			'bind_dn_format' => $this->input->post('ldap_bind_dn_format'),
			'base_dn' => $this->input->post('ldap_base_dn'),
			'search_filter' => $this->input->post('ldap_search_filter'),
			'attr_firstname' => $this->input->post('ldap_attr_firstname'),
			'attr_lastname' => $this->input->post('ldap_attr_lastname'),
			'attr_displayname' => $this->input->post('ldap_attr_displayname'),
			'attr_email' => $this->input->post('ldap_attr_email'),
		];

		$this->load->library('auth_ldap', $config);

		$username = $this->input->post('username');
		$password = $this->input->post('password');

		$user = $this->auth_ldap->verify($username, $password);

		$this->data['config'] = $config;
		$this->data['user'] = $user;
		$this->data['mapping'] = $this->auth_ldap->map_user_attributes($user);
		$this->data['errors'] = [];
		$this->data['user_bind_dn'] = $this->auth_ldap->get_user_bind_dn($username);
		$this->data['user_search_filter'] = $this->auth_ldap->get_user_search_filter($username);

		if ($user === FALSE) {
			$this->data['errors'] = $this->auth_ldap->get_errors();
		}

		$this->load->view('settings/authentication/ldap_test_results', $this->data);
	}



	/**
	* Handle submitted form
	*
	*/
	private function save_ldap()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('ldap_enabled', 'LDAP Enabled', 'required|is_natural');
		$this->form_validation->set_rules('ldap_create_users', 'Create users', 'required|is_natural');
		$this->form_validation->set_rules('ldap_server', 'Server', 'required|max_length[100]');
		$this->form_validation->set_rules('ldap_port', 'Port', 'required|is_natural_no_zero');
		$this->form_validation->set_rules('ldap_version', 'Version', 'required|is_natural');
		$this->form_validation->set_rules('ldap_use_tls', 'Use TLS', 'required|is_natural');
		$this->form_validation->set_rules('ldap_ignore_cert', 'Ignore certiicate', 'required|is_natural');
		$this->form_validation->set_rules('ldap_bind_dn_format', 'Bind DN format', 'required|max_length[1024]');
		$this->form_validation->set_rules('ldap_base_dn', 'Base DN', 'max_length[1024]');
		$this->form_validation->set_rules('ldap_search_filter', 'Search filter', 'max_length[1024]');

		$this->form_validation->set_rules('ldap_attr_firstname', 'First Name attribute', 'max_length[255]');
		$this->form_validation->set_rules('ldap_attr_lastname', 'Last Name attribute', 'max_length[255]');
		$this->form_validation->set_rules('ldap_attr_displayname', 'Display Name attribute', 'max_length[255]');
		$this->form_validation->set_rules('ldap_attr_email', 'Email attribute', 'max_length[255]');

		if ($this->form_validation->run() == FALSE) {
			return FALSE;
		}

		$fields = [
			'ldap_enabled',
			'ldap_create_users',
			'ldap_server',
			'ldap_port',
			'ldap_version',
			'ldap_use_tls',
			'ldap_ignore_cert',
			'ldap_bind_dn_format',
			'ldap_base_dn',
			'ldap_search_filter',
			'ldap_attr_firstname',
			'ldap_attr_lastname',
			'ldap_attr_displayname',
			'ldap_attr_email',
		];

		$settings = [];

		foreach ($fields as $field) {

			$value = $this->input->post($field);

			switch ($field) {
				case 'ldap_enabled':
				case 'ldap_create_users':
				case 'ldap_port':
				case 'ldap_version':
				case 'ldap_ignore_cert':
					$value = (int) $this->input->post($field);
				break;
			}

			$settings[ $field ] = $value;
		}

		if (DEMO_MODE) {
			$settings['ldap_enabled'] = 0;
		}

		$this->settings_model->set($settings, 'auth');

		$this->session->set_flashdata('saved', msgbox('info', 'LDAP settings have been updated.'));

		redirect('settings/authentication/ldap');
	}



}
