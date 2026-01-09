<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rooms extends MY_Controller
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
			'crud_model',
			'rooms_model',
			'users_model',
			'room_groups_model',
		]);

		$this->load->helper('number');

		$this->data['max_size_bytes'] = max_upload_file_size();
		$this->data['max_size_human'] = byte_format(max_upload_file_size());
	}




	public function save_pos()
	{
		$updates = [];
		$rooms = $this->input->post('rooms');
		foreach ($rooms as $pos => $room_id) {
			$updates[] = [
				'room_id' => $room_id,
				'pos' => $pos,
			];
		}

		$this->rooms_model->update_pos($updates);

		hx_toast('success', lang('room.save_order.success'));
		$this->output->set_status_header(204);
		return;
	}



	/**
	 * Add a new room
	 *
	 */
	public function add($room_group_id = null)
	{
		$this->data['room_group'] = $this->find_room_group($room_group_id);

		$this->data['users'] = $this->users_model->Get(NULL, NULL, NULL);
		$this->data['fields'] = $this->rooms_model->GetFields();
		$this->data['fieldvalues'] = array();

		$group_name = html_escape($this->data['room_group']->name);
		$this->data['title'] = $this->data['showtitle'] = sprintf(lang('room.add.title'), $group_name);

		$this->data['groups'] = $this->room_groups_model->get_all();
		$this->data['group_id'] = $room_group_id;

		if ($this->input->post()) {
			$this->save_room();
		}

		$columns = array(
			'c1' => array(
				'content' => $this->load->view('setup/rooms/rooms/rooms_add', $this->data, TRUE),
				'width' => '70%',
			),
			'c2' => array(
				'content' => $this->load->view('setup/rooms/rooms/rooms_add_side', $this->data, TRUE),
				'width' => '30%',
			),
		);

		$columns = $this->load->view('columns', $columns, TRUE);

		$icons = $this->load->view('setup/rooms/_icons_rooms', $this->data, true);

		$this->data['body'] = $icons . $columns;

		return $this->render();
	}



	/**
	 * Edit a room
	 *
	 */
	public function edit($id = NULL)
	{
		$this->data['room'] = $this->rooms_model->get_by_id($id);

		if (empty($this->data['room'])) {
			show_404();
		}

		$this->data['groups'] = $this->room_groups_model->get_all();

		$this->data['room_group'] = $this->find_room_group($this->data['room']->room_group_id);
		$this->data['group_id'] = $this->data['room_group']->room_group_id;

		$this->data['users'] = $this->users_model->Get(NULL, NULL, NULL);
		$this->data['fields'] = $this->rooms_model->GetFields();
		$this->data['fieldvalues'] = $this->rooms_model->GetFieldValues($id);

		$this->data['title'] = $this->data['showtitle'] = sprintf('%s: %s', $this->data['room_group']->name, $this->data['room']->name);

		if ($this->input->post()) {
			$this->save_room();
		}

		$columns = array(
			'c1' => array(
				'content' => $this->load->view('setup/rooms/rooms/rooms_add', $this->data, TRUE),
				'width' => '70%',
			),
			'c2' => array(
				'content' => $this->load->view('setup/rooms/rooms/rooms_add_side', $this->data, TRUE),
				'width' => '30%',
			),
		);

		$this->data['active'] = $this->uri->uri_string();
		$icons = $this->load->view('setup/rooms/_icons_rooms', $this->data, true);

		$columns = $this->load->view('columns', $columns, TRUE);

		$this->data['body'] = $icons . $columns;

		return $this->render();
	}


	/**
	 * Save
	 *
	 */
	private function save_room()
	{
		$room_id = $this->input->post('room_id');

		$this->load->library('form_validation');

		$this->form_validation->set_rules('room_id', 'ID', 'integer');
		$this->form_validation->set_rules('room_group_id', 'lang:room_group.group', 'required|integer');
		$this->form_validation->set_rules('name', 'lang:room.field.name', 'required|min_length[1]|max_length[20]');
		$this->form_validation->set_rules('user_id', 'lang:room.field.user_id', 'integer');
		$this->form_validation->set_rules('location', 'lang:room.field.location', 'max_length[40]');
		$this->form_validation->set_rules('notes', 'lang:room.field.notes', 'max_length[255]');
		$this->form_validation->set_rules('bookable', 'lang:room.field.bookable', 'integer');

		if ($this->form_validation->run() == FALSE) {
			return false;
		}

		$room_data = array(
			'name' => $this->input->post('name'),
			'user_id' => $this->input->post('user_id') ?: null,
			'location' => $this->input->post('location'),
			'notes' => $this->input->post('notes'),
			'bookable' => $this->input->post('bookable'),
			'room_group_id' => $this->input->post('room_group_id'),
		);

		if (empty($room_id)) {

			$uri = 'setup/rooms/groups/view/'.$room_data['room_group_id'];

			$room_id = $this->rooms_model->insert($room_data);

			if ($room_id) {
				$msg = sprintf(lang('room.create.success'), $room_data['name']);
				$flashmsg = msgbox('info', $msg);
			} else {
				$line = lang('room.create.error');
				$flashmsg = msgbox('error', $line);
			}

		} else {

			$uri = 'setup/rooms/rooms/edit/'.$room_id;

			if ($this->rooms_model->update($room_id, $room_data)) {
				$msg = sprintf(lang('room.update.success'), $room_data['name']);
				$flashmsg = msgbox('info', $msg);
			} else {
				$line = lang('room.update.error');
				$flashmsg = msgbox('error', $line);
			}

		}

		// Process image things
		//
		$image_status = $this->process_image($room_id);
		if ( ! $image_status) {
			$this->data['notice'] = msgbox('error', lang('room.error.bad_image'));
			return false;
		}

		// Process field-related things
		//
		$fields_status = $this->process_fields($room_id);
		if ( ! $fields_status) {
			$this->data['notice'] = msgbox('error', lang('room.error.fields_error'));
			return false;
		}

		$this->session->set_flashdata('saved', $flashmsg);

		redirect($uri);
	}


	/**
	 * Handle the uploading of an image when saving a room.
	 *
	 */
	private function process_image($room_id = NULL)
	{
		if (empty($room_id)) {
			return TRUE;
		}

		if ($this->input->post('photo_delete')) {
			$this->rooms_model->delete_photo($room_id);
		}

		$has_image = (isset($_FILES['userfile'])
		              && isset($_FILES['userfile']['name'])
		              && ! empty($_FILES['userfile']['name']));

		if ( ! $has_image) {
			return TRUE;
		}

		// Upload config
		//

		$upload_config = array(
			'upload_path' => config_item('upload_path'),
			'allowed_types' => 'jpg|jpeg|png|gif',
			'max_size' => $this->data['max_size_bytes'],
			'encrypt_name' => TRUE,
		);

		$this->load->library('upload', $upload_config);

		if ( ! $this->upload->do_upload()) {
			$error = $this->upload->display_errors('','');
			$this->session->set_flashdata('image_error', $error);
			$image_error = $error;
			return FALSE;
		}

		// File uploaded
		//

		$upload_data = $this->upload->data();

		$this->load->library('image_lib');

		$image_config = array(
			'image_library' => 'gd2',
			'source_image' => $upload_data['full_path'],
			'maintain_ratio' => TRUE,
			'width' => 800,
			'height' => 800,
			'master_dim' => 'auto',
		);

		$this->image_lib->initialize($image_config);

		$res = $this->image_lib->resize();

		if ( ! $res) {
			$this->session->set_flashdata('image_error', $this->image_lib->display_errors());
			return FALSE;
		}

		handle_uploaded_file($upload_data['full_path']);

		// Remove previous photo
		$this->rooms_model->delete_photo($room_id);

		// Update DB with new photo
		$this->rooms_model->update($room_id, array(
			'photo' => $upload_data['file_name'],
		));

		return TRUE;
	}


	/**
	 * Process the updating of field values when saving a room
	 *
	 */
	private function process_fields($room_id = NULL)
	{
		if (empty($room_id)) {
			return TRUE;
		}

		$fieldvalues = array();
		$fields = $this->rooms_model->GetFields();
		$fields = (is_array($fields) ? $fields : array());

		foreach ($fields as $field) {
			$key = $field->field_id;
			$value = $this->input->post("f{$key}");
			$fieldvalues[ $key ] = $value;
		}

		return $this->rooms_model->save_field_values($room_id, $fieldvalues);
	}




	/**
	 * Controller function to delete a room
	 *
	 */
	public function delete($id)
	{
		$room = $this->data['room'] = $this->find_room($id);
		if ($room->room_group_id) {
			$group = $this->data['room_group'] = $this->find_room_group($room->room_group_id);
		}

		$uri = ($room->room_group_id)
			? 'setup/rooms/groups/view/'.$room->room_group_id
			: 'setup/rooms';

		if ($this->input->post('id') == $id) {
			$this->rooms_model->delete($this->input->post('id'));
			$msg = sprintf(lang('room.delete.success'), $room->name);
			$flashmsg = msgbox('info', $msg);
			$this->session->set_flashdata('saved', $flashmsg);
			redirect($uri);
		}

		$this->data['action'] = current_url();
		$this->data['id'] = $id;
		$this->data['cancel'] = $uri;
		$this->data['text'] = lang('room.delete.warning');

		$this->data['title'] = $this->data['showtitle'] = sprintf(lang('room.delete.title'), $room->name);

		$icons = $this->load->view('setup/rooms/_icons_rooms', $this->data, true);

		$body = $this->load->view('partials/deleteconfirm', $this->data, TRUE);

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


	private function find_room($room_id)
	{
		if (empty($room_id)) {
			show_404();
		}

		$room = $this->rooms_model->get_by_id($room_id);

		if (empty($room)) {
			show_404();
		}

		return $room;
	}



}
