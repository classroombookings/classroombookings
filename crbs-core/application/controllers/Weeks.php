<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Weeks extends MY_Controller
{


	public $WeeksCount = 0;


	public function __construct()
	{
		parent::__construct();

		$this->require_logged_in();
		$this->require_auth_level(ADMINISTRATOR);

		$this->load->model('crud_model');
		$this->load->model('holidays_model');
		$this->load->model('weeks_model');
		$this->load->helper('week');
	}


	public function index()
	{
		$this->data['weeks'] = $this->weeks_model->get_all();
		$this->data['title'] = $this->data['showtitle'] = 'Timetable Weeks';

		$body = $this->load->view('weeks/index', $this->data, TRUE);

		$this->data['body'] = $body;

		return $this->render();
	}


	/**
	 * Add new week
	 *
	 */
	public function add()
	{
		$this->data['title'] = $this->data['showtitle'] = 'Add Timetable Week';

		if ($this->input->post()) {
			$this->save_week();
		}

		$add = $this->load->view('weeks/add', $this->data, TRUE);

		$columns = [
			'c1' => ['content' => $add, 'width' => '70%'],
		];
		$body = $this->load->view('columns', $columns, TRUE);
		$this->data['body'] = $body;

		return $this->render();
	}



	/**
	 * Edit week
	 *
	 */
	function edit($id)
	{
		$this->data['week'] = $this->find_week($id);

		$this->data['title'] = $this->data['showtitle'] = 'Edit Week';

		if ($this->input->post()) {
			$this->save_week($this->data['week']->week_id);
		}

		$add = $this->load->view('weeks/add', $this->data, TRUE);

		$columns = [
			'c1' => ['content' => $add, 'width' => '70%'],
		];
		$body = $this->load->view('columns', $columns, TRUE);
		$this->data['body'] = $body;

		return $this->render();
	}



	/**
	 * Get and return a week by ID or show error page.
	 *
	 */
	private function find_week($week_id)
	{
		$week = $this->weeks_model->get($week_id);

		if (empty($week)) {
			show_404();
		}

		return $week;
	}


	/**
	 * Add or edit a week
	 *
	 */
	private function save_week($week_id = NULL)
	{
		$this->load->library('form_validation');

		$this->form_validation->set_rules('name', 'Name', 'required|max_length[20]');
		$this->form_validation->set_rules('bgcol', 'Colour', "required|min_length[6]|max_length[7]");

		$data = array(
			'name' => $this->input->post('name'),
			'bgcol' => $this->input->post('bgcol'),
		);

		if ($this->form_validation->run() == FALSE) {
			return FALSE;
		}

		if ($week_id) {
			if ($this->weeks_model->update($week_id, $data)) {
				$line = sprintf($this->lang->line('crbs_action_saved'), $data['name']);
				$flashmsg = msgbox('info', $line);
			} else {
				$line = sprintf($this->lang->line('crbs_action_dberror'), 'editing');
				$flashmsg = msgbox('error', $line);
			}
		} else {
			if ($week_id = $this->weeks_model->insert($data)) {
				$line = sprintf($this->lang->line('crbs_action_added'), 'Session');
				$flashmsg = msgbox('info', $line);
			} else {
				$line = sprintf($this->lang->line('crbs_action_dberror'), 'adding');
				$flashmsg = msgbox('error', $line);
			}
		}

		$this->session->set_flashdata('saved', $flashmsg);
		redirect('weeks');
	}


	/**
	 * Delete a week
	 *
	 */
	function delete($id)
	{
		$week = $this->find_week($id);

		$this->data['title'] = $this->data['showtitle'] = 'Timetable Weeks';

		if ($this->input->post('id')) {
			$this->weeks_model->delete($this->input->post('id'));
			$flashmsg = msgbox('info', $this->lang->line('crbs_action_deleted'));
			$this->session->set_flashdata('saved', $flashmsg);
			redirect('weeks');
		}

		$this->data['action'] = current_url();
		$this->data['id'] = $id;
		$this->data['cancel'] = 'weeks';
		$this->data['text'] = 'If you delete this week, <strong>all reurring bookings</strong> that take place on this week will be permanently deleted.';

		$this->data['title'] = sprintf('Delete %s', html_escape($week->name));

		$title = "<h2>{$this->data['title']}</h2>";
		$body = $this->load->view('partials/deleteconfirm', $this->data, TRUE);

		$this->data['body'] = $title . $body;

		return $this->render();
	}




}
