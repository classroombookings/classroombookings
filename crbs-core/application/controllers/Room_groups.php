<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Room_groups extends MY_Controller
{


	public function __construct()
	{
		parent::__construct();

		$this->require_auth_level(ADMINISTRATOR);

		if ( ! feature('room_groups')) show_404();

		$this->load->model([
			'room_groups_model',
			'rooms_model',
		]);

		$this->data['showtitle'] = 'Rooms';

		$this->data['rooms_icons'] = [
			['rooms', 'Rooms', 'school_manage_rooms.png'],
			feature('room_groups')
			 	? ['room_groups', 'Groups', 'folder.png']
			 	: null
			 	,
			['rooms/fields', 'Custom Fields', 'room_fields.png'],
			['access_control', 'Access Control', 'key.png'],
		];
	}


	public function index()
	{
		$this->load->library('table');

		$this->data['groups'] = $this->room_groups_model->get_all();

		$this->data['title'] = 'Room Groups';

		$icons = iconbar($this->data['rooms_icons'], 'room_groups');
		$body = $this->load->view('room_groups/index', $this->data, TRUE);

		$this->data['body'] = $icons . $body;

		return $this->render();
	}


	public function save_pos()
	{
		$this->data['message'] = msgbox('info', 'The group order has been saved.');

		$updates = [];
		$groups = $this->input->post('groups');
		foreach ($groups as $pos => $room_group_id) {
			$updates[] = [
				'room_group_id' => $room_group_id,
				'pos' => $pos,
			];
		}

		$this->room_groups_model->update_pos($updates);

		return $this->index();
	}


	public function add()
	{
		$this->data['title'] = 'Add Room Group';
		$title = "<h2>{$this->data['title']}</h2>";

		$this->data['groups'] = $this->room_groups_model->get_all();
		$this->data['rooms'] = $this->rooms_model->get_all_grouped();

		if ($this->input->post()) {
			$this->save_room_group();
		}

		$add = $this->load->view('room_groups/add', $this->data, TRUE);
		$side = $this->load->view('room_groups/add_side', $this->data, TRUE);

		$columns = [
			'c1' => ['content' => $add, 'width' => '70%'],
			'c2' => ['content' => $side, 'width' => '30%'],
		];

		$body = $this->load->view('columns', $columns, TRUE);

		$icons = iconbar($this->data['rooms_icons'], 'room_groups');
		$this->data['body'] = $icons . $title . $body;

		return $this->render();
	}


	public function edit($room_group_id)
	{
		$this->data['title'] = 'Edit Room Group';
		$title = "<h2>{$this->data['title']}</h2>";

		$this->data['group'] = $this->find_room_group($room_group_id);
		$this->data['groups'] = $this->room_groups_model->get_all();
		$this->data['rooms'] = $this->rooms_model->get_all_grouped();

		if ($this->input->post()) {
			$this->save_room_group($room_group_id);
		}

		$edit = $this->load->view('room_groups/add', $this->data, TRUE);
		$side = $this->load->view('room_groups/add_side', $this->data, TRUE);

		$columns = [
			'c1' => ['content' => $edit, 'width' => '70%'],
			'c2' => ['content' => $side, 'width' => '30%'],
		];

		$body = $this->load->view('columns', $columns, TRUE);

		$icons = iconbar($this->data['rooms_icons'], 'room_groups');
		$this->data['body'] = $icons . $title . $body;

		return $this->render();
	}


	/**
	 * Add or edit a group
	 *
	 */
	private function save_room_group($room_group_id = NULL)
	{
		$this->load->library('form_validation');

		$this->form_validation->set_rules('name', 'Name', 'required|max_length[32]');
		$this->form_validation->set_rules('description', 'Description', "");
		$this->form_validation->set_rules('room_ids', 'Room IDs', "");

		$data = array(
			'name' => $this->input->post('name'),
			'description' => $this->input->post('description'),
		);

		if ($this->form_validation->run() == FALSE) {
			return FALSE;
		}

		$uri = "room_groups";

		if ($room_group_id) {
			if ($this->room_groups_model->update($room_group_id, $data)) {
				$line = sprintf($this->lang->line('crbs_action_saved'), $data['name']);
				$flashmsg = msgbox('info', $line);
			} else {
				$line = sprintf($this->lang->line('crbs_action_dberror'), 'editing');
				$flashmsg = msgbox('error', $line);
			}
		} else {
			if ($room_group_id = $this->room_groups_model->insert($data)) {
				$line = sprintf($this->lang->line('crbs_action_added'), 'Room Group');
				$flashmsg = msgbox('info', $line);
			} else {
				$line = sprintf($this->lang->line('crbs_action_dberror'), 'adding');
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
	 * Delete a session
	 *
	 */
	public function delete($id)
	{
		$group = $this->find_room_group($id);

		if ($this->input->post('id')) {
			$this->room_groups_model->delete($this->input->post('id'));
			$flashmsg = msgbox('info', $this->lang->line('crbs_action_deleted'));
			$this->session->set_flashdata('saved', $flashmsg);
			redirect('room_groups');
		}

		$this->data['action'] = current_url();
		$this->data['id'] = $id;
		$this->data['cancel'] = 'room_groups';
		$this->data['text'] = 'Any rooms in this group will be kept, but will not belong to any group.';

		$this->data['title'] = sprintf('Delete room group (%s)', html_escape($group->name));

		$title = "<h2>{$this->data['title']}</h2>";
		$body = $this->load->view('partials/deleteconfirm', $this->data, TRUE);
		$icons = iconbar($this->data['rooms_icons'], 'room_groups');

		$this->data['body'] = $icons . $title . $body;

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
