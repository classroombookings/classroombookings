<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Authentication extends MY_Controller
{


	public function __construct()
	{
		parent::__construct();

		$this->require_logged_in();
		$this->require_permission(Permission::SETUP_AUTHENTICATION);

		$this->load->model([
			'roles_model',
			'departments_model',
		]);

		$this->data['showtitle'] = lang('auth.authentication');
	}


	/**
	* LDAP Configuration
	*
	*/
	public function ldap()
	{
		$ldap_available = extension_loaded('ldap');

		$this->data['title'] = lang('auth.ldap.ldap');

		$this->data['settings'] = $this->settings_model->get_all('auth');

		$departments = $this->departments_model->Get(NULL, NULL, NULL);
		$this->data['department_options'] = results_to_assoc($departments, 'department_id', 'name', sprintf('(%s)', lang('app.none')));

		$roles = $this->roles_model->get_all();
		$this->data['role_options'] = results_to_assoc($roles, 'role_id', 'name', sprintf('(%s)', lang('app.none')));

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
			$body = msgbox('error', lang('auth.ldap.error.no_module'));
		}

		$this->data['body'] = '<h2>' . lang('auth.ldap.ldap') . '</h2>' . $body;

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

		$username = $this->input->post('username');
		$password = $this->input->post('password');

		$this->load->library('auth_ldap', $config);

		if (is_demo_mode()) {
			$this->data['config'] = $config;
			$this->data['user'] = false;
			$this->data['user_bind_dn'] = $this->auth_ldap->get_user_bind_dn($username);
			$this->data['user_search_filter'] = $this->auth_ldap->get_user_search_filter($username);
			$this->data['errors'] = ['demo_mode'];
			$this->load->view('settings/authentication/ldap_test_results', $this->data);
			return;
		}

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

		$opt_required = '';
		if ($this->input->post('ldap_enabled') == 1) {
			$opt_required = 'required|';
		}

		$this->form_validation->set_rules('ldap_enabled', 'lang:auth.ldap.field.ldap_enabled', 'required|is_natural');
		$this->form_validation->set_rules('ldap_create_users', 'lang:auth.ldap.field.ldap_create_users', $opt_required.'is_natural');
		$this->form_validation->set_rules('ldap_server', 'lang:auth.ldap.field.ldap_server', $opt_required.'max_length[100]');
		$this->form_validation->set_rules('ldap_port', 'lang:auth.ldap.field.ldap_port', $opt_required.'is_natural_no_zero');
		$this->form_validation->set_rules('ldap_version', 'lang:auth.ldap.field.ldap_version', $opt_required.'is_natural');
		$this->form_validation->set_rules('ldap_use_tls', 'lang:auth.ldap.field.ldap_use_tls', $opt_required.'is_natural');
		$this->form_validation->set_rules('ldap_ignore_cert', 'lang:auth.ldap.field.ldap_ignore_cert', $opt_required.'is_natural');
		$this->form_validation->set_rules('ldap_bind_dn_format', 'lang:auth.ldap.field.ldap_bind_dn_format', $opt_required.'max_length[1024]');
		$this->form_validation->set_rules('ldap_base_dn', 'lang:auth.ldap.field.ldap_base_dn', 'max_length[1024]');
		$this->form_validation->set_rules('ldap_search_filter', 'lang:auth.ldap.field.ldap_search_filter', 'max_length[1024]');

		$this->form_validation->set_rules('ldap_attr_firstname', 'lang:user.field.firstname', 'max_length[255]');
		$this->form_validation->set_rules('ldap_attr_lastname', 'lang:user.field.lastname', 'max_length[255]');
		$this->form_validation->set_rules('ldap_attr_displayname', 'lang:user.field.displayname', 'max_length[255]');
		$this->form_validation->set_rules('ldap_attr_email', 'lang:user.field.email', 'max_length[255]');

		$this->form_validation->set_rules('ldap_default_role_id', 'lang:role.role', 'is_natural_no_zero');
		$this->form_validation->set_rules('ldap_default_department_id', 'lang:department.department', 'is_natural_no_zero');

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
			'ldap_default_role_id',
			'ldap_default_department_id',
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

		if (is_demo_mode()) {
			$settings['ldap_enabled'] = 0;
		}

		$this->settings_model->set($settings, 'auth');

		$this->session->set_flashdata('saved', msgbox('info', lang('auth.ldap.save.success')));

		redirect('settings/authentication/ldap');
	}



}
