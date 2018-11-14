<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rooms extends MY_Controller
{




	public function __construct()
	{
		parent::__construct();

		$this->require_logged_in();

		$this->load->model('crud_model');
		$this->load->model('school_model');
		$this->load->model('rooms_model');
		$this->load->model('users_model');
		$this->load->helper('number');

		$this->data['max_size_bytes'] = max_upload_file_size();
		$this->data['max_size_human'] = byte_format(max_upload_file_size());
	}



	function info()
	{
		#$this->output->enable_profiler(true);
		//$this->output->cache(60*24*7);
		$school_id = $this->uri->segment(3);
		$room_id = $this->uri->segment(4);

		$room['users'] = $this->M_users->Get(NULL, $this->school_id, array('user_id', 'username', 'displayname'), 'lastname asc, username asc' );

		$room['fields'] = $this->M_rooms->GetFields(NULL, $school_id);
		$room['fieldvalues'] = $this->M_rooms->GetFieldValues($room_id);
		$room['room'] = $this->M_rooms->Get($room_id, $school_id);

		#$room = $this->M_rooms->GetInfo($room_id, $school_id);
		#$room['fields'] = $this->M_rooms->GetFields(NULL, $this->school_id);
		#$room['fieldvalues'] = $this->M_rooms->GetFieldValues($room_id);

		$layout['body'] = $this->load->view('rooms/room_info', $room, True);
		$layout['title'] = $room['room']->name;
		#print_r($room);
		$this->load->view('minilayout', $layout);
	}





	function index()
	{
		$this->require_auth_level(ADMINISTRATOR);

		$this->data['rooms'] = $this->rooms_model->Get();

		$this->data['title'] = 'Rooms';
		$this->data['showtitle'] = $this->data['title'];
		$this->data['body'] = $this->load->view('rooms/rooms_index', $this->data, TRUE);

		return $this->render();
	}





	/**
	 * Controller function to handle the Add page
	 */
	function add()
	{
		$this->require_auth_level(ADMINISTRATOR);

		// Get list of users
		$this->data['users'] = $this->users_model->Get(NULL, NULL, array('user_id', 'username', 'displayname'), 'lastname asc, username asc');
		$this->data['fields'] = $this->rooms_model->GetFields();
		$this->data['fieldvalues'] = array();

		$this->data['title'] = 'Add Room';
		$this->data['showtitle'] = $this->data['title'];

		$columns = array(
			'c1' => array(
				'content' => $this->load->view('rooms/rooms_add', $this->data, TRUE),
				'width' => '70%',
			),
			'c2' => array(
				'content' => $this->load->view('rooms/rooms_add_side', $this->data, TRUE),
				'width' => '30%',
			),
		);

		$this->data['body'] = $this->load->view('columns', $columns, TRUE);

		return $this->render();
	}





	/**
	 * Controller function to handle an edit
	 */
	function edit($id = NULL)
	{
		$this->require_auth_level(ADMINISTRATOR);

		$this->data['room'] = $this->rooms_model->Get($id);

		if (empty($this->data['room'])) {
			show_404();
		}

		$this->data['users'] = $this->users_model->Get(NULL, NULL, array('user_id', 'username', 'displayname'), 'lastname asc, username asc' );
		$this->data['fields'] = $this->rooms_model->GetFields();
		$this->data['fieldvalues'] = $this->rooms_model->GetFieldValues($id);

		$this->data['title'] = 'Edit Room';
		$this->data['showtitle'] = $this->data['title'];

		$columns = array(
			'c1' => array(
				'content' => $this->load->view('rooms/rooms_add', $this->data, TRUE),
				'width' => '70%',
			),
			'c2' => array(
				'content' => $this->load->view('rooms/rooms_add_side', $this->data, TRUE),
				'width' => '30%',
			),
		);

		$this->data['body'] = $this->load->view('columns', $columns, TRUE);

		return $this->render();
	}




	/**
	 * Save
	 *
	 */
	function save()
	{
		// Get ID from form
		$room_id = $this->input->post('room_id');

		$this->load->library('form_validation');

		$this->form_validation->set_rules('room_id', 'ID', 'integer');
		$this->form_validation->set_rules('name', 'Name', 'required|min_length[1]|max_length[20]');
		$this->form_validation->set_rules('user_id', 'User', 'integer');
		$this->form_validation->set_rules('location', 'Location', 'max_length[40]');
		$this->form_validation->set_rules('notes', 'Notes', 'max_length[255]');
		$this->form_validation->set_rules('bookable', 'Bookable', 'integer');

		if ($this->form_validation->run() == FALSE) {
			return (empty($room_id) ? $this->add() : $this->edit($room_id));
		}

		$room_data = array(
			'name' => $this->input->post('name'),
			'user_id' => $this->input->post('user_id'),
			'location' => $this->input->post('location'),
			'notes' => $this->input->post('notes'),
			'bookable' => $this->input->post('bookable'),
		);

		if (empty($room_id)) {

			$room_id = $this->rooms_model->add($room_data);

			if ($room_id) {
				$line = sprintf($this->lang->line('crbs_action_added'), $room_data['name']);
				$flashmsg = msgbox('info', $line);
			} else {
				$line = sprintf($this->lang->line('crbs_action_dberror'), 'adding');
				$flashmsg = msgbox('error', $line);
			}

		} else {

			if ($this->rooms_model->edit($room_id, $room_data)) {
				$line = sprintf($this->lang->line('crbs_action_saved'), $room_data['name']);
				$flashmsg = msgbox('info', $line);
			} else {
				$line = sprintf($this->lang->line('crbs_action_dberror'), 'editing');
				$flashmsg = msgbox('error', $line);
			}

		}

		$this->session->set_flashdata('saved', $flashmsg);

		// Process image things
		//
		$image_status = $this->process_image($room_id);
		if ( ! $image_status) {
			return (empty($room_id) ? $this->add() : $this->edit($room_id));
		}

		// Process field-related things
		//
		$fields_status = $this->process_fields($room_id);
		if ( ! $fields_status) {
			return (empty($room_id) ? $this->add() : $this->edit($room_id));
		}

		redirect('rooms');
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
			'upload_path' => FCPATH . 'uploads',
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
			'width' => 1280,
			'height' => 1280,
			'master_dim' => 'auto',
		);

		$this->image_lib->initialize($image_config);

		$res = $this->image_lib->resize();

		if ( ! $res) {
			$this->session->set_flashdata('image_error', $this->image_lib->display_errors());
			return FALSE;
		}

		// Remove previous photo
		$this->rooms_model->delete_photo($room_id);

		// Update DB with new photo
		$this->rooms_model->edit($room_id, array(
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
	function delete($id = NULL)
	{
		// Check if a form has been submitted; if not - show it to ask user confirmation
		if ($this->input->post('id')) {
			$this->rooms_model->delete($this->input->post('id'));
			$flashmsg = msgbox('info', $this->lang->line('crbs_action_deleted'));
			$this->session->set_flashdata('saved', $flashmsg);
			redirect('rooms');
		}

		$this->data['action'] = 'rooms/delete';
		$this->data['id'] = $id;
		$this->data['cancel'] = 'rooms';
		$this->data['text'] = 'If you delete this room, <strong>all bookings</strong> for this room will be <strong>permanently deleted</strong> as well.';

		$row = $this->rooms_model->Get($id);
		$this->data['title'] = 'Delete Room ('.html_escape($row->name).')';
		$this->data['showtitle'] = $this->data['title'];
		$this->data['body'] = $this->load->view('partials/deleteconfirm', $this->data, TRUE);

		return $this->render();
	}




	/**
	 * FIELDS
	 */





	function fields_index(){
		$body['options_list'] = $this->M_rooms->options;
		// Get list of rooms from database
		$body['fields'] = $this->M_rooms->GetFields($this->session->userdata('schoolcode'));
		// Set main layout
		$layout['title'] = 'Room Fields';
		$layout['showtitle'] = 'Define Room Fields';
		$layout['body'] = $this->load->view('rooms/fields/rooms_fields_index', $body, True);
		$this->load->view('layout', $layout);
	}





	function fields_add(){
		$body['options_list'] = $this->M_rooms->options;
		// Load view
		$layout['title'] = 'Add Field';
		$layout['showtitle'] = $layout['title'];

		$cols[0]['content'] = $this->load->view('rooms/fields/rooms_fields_add', $body, True);
		$cols[0]['width'] = '70%';
		$cols[1]['content'] = '';	//$this->load->view('rooms/rooms_add_side', $body, True);
		$cols[1]['width'] = '30%';

		$layout['body'] = $this->load->view('columns', $cols, True);	#$this->load->view('rooms/rooms_add', $body, True);
		$this->load->view('layout', $layout);
	}





	/**
	 * Controller function to handle an edit
	 */
	function fields_edit($id = NULL){
		if($id == NULL){ $id = $this->uri->segment(4); }
		$body['field'] = $this->M_rooms->GetFields( $this->session->userdata('schoolcode'), $id );
		$body['options_list'] = $this->M_rooms->options;
		#print_r($body);
		// Load view
		$layout['title'] = 'Edit Field';
		$layout['showtitle'] = $layout['title'];

		$cols[0]['content'] = $this->load->view('rooms/fields/rooms_fields_add', $body, True);
		$cols[0]['width'] = '70%';
		$cols[1]['content'] = '';	//$this->load->view('rooms/rooms_add_side', $body, True);
		$cols[1]['width'] = '30%';

		$layout['body'] = $this->load->view('columns', $cols, True);	#$this->load->view('rooms/rooms_add', $body, True);
		$this->load->view( 'layout', $layout);
	}





	function fields_save(){

		// Get ID from form
		$field_id = $this->input->post('field_id');

		// Load validation
		#$this->load->library('validation');

		// Validation rules
		$vrules['field_id']		= 'required';
		$vrules['name']				= 'required|min_length[2]|max_length[64]';
		$this->validation->set_rules($vrules);

		// Pretty it up a bit for error validation message
		$vfields['field_id']		= 'Field ID';
		$vfields['name']				= 'Field name';
		$vfields['items']				= 'Items';
		$this->validation->set_fields($vfields);

		// Set the error delims to a nice styled red hint under the fields
		$this->validation->set_error_delimiters('<p class="hint error"><span>', '</span></p>');

		if ($this->validation->run() == FALSE){

	  // Validation failed
			if($field_id != "X"){
				$this->fields_edit($field_id);
			} else {
				$this->fields_add();
			}

		} else {

		  // Validation succeeded!
			$data['name']				= $this->input->post('name');
			$data['type']				= $this->input->post('type');
			$data['options']		= $this->input->post('options');

			// Now see if we are editing or adding
			if($field_id == 'X'){
				// No ID, adding new record
				$field_id = $this->M_rooms->field_add($data);
				$this->session->set_flashdata('saved', $this->load->view('msgbox/info', 'The <strong>'.$data['name'].'</strong> field has been added.', True) );
			} else {
				// We have an ID, updating existing record
				// Update row with new details
				$this->M_rooms->field_edit($field_id, $data);
				$this->session->set_flashdata('saved', $this->load->view('msgbox/info', 'The <strong>'.$data['name'].'</strong> field has been modified.', True) );
			}
			// Go back to index
			redirect('rooms/fields', 'redirect');
		}

	}





	/**
	 * Controller function to delete a room
	 */
	function fields_delete(){
	  // Get ID from URL
		$id = $this->uri->segment(4);
		// Check if a form has been submitted; if not - show it to ask user confirmation
		if( $this->input->post('id') ){
			// Form has been submitted (so the POST value exists)
			// Call model function to delete manufacturer
			$this->M_rooms->field_delete($this->input->post('id'));
			$this->session->set_flashdata('saved', $this->load->view('msgbox/info', 'The field has been deleted.', True) );
			// Redirect to rooms again
			redirect('rooms/fields', 'redirect');
		} else {
			// Initialise page
			$body['action'] = 'rooms/fields/delete';
			$body['id'] = $id;
			$body['cancel'] = 'rooms/fields';
			#$body['text'] = 'If you delete this field, <strong>all bookings</strong> for this room will be <strong>permanently deleted</strong> as well.';
			// Load page
			$row = $this->M_rooms->GetFields($id);
			$layout['title'] = 'Delete Field ('.$row->name.')';
			$layout['showtitle'] = $layout['title'];
			$layout['body'] = $this->load->view('partials/deleteconfirm', $body, TRUE);
			$this->load->view('layout', $layout);
		}
	}





}
?>
