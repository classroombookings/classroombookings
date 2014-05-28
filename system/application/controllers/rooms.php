<?php
class Rooms extends Controller {





  function Rooms(){
    parent::Controller();

		// Load language
  	$this->lang->load('crbs', 'english');

		// Get school id
    $this->school_id = $this->session->userdata('school_id');

    $this->output->enable_profiler($this->session->userdata('profiler'));

    // Check user is logged in & is admin
    if($this->uri->segment(2) != 'info'){
	    if(!$this->userauth->loggedin()){
	    	$this->session->set_flashdata('login', $this->load->view('msgbox/error', $this->lang->line('crbs_auth_mustbeloggedin'), True) );
				redirect('site/home', 'location');
			} else {
				$this->loggedin = True;
				if(!$this->userauth->CheckAuthLevel(ADMINISTRATOR)){
					$this->session->set_flashdata('auth', $this->load->view('msgbox/error', $this->lang->line('crbs_auth_mustbeadmin'), True) );
					redirect('controlpanel', 'location');
				}
			}
		}
		// Load models
		$this->load->model('crud_model', 'crud');
    $this->load->model('school_model', 'M_school');
    $this->load->model('rooms_model', 'M_rooms');
    $this->load->model('users_model', 'M_users');
    // Load the icon selector helper
    $this->load->helper('iconsel');
    // Load the image resizer script
		$this->load->script('resize');
    #$this->load->scaffolding('rooms');
  }




