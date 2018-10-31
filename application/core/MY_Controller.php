<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{

	/**
	 * Whether the user is loged in
	 *
	 * @var boolean
	 *
	 */
	public $loggedin = FALSE;


	/**
	 * Auth level of logged-in user
	 *
	 * @var boolean
	 *
	 */
	public $authlevel = FALSE;


	public function __construct()
	{
		parent::__construct();
		$this->output->enable_profiler(config_item('show_profiler') === TRUE);
	}


	public function require_logged_in()
	{
		// Check loggedin status
		if ( ! $this->userauth->loggedin()) {
			$this->session->set_flashdata('login', msgbox('error', 'Please log in to access this page.'));
			redirect('login');
		} else {
			$this->loggedin = TRUE;
			$this->authlevel = $this->userauth->GetAuthLevel($this->session->userdata('user_id'));
		}
	}


}
