<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Room_schedules extends MY_Controller
{


	public function __construct()
	{
		parent::__construct();

		$this->require_logged_in();
		$this->require_auth_level(ADMINISTRATOR);

		$this->load->model([
			'schedules_model',
			'sessions_model',
			'room_groups_model',
			'session_schedules_model',
		]);

		$this->data['showtitle'] = 'Room Schedules';
	}


	public function session($session_id)
	{
		$session = $this->find_session($session_id);

		$this->data['session'] = $session;
		$this->data['schedules'] = $this->schedules_model->get_all();
		$this->data['room_groups'] = $this->room_groups_model->get_all();

		$session_schedules = $this->session_schedules_model->get_by_session($session->session_id);
		$session_schedules_flat = $this->session_schedules_model->flatten_results($session_schedules);

		$this->data['session_schedules'] = $session_schedules_flat;
		$this->data['title'] = $this->data['showtitle'] = 'Session: ' . $session->name . ': Room Schedules';

		$icons = $this->load->view('sessions/_icons', [
			'session' => $session,
			'active' => 'room_schedules/session/' . $session->session_id,
		], TRUE);

		if (empty($this->data['room_groups'])) {
			$msg = "No room groups have been created yet.";
			$body = "<p class='msgbox exclamation'>{$msg}</p>";
		} else {
			$body = $this->load->view('room_schedules/index', $this->data, TRUE);
		}

		$main = $this->load->view('room_schedules/index', $this->data, TRUE);
		$side = $this->load->view('room_schedules/index_side', $this->data, TRUE);

		$columns = [
			'c1' => ['content' => $main, 'width' => '70%'],
			'c2' => ['content' => $side, 'width' => '30%'],
		];

		$body = $this->load->view('columns', $columns, TRUE);

		$this->data['body'] = $icons . $body;

		return $this->render();
	}


	public function save($session_id)
	{
		$this->load->library('form_validation');

		$this->form_validation->set_rules($this->get_validation_rules());

		if ($this->form_validation->run() == FALSE) {
			return $this->session($session_id);
		}

		$defaults = ['session_id' => $session_id];
		$data = $this->input->post('group_schedule');

		$this->session_schedules_model->update($defaults, $data);

		$flashmsg = msgbox('info', "The room groups schedules have been updated.");
		$this->session->set_flashdata('saved', $flashmsg);
		redirect('room_schedules/session/' . $session_id);
	}


	public function get_validation_rules()
	{
		$rules = [];

		$groups = $this->room_groups_model->get_all();
		if (empty($groups)) return [];

		foreach ($groups as $group) {
			$label = html_escape($group->name);
			$field_name = sprintf('group_schedule[%d][schedule_id]', $group->room_group_id);
			$rule = [
				'field' => $field_name,
				'label' => $label,
				'rules' => 'required|integer',
			];
			$rules[] = $rule;
		}

		return $rules;
	}



	/**
	 * Get and return a holiday by ID or show error page.
	 *
	 */
	/*private function find_holiday($holiday_id)
	{
		$holiday = $this->holidays_model->get($holiday_id);

		if (empty($holiday)) {
			show_404();
		}

		return $holiday;
	}*/



	/**
	 * Get and return a session by ID or show error page.
	 *
	 */
	private function find_session($session_id)
	{
		$session = $this->sessions_model->get($session_id);

		if (empty($session)) {
			show_404();
		}

		return $session;
	}



}
