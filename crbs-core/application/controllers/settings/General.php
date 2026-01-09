<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class General extends MY_Controller
{

	public $js = [
		'autocomplete',
	];


	private $tzlist;


	public function __construct()
	{
		parent::__construct();

		$this->require_logged_in();
		$this->require_permission(Permission::SETUP_SETTINGS);

		$this->tzlist = DateTimeZone::listIdentifiers(DateTimeZone::ALL);

		$this->load->config('features');
	}


	/**
	* Settings page
	*
	*/
	function index()
	{
		$this->data['feature_list'] = $this->config->item('features');
		$this->data['settings_features'] = $this->settings_model->get_all('features');
		$this->data['settings'] = $this->settings_model->get_all('crbs');
		$this->data['date_settings'] = $this->settings_model->get_all('dates');

		if ($this->input->post()) {
			$this->save_settings();
		}


		$zones = [];
		foreach ($this->tzlist as $tz) {
			$zones[$tz] = $tz;
		}
		$this->data['timezones'] = $zones;

		$this->data['date_pattern_options'] = $this->dates->date_pattern_options();
		$this->data['time_pattern_options'] = $this->dates->time_pattern_options();


		$this->data['title'] = lang('settings.settings');
		$this->data['showtitle'] = $this->data['title'];

		$body = $this->load->view('settings/general', $this->data, TRUE);

		$this->data['body'] = $body;

		return $this->render();
	}



	/**
	* Controller function to handle submitted form
	*
	*/
	private function save_settings()
	{
		// Parse data input from view and carry out appropriate action.

		// Load image manipulation library
		$this->load->library('image_lib');

		$this->load->library('form_validation');
		$this->form_validation->set_rules('displaytype', 'lang:settings.general.displaytype.label', 'required');
		$this->form_validation->set_rules('d_columns', 'lang:settings.general.columns.label', 'callback__valid_columns');
		$this->form_validation->set_rules('grid_highlight', 'lang:settings.general.grid_highlight.label', 'is_natural');
		$this->form_validation->set_rules('timezone', 'lang:settings.general.timezone.label', 'required');
		$this->form_validation->set_rules('login_message_enabled', 'lang:settings.general.login_message', 'is_natural');
		$this->form_validation->set_rules('login_message_text', 'lang:settings.general.login_message', 'max_length[1024]');
		$this->form_validation->set_rules('maintenance_mode', 'lang:settings.general.maintenance_mode', 'is_natural');
		$this->form_validation->set_rules('maintenance_mode_message', 'lang:settings.general.maintenance_mode', 'max_length[1024]');

		foreach ($this->data['feature_list'] as $feature_name) {
			$title = lang("features_{$feature_name}");
			$this->form_validation->set_rules($feature_name, $title, 'is_natural');
		}

		if ($this->form_validation->run() == FALSE) {
			return FALSE;
		}

		// General
		//

		$general = array(
			'colour' => '468ED8',
			'displaytype' => $this->input->post('displaytype'),
			'd_columns' => $this->input->post('d_columns'),
			'grid_highlight' => $this->input->post('grid_highlight'),
			'timezone' => $this->input->post('timezone'),
			'login_message_enabled' => $this->input->post('login_message_enabled'),
			'login_message_text' => $this->input->post('login_message_text'),
			'maintenance_mode' => $this->input->post('maintenance_mode'),
			'maintenance_mode_message' => $this->input->post('maintenance_mode_message'),
		);
		$this->settings_model->set($general);

		$dates = [
			'pattern_long' => $this->input->post('pattern_long'),
			'pattern_weekday' => $this->input->post('pattern_weekday'),
			'pattern_time' => $this->input->post('pattern_time'),
		];
		$this->settings_model->set($dates, 'dates');

		// Features
		//
		$feature_settings = [];
		foreach ($this->data['feature_list'] as $feature_name) {
			$feature_settings[$feature_name] = (int) $this->input->post($feature_name);
		}
		$this->settings_model->set($feature_settings, 'features');

		$this->session->set_flashdata('saved', msgbox('info', lang('settings.save.success')));

		redirect('settings/general');
	}


	function _valid_columns($cols)
	{
		// Day: Periods / Rooms
		// Room: Periods / Days
		$valid['day'] = array('periods', 'rooms');
		$valid['room'] = array('periods', 'days');

		$displaytype = $this->input->post('displaytype');

		switch ($displaytype) {

			case 'day':
				if (in_array($cols, $valid['day'])) {
					$ret = TRUE;
				} else {
					$ret = FALSE;
				}
			break;

			case 'room':
				if (in_array($cols, $valid['room'])) {
					$ret = TRUE;
				} else {
					$ret = FALSE;
				}
			break;
		}

		if ($ret == FALSE) {
			$this->form_validation->set_message('_valid_columns', lang('settings.general.columns.error'));
		}

		return $ret;
	}


}