  function info(){
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





	function index(){
		// Get list of rooms from database
		$body['rooms'] = $this->M_rooms->Get(NULL, $this->school_id);	//$this->session->userdata('schoolcode'));
		// Set main layout
		$layout['title'] = 'Rooms';
		$layout['showtitle'] = $layout['title'];
		$layout['body'] = $this->load->view('rooms/rooms_index', $body, True);
		$this->load->view('layout', $layout);
	}





	/**
	 * Controller function to handle the Add page
	 */
	function add(){
		// Get list of users
		$body['users'] = $this->M_users->Get( $this->session->userdata('schoolcode'), NULL, array('user_id', 'username', 'displayname'), 'lastname asc, username asc' );
		$body['users'] = $this->M_users->Get(NULL, NULL, array('user_id', 'username', 'displayname'), 'lastname asc, username asc' );
		$body['fields'] = $this->M_rooms->GetFields($this->session->userdata('schoolcode'));
		// Load view
		$layout['title'] = 'Add Room';
		$layout['showtitle'] = $layout['title'];

		$cols[0]['content'] = $this->load->view('rooms/rooms_add', $body, True);
		$cols[0]['width'] = '70%';
		$cols[1]['content'] = $this->load->view('rooms/rooms_add_side', $body, True);
		$cols[1]['width'] = '30%';

		$layout['body'] = $this->load->view('columns', $cols, True);	#$this->load->view('rooms/rooms_add', $body, True);
		$this->load->view('layout', $layout);
	}





	/**
	 * Controller function to handle an edit
	 */
	function edit($id = NULL){
		if($id == NULL){ $id = $this->uri->segment(3); }
		$body['users'] = $this->M_users->Get(NULL, NULL, array('user_id', 'username', 'displayname'), 'lastname asc, username asc' );
		$body['fields'] = $this->M_rooms->GetFields($this->session->userdata('schoolcode'));
		$body['fieldvalues'] = $this->M_rooms->GetFieldValues($id);
		$body['room'] = $this->M_rooms->Get($id, $this->school_id);
		#print_r($body);
		// Load view
		$layout['title'] = 'Edit Room';
		$layout['showtitle'] = $layout['title'];

		$cols[0]['content'] = $this->load->view('rooms/rooms_add', $body, True);
		$cols[0]['width'] = '70%';
		$cols[1]['content'] = $this->load->view('rooms/rooms_add_side', $body, True);
		$cols[1]['width'] = '30%';

		$layout['body'] = $this->load->view('columns', $cols, True);	#$this->load->view('rooms/rooms_add', $body, True);
		$this->load->view( 'layout', $layout);
	}





	/**
	 * Save
	 */
	 function save(){

	 	// Get ID from form
		$room_id = $this->input->post('room_id');

		// Load image manipulation library
		$this->load->library('image_lib');

		// Load upload library
		$this->load->library('upload');

		// Load file helper library
		$this->load->helper('file');

		// Upload config
		$upload['upload_path'] 			= './webroot/images/roomphotos/temp';
		$upload['allowed_types']		= 'jpg|jpeg';
		$upload['max_size']					= '4096';
		$upload['max_width']				= '3000';
		$upload['max_height']				= '3000';
		$this->upload->initialize($upload);

		// Validation rules
		$vrules['room_id']		= 'required';
		$vrules['name']				= 'required|min_length[2]|max_length[20]';
		$vrules['location']		= 'min_length[2]|max_length[40]';
		$vrules['icon']				= 'max_length[255]';
		$vrules['notes']			= 'max_length[255]';
		$vrules['photo']			= 'max_length[255]';
		$this->validation->set_rules($vrules);

		// Pretty it up a bit for error validation message
		$vfields['room_id']					= 'Room ID';
		$vfields['name']						= 'Room name';
		$vfields['location']				= 'Location';
		$vfields['user_id']					= 'Teacher';
		$vfields['icon']						= 'Icon';
		$vfields['notes']						= 'Notes';
		$vfields['bookable']				= 'Can be booked';
		$vfields['photo']						= 'Photo';
		$vfields['photo_delete']		= 'Delete photo';
		$this->validation->set_fields($vfields);

		// Set the error delims to a nice styled red hint under the fields
		$this->validation->set_error_delimiters('<p class="hint error"><span>', '</span></p>');

    if ($this->validation->run() == FALSE){

      // Validation failed
			if($room_id != "X"){
				return $this->edit($room_id);
			} else {
				return $this->add();
			}

		} else {

			log_message('debug', 'CRBS: Validation succeeded');

			if( !$this->upload->do_upload() ){
				// Not uploaded
				$error = $this->upload->display_errors('','');
				if( $error != 'You did not select a file to upload' ){
					$this->session->set_flashdata('image_error', $error);
					echo $error;
					if( $room_id != "X"){
						return $this->edit($room_id);

					} else {
						return $this->add();
					}
				}
				$upload = false;
			} else {
				#echo "uploading";
				// File uploaded
				$photo = $this->upload->data();

				// new filename is <md5(rawname sessionid)>.<extension>
				$newfile = md5($photo['raw_name'].$this->session->userdata('session_id')) . $photo['file_ext'];

				$thumbs['image_library']		= 'GD2';
				$thumbs['source_image']			= $photo['full_path'];
				$thumbs['create_thumb']			= false;
				$thumbs['maintain_ratio']		= true;
				$thumbs['master_dim']				= 'auto';
				$this->image_lib->initialize($thumbs);

				$errcount = 0;

				$thumbs['new_image']				= 'webroot/images/roomphotos/640/'.$newfile;
				$thumbs['width']						= 640;
				$thumbs['height']						= 480;
				$this->image_lib->initialize($thumbs);
				if( !$this->image_lib->resize() ){ $errcount++; }

				$thumbs['new_image']				= 'webroot/images/roomphotos/320/'.$newfile;
				$thumbs['width']						= 320;
				$thumbs['height']						= 240;
				$this->image_lib->initialize($thumbs);
				if( !$this->image_lib->resize() ){ $errcount++; }

				$thumbs['new_image']				= 'webroot/images/roomphotos/160/'.$newfile;
				$thumbs['width']						= 160;
				$thumbs['height']						= 120;
				$this->image_lib->initialize($thumbs);
				if( !$this->image_lib->resize() ){ $errcount++; }

				log_message('debug', 'CRBS: Full path to uploaded photo: '.$photo['full_path']);
				log_message('debug', 'CRBS: Resize room photo image error count: '.$errcount);

				if( $errcount == 0 ){
					unlink($photo['full_path']);
				}

				// Done
				$upload = true;
				//print_r($photo);
			}

		  // Validation succeeded!
			/*$data = array	(
											'rooms.name'				=> $this->input->post('name'),
											'rooms.location'		=> $this->input->post('location'),
											'rooms.icon'				=> $this->input->post('icon'),
											'rooms.notes'				=> $this->input->post('notes'),
											'rooms.user_id'			=> $this->input->post('user_id'),
											#'rooms.foobar'=>'foo',
										);*/

			$data = array();
			$data['rooms.name'] = $this->input->post('name');
			$data['rooms.location'] = $this->input->post('location');
			$data['rooms.icon'] = $this->input->post('icon');
			$data['rooms.notes'] = $this->input->post('notes');
			$data['rooms.user_id'] = $this->input->post('user_id');
			$data['rooms.bookable'] = ($this->input->post('bookable')) ? 1 : 0;

			if( $upload == true ){
				$data['rooms.photo'] = $newfile;
			}

			// If user clicked the 'delete photo' button on an edit, delete photo
			if( $this->input->post('photo_delete') != NULL && $room_id != 'X'){
				$this->M_rooms->delete_photo($room_id);
			}

			$fields = $this->M_rooms->GetFields($this->session->userdata('schoolcode'));
			foreach($fields as $field){
				$fieldvalues[$field->field_id] = $this->input->post('f'.$field->field_id);
			}
			#print_r($fieldvalues);

			// Now see if we are editing or adding
			if($room_id == 'X'){
				// No ID, adding new record
				$room_id = $this->M_rooms->add($data);
				$this->M_rooms->save_field_values($room_id, $fieldvalues);
				$this->session->set_flashdata('saved', $this->load->view('msgbox/info', $data['rooms.name'] . ' has been added.', True) );
			} else {
				// We have an ID, updating existing record
				// Now we delete the CURRENT photo on the database before we do an update on the ID (and thus possibly changing the photo)
				if( $upload == true ){$this->M_rooms->delete_photo($room_id); }
				// Update row with new details
				$this->M_rooms->edit($room_id, $data);
				$this->M_rooms->save_field_values($room_id, $fieldvalues);
				$this->session->set_flashdata('saved', $this->load->view('msgbox/info', $data['rooms.name'] . ' has been modified.', True) );
			}
			// Go back to index
			redirect('rooms', 'redirect');

		}

	}





	/**
	 * Controller function to delete a room
	 */
	function delete(){
	  // Get ID from URL
		$id = $this->uri->segment(3);

		// Check if a form has been submitted; if not - show it to ask user confirmation
		if( $this->input->post('id') ){
			// Form has been submitted (so the POST value exists)
			// Call model function to delete manufacturer
			$this->M_rooms->delete($this->input->post('id'));
			$this->session->set_flashdata('saved', $this->load->view('msgbox/info', 'The room has been deleted.', True) );
			// Redirect to rooms again
			redirect('rooms', 'redirect');
		} else {
			// Initialise page
			$body['action'] = 'rooms/delete';
			$body['id'] = $id;
			$body['cancel'] = 'rooms';
			$body['text'] = 'If you delete this room, <strong>all bookings</strong> for this room will be <strong>permanently deleted</strong> as well.';
			// Load page
			$row = $this->M_rooms->Get($id, $this->school_id);
			$layout['title'] = 'Delete Room ('.$row->name.')';
			$layout['showtitle'] = $layout['title'];
			$layout['body'] = $this->load->view('partials/deleteconfirm', $body, TRUE);
			$this->load->view('layout', $layout);
		}
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
