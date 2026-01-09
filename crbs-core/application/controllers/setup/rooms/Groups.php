<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Groups extends MY_Controller
{

	public $js = [
		'sortable',
	];

	public function __construct()
	{
		parent::__construct();

		$this->require_logged_in();
		$this->require_permission(Permission::SETUP_ROOMS);

		$this->load->model([
			'room_groups_model',
			'rooms_model',
		]);

		$this->data['showtitle'] = lang('room.rooms');
	}


	public function index()
	{
		$this->load->library('table');

		$this->data['groups'] = $this->room_groups_model->get_all();

		$this->data['title'] = lang('room.rooms');

		$this->data['active'] = 'setup/rooms/groups';

		$icons = $this->load->view('setup/rooms/_icons_primary', $this->data, true);
		$body = $this->load->view('setup/rooms/groups/index', $this->data, true);

		$this->data['body'] = $icons . $body;

		return $this->render();
	}


	public function save_pos()
	{
		$updates = [];
		$groups = $this->input->post('groups');
		foreach ($groups as $pos => $room_group_id) {
			$updates[] = [
				'room_group_id' => $room_group_id,
				'pos' => $pos,
			];
		}

		$this->room_groups_model->update_pos($updates);

		hx_toast('success', lang('room_group.save_order.success'));
		$this->output->set_status_header(204);
		return;
	}


	public function view($room_group_id)
	{
		$this->load->library('table');

		$this->data['group'] = $this->find_room_group($room_group_id);

		$this->data['rooms'] = $this->rooms_model->get_in_group($room_group_id);

		$title = sprintf('%s %s: %s', $this->data['group']->name, strtolower(lang('room_group.group')), lang('room.rooms'));
		$this->data['title'] = $this->data['showtitle'] = $title;

		$this->data['active'] = 'setup/rooms/groups/view/'.$room_group_id;

		$icons = $this->load->view('setup/rooms/_icons_primary', $this->data, true);
		$body = $this->load->view('setup/rooms/groups/view', $this->data, true);

		$this->data['body'] = $icons . $body;

		return $this->render();
	}


	public function add()
	{
		$this->data['title'] = $this->data['showtitle'] = lang('room_group.add.title');
		$this->data['groups'] = $this->room_groups_model->get_all();
		$this->data['rooms'] = $this->rooms_model->get_all_grouped();

		if ($this->input->post()) {
			$this->save_room_group();
		}

		$add = $this->load->view('setup/rooms/groups/add', $this->data, TRUE);
		$side = $this->load->view('setup/rooms/groups/add_side', $this->data, TRUE);

		$columns = [
			'c1' => ['content' => $add, 'width' => '70%'],
			'c2' => ['content' => $side, 'width' => '30%'],
		];

		$body = $this->load->view('columns', $columns, TRUE);

		$this->data['active'] = 'setup/rooms/groups';
		$icons = $this->load->view('setup/rooms/_icons_primary', $this->data, true);
		$this->data['body'] = $icons . $body;

		return $this->render();
	}


	public function edit($room_group_id)
	{
		$this->data['group'] = $this->find_room_group($room_group_id);

		$title = sprintf('%s %s: %s', $this->data['group']->name, strtolower(lang('room_group.group')), lang('room_group.edit.title'));
		$this->data['title'] = $this->data['showtitle'] = $title;

		$this->data['groups'] = $this->room_groups_model->get_all();
		$this->data['rooms'] = $this->rooms_model->get_all_grouped();

		if ($this->input->post()) {
			$this->save_room_group($room_group_id);
		}

		$edit = $this->load->view('setup/rooms/groups/add', $this->data, TRUE);
		$side = $this->load->view('setup/rooms/groups/add_side', $this->data, TRUE);

		$columns = [
			'c1' => ['content' => $edit, 'width' => '70%'],
			'c2' => ['content' => $side, 'width' => '30%'],
		];

		$body = $this->load->view('columns', $columns, TRUE);
		$this->data['active'] = $this->uri->uri_string();
		$icons = $this->load->view('setup/rooms/_icons_primary', $this->data, true);
		$this->data['body'] = $icons . $body;

		return $this->render();
	}


	/**
	 * Add or edit a group
	 *
	 */
	private function save_room_group($room_group_id = NULL)
	{
		$this->load->library('form_validation');

		$this->form_validation->set_rules('name', 'lang:room_group.field.name', 'required|max_length[32]');
		// $this->form_validation->set_rules('description', 'lang:room_group.field.description', "");
		$this->form_validation->set_rules('room_ids[]', 'lang:room.rooms', "integer");

		if ($this->form_validation->run() == FALSE) {
			return FALSE;
		}

		$data = array(
			'name' => $this->input->post('name'),
			'description' => $this->input->post('description'),
		);

		if ($room_group_id) {
			$uri = "setup/rooms/groups/edit/".$room_group_id;
			if ($this->room_groups_model->update($room_group_id, $data)) {
				$msg = sprintf(lang('room_group.update.success'), $data['name']);
				$flashmsg = msgbox('info', $msg);
			} else {
				$line = lang('room_group.update.error');
				$flashmsg = msgbox('error', $line);
			}
		} else {
			$uri = "setup/rooms/groups";
			if ($room_group_id = $this->room_groups_model->insert($data)) {
				$msg = sprintf(lang('room_group.create.success'), $data['name']);
				$flashmsg = msgbox('info', $msg);
			} else {
				$line = lang('room_group.create.error');
				$flashmsg = msgbox('error', $line);
			}
		}

		// Update room assignments
		$this->update_room_groups($room_group_id, $this->input->post('room_ids'));

		$this->session->set_flashdata('saved', $flashmsg);
		redirect($uri);
	}


	/**
	 * Add or remove room assignments that are done from add/edit page.
	 *
	 */
	private function update_room_groups($room_group_id, $room_ids)
	{
		// Get current rooms in this group
		$rooms = $this->rooms_model->get_in_group($room_group_id);
		if ( ! is_array($rooms)) $rooms = [];
		$current_room_ids = array_column($rooms, 'room_id');

		$updates = [];

		if (empty($room_ids)) return;

		// Check for additions and removals
		foreach ($room_ids as $room_id => $checked) {
			if ($checked == 1 && ! in_array($room_id, $current_room_ids)) {
				$updates[] = [
					'room_id' => $room_id,
					'room_group_id' => $room_group_id,
				];
			}
			if ($checked == 0 && in_array($room_id, $current_room_ids)) {
				$updates[] = [
					'room_id' => $room_id,
					'room_group_id' => null,
				];
			}
		}

		if (empty($updates)) return;

		return $this->rooms_model->update_batch($updates, 'room_id');
	}



	/**
	 * Delete a room group
	 *
	 */
	public function delete($id)
	{
		$group = $this->find_room_group($id);

		if ($this->input->post('id') == $id) {
			$this->room_groups_model->delete($this->input->post('id'));
			$msg = sprintf(lang('room_group.delete.success'), $group->name);
			$flashmsg = msgbox('info', $msg);
			$this->session->set_flashdata('saved', $flashmsg);
			redirect('setup/rooms/groups');
		}

		$this->data['action'] = current_url();
		$this->data['id'] = $id;
		$this->data['cancel'] = 'setup/rooms/groups';
		$this->data['text'] = lang('room_group.delete.warning');

		$this->data['title'] = $this->data['showtitle'] = sprintf(lang('room_group.delete.title'), $group->name);

		$this->data['active'] = 'setup/rooms/groups';

		$body = $this->load->view('partials/deleteconfirm', $this->data, TRUE);
		$icons = $this->load->view('setup/rooms/_icons_primary', $this->data, true);

		$this->data['body'] = $icons . $body;

		return $this->render();
	}


	/**
	 * Get and return a group by ID or show error page.
	 *
	 */
	private function find_room_group($room_group_id)
	{
		if (empty($room_group_id)) {
			show_404();
		}

		$group = $this->room_groups_model->get($room_group_id);

		if (empty($group)) {
			show_404();
		}

		return $group;
	}


}
