<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Export extends MY_Controller
{


	public function __construct()
	{
		parent::__construct();

		$this->require_logged_in();
		$this->require_permission(Permission::SYS_EXPORT_BOOKINGS);

		$this->load->model([
			'bookings_model',
			'sessions_model',
			'room_groups_model',
		]);

		$this->data['showtitle'] = lang('export.title_long');
	}


	public function index()
	{
		$this->data['title'] = lang('export.export');

		$active = $this->sessions_model->get_all_active();
		$past = $this->sessions_model->get_all_past();

		$this->data['session_options'] = [
			'All' => ['' => sprintf('(%s)', lang('app.all')) ],
			'Active' => results_to_assoc($active, 'session_id', 'name'),
			'Past' => results_to_assoc($past, 'session_id', 'name'),
		];

		$room_groups = $this->room_groups_model->get_all();
		$this->data['room_group_options'] = results_to_assoc($room_groups, 'room_group_id', 'name', sprintf('(%s)', lang('app.all')));

		if ($this->input->post()) {
			$this->process_export();
		}

		$body = $this->load->view('export/index', $this->data, TRUE);

		$this->data['body'] = $body;

		return $this->render();
	}


	private function process_export()
	{
		$filter = [
			'session_id' => $this->input->post('session_id'),
			'room_group_id' => $this->input->post('room_group_id'),
			'include_cancelled' => $this->input->post('include_cancelled'),
		];

		$filename = sprintf('classroombookings-%s-%s.csv', strtolower(lang('export.export')), date('Y-m-d_His'));

		$f = fopen('php://output', 'w');

		header('Content-Type: application/csv; charset=UTF-8');
		header('Content-Disposition: attachment; filename="'.$filename.'";');
		header('X-Accel-Buffering: no');
		echo "\xEF\xBB\xBF"; // UTF-8 BOM

		try {

			$this->bookings_model->export_unbuffered($filter, function($row) use ($f) {
				$this->write_csv_row($f, $row);
			});

		} catch (Exception $e) {
			header_remove('Content-Type');
			header_remove('Content-Disposition');
			header_remove('X-Accel-Buffering');
			show_error($e->getMessage());
		}

		exit;
	}


	private function write_csv_row($handle, $row, $delim = ",", $newline = "\n", $enclosure = '"')
	{
		$out = '';
		foreach ($row as $item) {
			$out .= $enclosure.str_replace($enclosure, $enclosure.$enclosure, ($item ?? '')).$enclosure.$delim;
		}
		$out = rtrim($out);
		$out .= $newline;

		fwrite($handle, $out);
	}


}
