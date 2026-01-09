<?php

defined('BASEPATH') OR exit('No direct script access allowed');

use app\components\Calendar;


class Schedules extends MY_Controller
{


	public function __construct()
	{
		parent::__construct();

		$this->require_logged_in();
		$this->require_permission(Permission::SETUP_SCHEDULES);

		$this->load->model([
			'schedules_model',
		]);

		$this->data['showtitle'] = lang('schedule.schedules');
	}


	private function get_icons()
	{
		$items = [
			['schedules', lang('schedule.schedules'), 'school_manage_times.png'],
		];

		return $items;
	}


	public function index()
	{
		$this->load->library('table');
		$this->data['schedules'] = $this->schedules_model->get_all();
		$this->data['title'] = lang('schedule.schedules');

		$icons = iconbar($this->get_icons(), 'schedules');
		$body = $this->load->view('schedules/index', $this->data, TRUE);

		$this->data['body'] = $icons . $body;

		return $this->render();
	}


	public function add()
	{
		$this->data['title'] = lang('schedule.add.title');
		$this->data['showtitle'] = $this->data['title'];

		if ($this->input->post()) {
			$this->save();
		}

		$icons = iconbar($this->get_icons(), 'schedules');
		$body = $this->load->view('schedules/add', $this->data, TRUE);

		$this->data['body'] = $icons . $body;

		return $this->render();
	}


	public function edit($schedule_id)
	{
		$this->data['title'] = lang('schedule.edit.title');
		$this->data['showtitle'] = $this->data['title'];

		$this->data['schedule'] = $this->find_schedule($schedule_id);

		if ($this->input->post()) {
			$this->save($schedule_id);
		}

		$icons = iconbar($this->get_icons(), 'schedules');
		$body = $this->load->view('schedules/add', $this->data, TRUE);

		$this->data['body'] = $icons . $body;

		return $this->render();
	}


	private function save($schedule_id = NULL)
	{
		$this->load->library('form_validation');

		$this->form_validation->set_rules('name', 'lang:schedule.field.name', 'required|max_length[32]');
		$this->form_validation->set_rules('description', 'lang:schedule.field.description', "trim");

		$data = array(
			'name' => $this->input->post('name'),
			'description' => $this->input->post('description'),
		);

		if ($this->form_validation->run() == FALSE) {
			return FALSE;
		}

		if ( ! is_null($schedule_id)) {
			if ($this->schedules_model->update($schedule_id, $data)) {
				$msg = sprintf(lang('schedule.update.success'), $data['name']);
				$flashmsg = msgbox('info', $msg);
			} else {
				$flashmsg = msgbox('error', lang('schedule.update.error'));
				$this->session->set_flashdata('saved', $flashmsg);
				return FALSE;
			}
		} else {
			if ($schedule_id = $this->schedules_model->insert($data)) {
				$msg = sprintf(lang('schedule.create.success'), $data['name']);
				$flashmsg = msgbox('info', $msg);
			} else {
				$flashmsg = msgbox('error', lang('schedule.create.error'));
				$this->session->set_flashdata('saved', $flashmsg);
				return FALSE;
			}
		}

		$this->session->set_flashdata('saved', $flashmsg);
		redirect('schedules/edit/' . $schedule_id);
	}


	/**
	 * Delete a schedule
	 *
	 */
	public function delete($id)
	{
		$schedule = $this->find_schedule($id);

		if ($this->input->post('id')) {
			$this->schedules_model->delete($this->input->post('id'));
			$flashmsg = msgbox('info', sprintf(lang('schedule.delete.success'), $schedule->name));
			$this->session->set_flashdata('saved', $flashmsg);
			redirect('schedules');
		}

		$this->data['action'] = current_url();
		$this->data['id'] = $id;
		$this->data['cancel'] = 'schedules';
		$this->data['text'] = lang('schedule.delete.warning');

		$this->data['title'] = sprintf(lang('schedule.delete.title'), html_escape($schedule->name));

		$title = "<h2>{$this->data['title']}</h2>";
		$body = $this->load->view('partials/deleteconfirm', $this->data, TRUE);

		$this->data['body'] = $title . $body;

		return $this->render();
	}


	/**
	 * Get and return a group by ID or show error page.
	 *
	 */
	private function find_schedule($schedule_id)
	{
		if (empty($schedule_id)) {
			show_404();
		}

		$schedule = $this->schedules_model->get($schedule_id);

		if (empty($schedule)) {
			show_404();
		}

		return $schedule;
	}


}
