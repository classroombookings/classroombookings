<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Profile extends MY_Controller
{


	public function __construct()
	{
		parent::__construct();

		$this->require_logged_in();

		$this->load->model('crud_model');
		$this->load->model('users_model');

		// Load user
		$user_id = $this->userauth->user->user_id;
		$this->data['user'] = $this->users_model->Get($user_id);

		// Language
		$this->data['can_change_lang'] = false;
		$this->data['default_language'] = setting('default_language', 'lang') ?: 'english';
		$lang_settings = $this->settings_model->get_all('lang');
		$enabled_langs = $lang_settings['languages'] ?? [];
		if (count($enabled_langs) > 1) {
			$opts = [];
			foreach ($enabled_langs as $lang_id) {
				$title = lang(sprintf('language.lang.%s', $lang_id));
				$title = empty($title) ? $lang_id : html_escape($title);
				$opts[ $lang_id ] = $title;
			}
			$this->data['language_options'] = $opts;
			$this->data['user_settings'] = [
				'language' => user_setting('language', $this->data['user']->user_id),
			];
			$this->data['can_change_lang'] = true;
		}
	}


	function edit()
	{
		$columns = array(
			'c1' => array(
				'width' => '70%',
				'content' => $this->load->view('profile/profile_edit', $this->data, TRUE),
			),
			'c2' => array(
				'width' => '30%',
				'content' => $this->load->view('profile/profile_edit_side', $this->data, TRUE),
			),
		);

		$this->data['title'] = lang('account.title');
		$this->data['showtitle'] = $this->data['title'];
		$this->data['body'] = $this->load->view('columns', $columns, TRUE);

		return $this->render();
	}


	function save()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('password1', 'lang:user.field.password', 'min_length[8]');
		$this->form_validation->set_rules('password2', 'lang:user.field.password2', 'min_length[8]|matches[password1]');
		$this->form_validation->set_rules('email', 'lang:user.field.email', 'max_length[255]|valid_email');
		$this->form_validation->set_rules('firstname', 'lang:user.field.firstname', 'max_length[20]');
		$this->form_validation->set_rules('lastname', 'lang:user.field.lastname', 'max_length[20]');
		$this->form_validation->set_rules('displayname', 'lang:user.field.displayname', 'max_length[20]');
		$this->form_validation->set_rules('extension', 'lang:user.field.ext', 'max_length[10]');

		if (isset($this->data['language_options'])) {
			$lang_list = implode(',', array_keys($this->data['language_options']));
			$this->form_validation->set_rules('language', 'lang:language.language', "alpha|in_list[{$lang_list}]");
		}

		if ($this->form_validation->run() == FALSE) {
	  		// Validation failed
			return $this->edit();
		}

		// Validation passed!
		$data = array(
			'email' => $this->input->post('email'),
			'firstname' => $this->input->post('firstname'),
			'lastname' => $this->input->post('lastname'),
			'displayname' =>$this->input->post('displayname'),
			'ext' => $this->input->post('ext'),
		);

		// Only update password if one was supplied
		if ($this->input->post('password1') && $this->input->post('password2')) {
			$data['password'] = password_hash((string) $this->input->post('password1'), PASSWORD_DEFAULT);
		}

		// Update session variable with displayname
		$this->session->set_userdata('displayname', $data['displayname']);

		// Handle language
		$lang = $this->input->post('language');
		$group = sprintf('user.%d', $this->data['user']->user_id);
		$key = 'language';
		if (empty($lang)) {
			// Delete
			$this->settings_model->delete($key, $group);
			unset($_SESSION['crbs_lang']);
			delete_cookie('crbs_lang');
			$this->load_language($this->data['default_language']);
		} else {
			$this->settings_model->set($key, $lang, $group);
			$_SESSION['crbs_lang'] = $lang;
			set_cookie('crbs_lang', $lang, TIME_WEEK);
			$this->load_language($lang);
			// $this->load->languages($lang);
		}

		// Now call database to update user and load appropriate message for return value
		if ( ! $this->crud_model->Edit('users', 'user_id', $this->data['user']->user_id, $data)) {
			$line = lang('account.update.error');
			$flashmsg = msgbox('error', $line);
		} else {
			$line = lang('account.update.success');
			$flashmsg = msgbox('info', $line);
		}

		// Go back to index
		$this->session->set_flashdata('saved', $flashmsg);
		redirect('profile/edit');
	}


	public function new_password()
	{
		// Get User ID
		$user_id = $this->userauth->user->user_id;
		$this->data['user'] = $this->users_model->Get($user_id);

		$sess_reset = $_SESSION['force_password_reset'] ?? 0;
		$user_reset = $this->data['user']->force_password_reset;
		$allow_reset = ($sess_reset == 1 || $user_reset == 1);

		if ( ! $allow_reset) {
			show_404();
		}

		if ($this->input->post('password1')) {
			$this->save_new_password($user_id);
		}

		$this->data['title'] = lang('account.password.title');
		$this->data['showtitle'] = $this->data['title'];
		$this->data['body'] = $this->load->view('profile/new_password', $this->data, TRUE);

		return $this->render();
	}


	private function save_new_password($user_id)
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('password1', 'lang:user.field.password', "required|min_length[8]|is_not_current_password[{$user_id}]");
		$this->form_validation->set_rules('password2', 'lang:user.field.password2', 'required|min_length[8]|matches[password1]');

		if ($this->form_validation->run() == FALSE) {
	  		// Validation failed
			return false;
		}

		$userdata = [
			'password' => password_hash((string) $this->input->post('password1'), PASSWORD_DEFAULT),
			'force_password_reset' => 0,
		];

		if ( ! $this->crud_model->Edit('users', 'user_id', $user_id, $userdata)) {
			$line = lang('account.password.error');
			$flashmsg = msgbox('error', $line);
		} else {
			unset($_SESSION['force_password_reset']);
			$line = lang('account.password.success');
			$flashmsg = msgbox('info', $line);
		}


		// Go back to index
		$this->session->set_flashdata('saved', $flashmsg);
		redirect('');
	}


}
