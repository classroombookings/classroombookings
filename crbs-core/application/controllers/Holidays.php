<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Holidays extends MY_Controller
{


	public function __construct()
	{
		parent::__construct();

		$this->require_logged_in();
		$this->require_permission(Permission::SETUP_SESSIONS);

		$this->load->model([
			'sessions_model',
			'crud_model',
			'weeks_model',
			'holidays_model',
		]);

		$this->data['showtitle'] = lang('holiday.holidays');
	}


	public function session($session_id)
	{
		$session = $this->find_session($session_id);

		$this->data['session'] = $session;
		$this->data['holidays'] = $this->holidays_model->get_by_session($session->session_id);
		$this->data['title'] = $this->data['showtitle'] = sprintf('%s: %s: %s', lang('session.session'), $session->name, lang('holiday.holidays'));

		$icons = $this->load->view('sessions/_icons', [
			'session' => $session,
			'active' => 'holidays/session/' . $session->session_id,
		], TRUE);

		$body = $this->load->view('holidays/index', $this->data, TRUE);

		$this->data['body'] = $icons . $body;

		return $this->render();
	}




	/**
	 * Add a new holiday
	 *
	 */
	function add()
	{
		$session_id = $this->input->get_post('session_id');
		$session = $this->find_session($session_id);

		$this->data['session'] = $session;
		$this->data['title'] = $this->data['showtitle'] = sprintf('%s: %s: %s', lang('session.session'), $session->name, lang('holiday.add.title'));

		if ($this->input->post()) {
			$this->save_holiday();
		}

		$add = $this->load->view('holidays/add', $this->data, TRUE);
		$side = $this->load->view('holidays/add_side', $this->data, TRUE);

		$columns = [
			'c1' => ['content' => $add, 'width' => '70%'],
			'c2' => ['content' => $side, 'width' => '30%'],
		];
		$body = $this->load->view('columns', $columns, TRUE);

		$icons = $this->load->view('sessions/_icons', [
			'session' => $session,
			'active' => 'holidays/session/' . $session->session_id,
		], TRUE);

		$this->data['body'] = $icons . $body;

		return $this->render();
	}




	/**
	 * Edit a holiday
	 *
	 */
	function edit($id)
	{
		$this->data['holiday'] = $this->find_holiday($id);

		$session = $this->sessions_model->get($this->data['holiday']->session_id);

		$this->data['session'] = $session;
		$this->data['title'] = $this->data['showtitle'] = sprintf('%s: %s: %s', lang('session.session'), $session->name, lang('holiday.edit.title'));

		if ($this->input->post()) {
			$this->save_holiday($this->data['holiday']->holiday_id);
		}

		$add = $this->load->view('holidays/add', $this->data, TRUE);
		$side = $this->load->view('holidays/add_side', $this->data, TRUE);

		$columns = [
			'c1' => ['content' => $add, 'width' => '70%'],
			'c2' => ['content' => $side, 'width' => '30%'],
		];
		$body = $this->load->view('columns', $columns, TRUE);

		$icons = $this->load->view('sessions/_icons', [
			'session' => $session,
			'active' => 'holidays/session/' . $session->session_id,
		], TRUE);

		$this->data['body'] = $icons . $body;

		return $this->render();
	}



	/**
	 * Add or edit a holiday
	 *
	 */
	private function save_holiday($holiday_id = NULL)
	{
		$this->load->library('form_validation');

		$this->form_validation->set_rules('name', 'lang:holiday.field.name', 'required|max_length[50]');

		$callbackRule = sprintf('callback__date_check[%d]', $this->data['session']->session_id);
		$this->form_validation->set_rules('date_start', 'lang:holiday.field.date_start', "required|{$callbackRule}");
		$this->form_validation->set_rules('date_end', 'lang:holiday.field.date_end', "required|{$callbackRule}");

		$data = array(
			'session_id' => $this->data['session']->session_id,
			'name' => $this->input->post('name'),
			'date_start' => $this->input->post('date_start'),
			'date_end' => $this->input->post('date_end'),
		);

		if ($this->form_validation->run() == FALSE) {
			return FALSE;
		}

		$uri = 'holidays/session/' . $this->data['session']->session_id;

		if ($holiday_id) {
			if ($this->holidays_model->update($holiday_id, $data)) {
				$msg = sprintf(lang('holiday.update.success'), $data['name']);
				$flashmsg = msgbox('info', $msg);
			} else {
				$flashmsg = msgbox('error', lang('holiday.update.error'));
			}
		} else {
			if ($holiday_id = $this->holidays_model->insert($data)) {
				$msg = sprintf(lang('holiday.create.success'), $data['name']);
				$flashmsg = msgbox('info', $msg);
			} else {
				$flashmsg = msgbox('error', lang('holiday.create.error'));
			}
		}

		$this->session->set_flashdata('saved', $flashmsg);
		redirect($uri);
	}



	/**
	 * Delete a holiday
	 *
	 */
	function delete($id)
	{
		$holiday = $this->find_holiday($id);

		$session = $this->sessions_model->get($holiday->session_id);

		if ($this->input->post('id')) {
			$this->holidays_model->delete($this->input->post('id'));
			$flashmsg = msgbox('info', $this->lang->line('holiday.delete.success'));
			$this->session->set_flashdata('saved', $flashmsg);
			redirect('holidays/session/' . $session->session_id);
		}

		$this->data['action'] = current_url();
		$this->data['id'] = $id;
		$this->data['cancel'] = 'holidays/session/' . $session->session_id;
		$this->data['text'] = lang('holiday.delete.warning');

		$this->data['session'] = $session;
		$this->data['title'] = $this->data['showtitle'] = sprintf('%s: %s: %s', lang('session.session'), $session->name, sprintf(lang('holiday.delete.title'), $holiday->name));

		$title = "<h2>" . html_escape(sprintf(lang('holiday.delete.title'), $holiday->name)) . "</h2>";
		$body = $this->load->view('partials/deleteconfirm', $this->data, TRUE);

		$icons = $this->load->view('sessions/_icons', [
			'session' => $session,
			'active' => 'holidays/session/' . $session->session_id,
		], TRUE);

		$this->data['body'] = $icons . $title . $body;

		return $this->render();
	}



	/**
	 * Validation: Ensure the date is valid and in range of the session
	 *
	 */
	public function _date_check($value, $session_id = NULL)
	{
		$rule = '_date_check';
		$session = $this->sessions_model->get($session_id);

		if ( ! $session) {
			$this->form_validation->set_message($rule, lang('holiday.validation.session_error'));
			return FALSE;
		}

		$dt = datetime_from_string($value);
		if ( ! $dt) {
			$msg = sprintf(lang('holiday.validation.date_parse_error'), $value);
			$this->form_validation->set_message($rule, $msg);
			return FALSE;
		}

		if ($dt < $session->date_start || $dt > $session->date_end) {
			$start_fmt = $session->date_start->format('d/m/Y');
			$end_fmt = $session->date_end->format('d/m/Y');
			$msg = sprintf(lang('holiday.validation.date_range_error'), $start_fmt, $end_fmt);
			$this->form_validation->set_message($rule, $msg);
			return FALSE;
		}

		return TRUE;
	}



	/**
	 * Get and return a holiday by ID or show error page.
	 *
	 */
	private function find_holiday($holiday_id)
	{
		$holiday = $this->holidays_model->get($holiday_id);

		if (empty($holiday)) {
			show_404();
		}

		return $holiday;
	}



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
