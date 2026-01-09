<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Login extends MY_Controller
{


	public function __construct()
	{
		parent::__construct();
	}


	function index()
	{
		if ($this->input->post()) {
			$this->handle_submit();
		}

		$this->data['title'] = lang('auth.log_in');

		$this->data['message'] = '';
		if (setting('login_message_enabled')) {
			$this->data['message'] = html_escape(setting('login_message_text'));
		}

		$columns = array(
			'c1' => array(
				'width' => '60%',
				'content' => $this->load->view('login/login_index', $this->data, TRUE),
			),
			'c2' => array(
				'width' => '40%',
				'content' => '',
			),
		);

		$image_url = image_url(setting('logo'));
		$logo_html = (!empty($image_url))
			? img($image_url, FALSE, ["style" => 'max-width:100%;max-height:300px;width:auto;display:block'])
			: '';

		$columns['c2']['content'] = $logo_html;

		$title = sprintf("<h2>%s</h2>", lang('auth.log_in'));
		$body = $this->load->view('columns', $columns, TRUE);

		$auth = (string) $this->session->flashdata('auth');

		$this->data['body'] = $title . $auth . $body;

		return $this->render();
	}


	private function handle_submit()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('username', 'lang:user.field.username', 'required|max_length[255]');
		$this->form_validation->set_rules('password', 'lang:user.field.password', 'required');

		// Run validation
		if ($this->form_validation->run() == FALSE) {
			return false;
		}

		$username = $this->input->post('username');
		$password = $this->input->post('password');

		if ($this->userauth->log_in($username, $password)) {
			// Success
			$uri = '';
			if (isset($_SESSION['post_login_uri'])) {
				$uri = $_SESSION['post_login_uri'];
				unset($_SESSION['post_login_uri']);
			}
			redirect($uri);
		} else {
			$this->data['error'] = msgbox('error', lang('auth.bad_credentials'));
			return false;
		}
	}


}
