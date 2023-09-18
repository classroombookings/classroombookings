<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use app\components\Calendar;


class Periods extends MY_Controller
{


	public function __construct()
	{
		parent::__construct();

		$this->require_logged_in();
		$this->require_auth_level(ADMINISTRATOR);

		$this->load->model([
			'schedules_model',
			'periods_model',
		]);
	}


	public function index($schedule_id)
	{
		$this->data['schedule'] = $this->schedules_model->get($schedule_id);
		$this->data['periods'] = $this->periods_model->get_by_schedule($schedule_id);
		$this->data['days'] = Calendar::get_day_names('short');

		$this->load->view('periods/index', $this->data);
	}


	public function edit($schedule_id, $period_id)
	{
		$this->output->set_output($this->render_edit($schedule_id, $period_id));
		return;
	}


	public function view($schedule_id, $period_id)
	{
		$this->output->set_output($this->render_view($schedule_id, $period_id));
		return;
	}


	public function save($schedule_id, $period_id = NULL)
	{
		$action = (is_null($period_id)) ? 'insert' : 'update';

		$this->load->library('form_validation');

		$this->form_validation->set_rules($this->get_validation_rules());

		if ($this->form_validation->run() == FALSE) {
			hx_toast('error', 'Please check the fields and try again');
			$view = $this->render_edit($schedule_id, $period_id);
			$this->output->set_output($view);
			return;
		}

		$data = $this->input->post();
		$data['schedule_id'] = $schedule_id;

		$period_data = $this->period_from_values($data);

		if ($action == 'insert') {
			$message = sprintf("'%s' has been added to the schedule.", html_escape($period_data['name']));
			$period_id = $this->periods_model->insert($period_data);
		} else {
			$message = sprintf("'%s' has been updated.", html_escape($period_data['name']));
			$this->periods_model->update($period_id, $period_data);
		}

		// Clear old data before re-rendering
		$this->form_validation->reset_validation();
		// Unset the fields likely to change for each period - keeping the others.
		unset($_POST['name']);
		unset($_POST['time_start']);
		unset($_POST['time_end']);

		hx_toast('success', $message);

		$out = '';

		// Render the 'view' row for this new period
		$out .= $this->render_view($schedule_id, $period_id);

		// Render an editable row for adding another new period.
		if ($action == 'insert') {
			$out .= $this->render_edit($schedule_id, NULL);
		}

		$this->output->set_output($out);
	}


	public function delete($schedule_id, $period_id)
	{
		if ($this->input->method() === 'post') {
			$this->periods_model->delete($period_id);
			hx_toast('success', 'The period has been deleted from the schedule.');
			return '';
		}
	}


	private function render_view($schedule_id, $period_id)
	{
		$data = [
			'schedule' => $this->schedules_model->get($schedule_id),
			'period' => $this->periods_model->get($period_id),
			'days' => Calendar::get_day_names('short'),
		];

		return $this->load->view('periods/item_view', $data, TRUE);
	}


	private function render_edit($schedule_id, $period_id = NULL)
	{
		$data = [
			'schedule' => $this->schedules_model->get($schedule_id),
			'period' => (is_null($period_id)) ? NULL : $this->periods_model->get($period_id),
			'days' => Calendar::get_day_names('short'),
		];

		return $this->load->view('periods/item_add_edit', $data, TRUE);
	}


	/**
	 * Get the validation rules for saving a period.
	 *
	 */
	private function get_validation_rules()
	{
		$rules = [
			['field' => 'schedule_id', 'label' => 'Schedule', 'rules' => 'required|integer'],
			['field' => 'name', 'label' => 'Name', 'rules' => 'required|min_length[1]|max_length[30]'],
			['field' => 'time_start', 'label' => 'Start time', 'rules' => 'required|min_length[4]|max_length[5]|valid_time'],
			['field' => 'time_end', 'label' => 'End time', 'rules' => 'required|min_length[4]|max_length[5]|valid_time|time_is_after[time_start]'],
			['field' => 'bookable', 'label' => 'Bookable', 'rules' => 'required|in_list[0,1]'],
		];

		foreach (Calendar::get_day_names() as $day_num => $label) {
			$rules[] = ['field' => "day_{$day_num}", 'label' => $label, 'rules' => 'required|in_list[0,1]'];
		}

		return $rules;
	}


	/**
	 * Convert post data values into an insertable or updateable array of values.
	 *
	 */
	private function period_from_values($data)
	{
		$period = [
			'schedule_id' => (int) $data['schedule_id'],
			'bookable' => $data['bookable'] == 1 ? 1 : 0,
			'name' => $data['name'],
			'time_start' => date('H:i:00', strtotime($data['time_start'])),
			'time_end' => date('H:i:00', strtotime($data['time_end'])),
			'day_1' => $data['day_1'] == 1 ? 1 : 0,
			'day_2' => $data['day_2'] == 1 ? 1 : 0,
			'day_3' => $data['day_3'] == 1 ? 1 : 0,
			'day_4' => $data['day_4'] == 1 ? 1 : 0,
			'day_5' => $data['day_5'] == 1 ? 1 : 0,
			'day_6' => $data['day_6'] == 1 ? 1 : 0,
			'day_7' => $data['day_7'] == 1 ? 1 : 0,
		];

		return $period;
	}


}
