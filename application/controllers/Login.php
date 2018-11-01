<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Login extends MY_Controller
{


	public function __construct()
	{
		parent::__construct();
		$this->loggedin = False;
		$this->load->model('school_model');
	}


	function index()
	{
		$layout['title'] = 'Login';
		$layout['showtitle'] = $layout['title'];
		$school_data = $this->school_model->GetInfo();
		$logo = 'uploads/' . $school_data->logo;

		$columns = array(
			'c1' => array(
				'width' => '60%',
				'content' => $this->load->view('login/login_index', NULL, TRUE),
			),
			'c2' => array(
				'width' => '40%',
				'content' => '',
			),
		);

		if ( ! empty($school_data->logo) && file_exists(FCPATH . $logo)) {
			$columns['c2']['content'] = img($logo, FALSE, 'style="max-width:100%;height:auto;display:block"');
		} else {
			$columns['c2']['content'] = '';
		}

		$layout['showtitle'] = 'Login to ' . $school_data->name;
		$layout['body'] = $this->load->view('columns', $columns, TRUE);

		$this->load->view('layout', $layout);
	}


	function submit()
	{
		log_message('debug', 'login submit');

		$this->load->library('form_validation');
		$this->form_validation->set_rules('username', 'Username', 'required');
		$this->form_validation->set_rules('password', 'Password', 'required');

		// Run validation
		if ($this->form_validation->run() == FALSE) {
	  		// Validation failed, load login page again
			return $this->index();
		}

		// Form validation for length etc. passed, now see if the credentials are OK in the DB
		// Post values
		$username = $this->input->post('username');
		$password = $this->input->post('password');

		// Now see if we can login
		if ($this->userauth->trylogin($username, $password)) {
			// Success! Redirect to control panel
			redirect('controlpanel');
		} else {
			$this->session->set_flashdata('auth', msgbox('error', 'Incorrect username and/or password.'));
			return $this->index();
		}
	}


}
