<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller
{


	public function __construct()
	{
		parent::__construct();

		$this->require_logged_in();

		// Has any setup permissions?
		$setup_permissions = [
			Permission::SETUP_AUTHENTICATION,
			Permission::SETUP_DEPARTMENTS,
			Permission::SETUP_ROLES,
			Permission::SETUP_ROOMS,
			Permission::SETUP_SCHEDULES,
			Permission::SETUP_SESSIONS,
			Permission::SETUP_SETTINGS,
			Permission::SETUP_TIMETABLE_WEEKS,
			Permission::SETUP_USERS,
		];

		$this->require_any_permission($setup_permissions);
	}


	public function index()
	{
		$data = [
			'setup_menu' => $this->menu_model->setup_menu(),
		];

		$this->data['title'] = lang('setup.setup');
		$this->data['body'] = '';
		$this->data['body'] .= $this->session->flashdata('auth');
		$this->data['body'] .= $this->load->view('setup/index', $data, TRUE);

		return $this->render();
	}


}
