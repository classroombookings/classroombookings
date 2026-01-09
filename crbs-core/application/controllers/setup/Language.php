<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Language extends MY_Controller
{


	public function __construct()
	{
		parent::__construct();

		$this->require_logged_in();
		$this->require_permission(Permission::SETUP_SETTINGS);
	}


	/**
	 * Language settings
	 *
	 */
	public function index()
	{
		$this->data['all_languages'] = $this->lang->get_languages();

		$opts = [];
		foreach ($this->data['all_languages'] as $lang_id) {
			$title = lang(sprintf('language.lang.%s', $lang_id));
			$title = empty($title) ? $lang_id : html_escape($title);
			$opts[ $lang_id ] = $title;
		}

		$this->data['language_options'] = $opts;

		$this->data['settings'] = $this->settings_model->get_all('lang');

		if ($this->input->post()) {
			$this->save();
		}

		$this->data['title'] = lang('language.language');
		$this->data['showtitle'] = $this->data['title'];

		$body = $this->load->view('setup/language/language', $this->data, TRUE);

		$this->data['body'] = $body;

		return $this->render();
	}


	/**
	* Controller function to handle a submitted form
	*
	*/
	private function save()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('languages[]', 'lang:language.field.languages', 'required');
		$this->form_validation->set_rules('default_language', 'lang:language.field.default_language', 'required');

		if ($this->form_validation->run() == false) {
			return false;
		}

		$settings = array(
			'languages' => $this->input->post('languages'),
			'default_language' => $this->input->post('default_language'),
		);

		$this->settings_model->set($settings, 'lang');

		$this->session->set_flashdata('saved', msgbox('info', lang('language.save.success')));

		redirect('setup/language');
	}


}
