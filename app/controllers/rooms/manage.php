<?php
/*
	This file is part of Classroombookings.

	Classroombookings is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	Classroombookings is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with Classroombookings.  If not, see <http://www.gnu.org/licenses/>.
*/

/*
	Room permission object types
		E:	Everyone
		O:	Room owner
		U:	User
		G:	Group
		D:	Department
*/


class Manage extends Controller{
	
	
	var $tpl;
	var $lasterr;
	var $resize_errors;
	var $types;
	
	
	function Manage(){
		parent::Controller();
		$this->load->model('security');
		$this->load->model('rooms_model');
		$this->load->model('departments_model');
		$this->load->helper('text');
		$this->load->helper('file');
		$this->tpl = $this->config->item('template');
		$this->output->enable_profiler($this->config->item('profiler'));
		$this->types = $this->rooms_model->types;
	}
	
	
	
	
	/**
	 * Page function: main rooms listing page
	 */
	function index(){
		
		$this->auth->check('rooms');
		
		$links[] = array('rooms/manage/add', 'Add a new room');
		$links[] = array('rooms/manage/attributes', 'Room attributes');
		$tpl['links'] = $this->load->view('parts/linkbar', $links, TRUE);
		
		$body['rooms'] = $this->rooms_model->get_in_categories();
		$body['cats'] = $this->rooms_model->get_categories_dropdown();
		$body['cats'][-1] = '(Uncategorised)';
		
		if($body['rooms'] == FALSE){
			$tpl['body'] = $this->msg->err($this->rooms_model->lasterr);
		} else {
			$tpl['body'] = $this->load->view('rooms/index', $body, TRUE);
		}
		
		$tpl['subnav'] = $this->rooms_model->subnav();
		$tpl['title'] = 'Rooms';
		$tpl['pagetitle'] = $tpl['title'];
		$this->load->view($this->tpl, $tpl);
		
	}
	
	
	
	
	/**
	 * Page function: add a room
	 */
	function add($tab = 'details'){
		
		$this->auth->check('rooms.add');
		$body['tab'] = ($this->session->flashdata('tab')) ? $this->session->flashdata('tab') : $tab;
		$body['room'] = NULL;
		$body['room_id'] = NULL;
		
		$body['users'] = $this->security->get_users_dropdown(TRUE);
		$body['cats'] = $this->rooms_model->get_categories_dropdown(TRUE);
		$body['cats'][-2] = 'Add new ...';
		
		$tpl['subnav'] = $this->rooms_model->subnav();
		$tpl['title'] = 'Add room';
		$tpl['pagetitle'] = 'Add a new room';
		$tpl['body'] = $this->load->view('rooms/addedit', $body, TRUE);
		$this->load->view($this->tpl, $tpl);
		
	}
	
	
	
	
	/**
	 * Page function: edit a room
	 */
	function edit($room_id, $tab = 'details'){
		
		$this->auth->check('rooms.edit');
		
		$body['tab'] = ($this->session->flashdata('tab')) ? $this->session->flashdata('tab') : $tab;
		$body['room'] = $this->rooms_model->get($room_id);
		$body['room_id'] = $room_id;
		
		// Permissions
		$body['permissions'] = $this->config->item('permissions');	// list of all AVAILABLE permissions
		$body['room_permissions'] = array();		// Booking-related permissions
		foreach($body['permissions']['room'] as $p){
			$body['room_permissions'][$p[0]] = $p[1];
		}
		
		$body['entries'] = $this->rooms_model->get_permissions($room_id);		// Permission entries
		$body['users'] = $this->security->get_users_dropdown(TRUE);
		$body['groups'] = $this->security->get_groups_dropdown();
		$body['departments'] = $this->departments_model->get_dropdown();
		
		#echo '<!-- ' . var_export($body, TRUE) . ' -->';
		
		// Categories
		$body['cats'] = $this->rooms_model->get_categories_dropdown(TRUE);
		$body['cats'][-2] = 'Add new ...';
		
		$tpl['subnav'] = $this->rooms_model->subnav();
		$tpl['title'] = 'Edit room';
		
		if($body['room'] != FALSE){
			$tpl['pagetitle'] = 'Edit room: ' . $body['room']->name;
			$tpl['body'] = $this->load->view('rooms/addedit', $body, TRUE);
		} else {
			$tpl['pagetitle'] = 'Error getting room';
			$tpl['body'] = $this->msg->err('Could not load the specified room. Please check the ID and try again.');
		}
		
		// Put the current room_id into a session variable (when deleting a permission, we can re-direct here)
		$this->session->set_userdata('editing_room_id', $room_id);
		
		$this->load->view($this->tpl, $tpl);
		
	}
	
	
	
	
	/**
	 * Form destination: Add/Edit a room
	 */
	function save(){
		
		$room_id = $this->input->post('room_id');
		
		$this->form_validation->set_rules('room_id', 'Room ID');
		$this->form_validation->set_rules('name', 'Name', 'required|max_length[20]|trim');
		$this->form_validation->set_rules('description', 'Description', 'max_length[50]|trim');
		$this->form_validation->set_rules('user_id', 'Room owner', 'required|integer');
		$this->form_validation->set_rules('category_id', 'Category', 'required');
		$this->form_validation->set_rules('bookable', 'Bookable', 'exact_length[1]');
		$this->form_validation->set_error_delimiters('<li>', '</li>');
		
		$data = array();
		
		// Check if upload is successful
		$upload = $this->_do_upload();
		if($upload == FALSE){
			// Upload failed OR nothing was uploaded
			// Check if a previous attempt was successful (will be in session)
			$rpupload = $this->session->userdata('rpupload');
			if($rpupload){
				// Upload has previously happened, use session data
				$upload = $rpupload;
			}
		} else {
			// Upload was successful, set the session data
			$this->session->set_userdata('rpupload', $upload);
		}
		
		#die(var_dump($upload));
		
		
		// Do certain actions based on category chosen (could be new or existing)
		$data['category_id'] = $this->input->post('category_id');
		if(is_numeric($data['category_id'])){
			// Haven't chosen to add a new category or it's not chosen, or no existing selected
			// Convert to proper integer
			$data['category_id'] = (int)$this->input->post('category_id');
			switch($data['category_id']){
				// If chosen a non-valued option, set to null
				case -1: $data['category_id'] = NULL; break;
				case -2: $data['category_id'] = NULL; break;
			}
		} else {
			// New category selected - add it, then get the new ID
			$add = $this->rooms_model->add_category($data['category_id']);
			$data['category_id'] = ($add == FALSE) ? NULL : $add;
		}
		
		
		// Check form validation
		if($this->form_validation->run() == FALSE){
			
			// Failed
			($room_id == NULL) ? $this->add() : $this->edit($room_id);
			
		} else {
			
			// All fields validated
			
			// Set initial photo value to nothing
			$data['photo'] = NULL;
			
			// Check image upload is OK
			if($upload != FALSE){
				// Attempt to resize the image
				$resize = $this->_process_image($upload);
				if($resize != FALSE){
					// Resize didn't fail - we can now use the name returned from the function
					$data['photo'] = $resize;
				} else {
					// Resize failed - add error message
					$this->msg->add('err', implode(', ', $this->resize_errors), $this->lasterr);
				}
			} else {
				// Not uploaded, maintain existing one
				$data['photo'] = $this->input->post('photo');
			}
			
			// Delete room photo if required (form filename doesn't match new filename)
			if(!empty($data['photo']) && $this->input->post('photo')){
				if($data['photo'] != $this->input->post('photo')){
					$this->_delete_photo($this->input->post('photo'));
				}
			}
			
			// Delete room photo if user ticks delete box
			if((int)$this->input->post('delete') == 1){
				$this->_delete_photo($data['photo']);
				$data['photo'] = NULL;
			}
			
			// Clear the photo upload data from the session
			$this->session->unset_userdata('rpupload');
			
			// Data array of fields to add to the database
			$data['name'] = $this->input->post('name');
			$data['description'] = $this->input->post('description');
			$data['bookable'] = ((int)$this->input->post('bookable') == 1) ? 1 : 0;
			$data['user_id'] = ((int)$this->input->post('user_id') != -1) ? (int)$this->input->post('user_id') : NULL;
			
			#var_dump($data);
			
			if($room_id == NULL){
				
				// Adding a new room
				
				$add = $this->rooms_model->add($data);
				
				if($add == TRUE){
					$this->msg->add('info', $this->lang->line('ROOMS_ADD_OK'));					
					if($this->auth->check('rooms.permissions', TRUE)){
						$this->msg->add('note', 'You can now configure the permissions for this room by '.anchor(sprintf('rooms/manage/edit/%d/%s', $add, 'permissions'), 'clicking here.'));
					}
				} else {
					$this->msg->add('err', sprintf($this->lang->line('ROOMS_ADD_FAIL', $this->rooms_model->lasterr)));
				}
				
			} else {
				
				// Updating a room
				
				$edit = $this->rooms_model->edit($room_id, $data);
				
				if($edit == TRUE){
					$this->msg->add('info', $this->lang->line('ROOMS_EDIT_OK'));
				} else {
					$this->msg->add('err', sprintf($this->lang->line('ROOMS_ADD_FAIL', $this->rooms_model->lasterr)));
				}
				
			}
			
			// All done - redirect
			#die(print_r($data));
			redirect('rooms/manage');
			
		}
		
	}
	
	
	
	
	/**
	 * Delete a room
	 *
	 * @param	room_id		Room ID to delete
	 */
	function delete($room_id = NULL){
		
		$this->auth->check('rooms.delete');
		
		// Check if a form has been submitted; if not - show it to ask user confirmation
		if($this->input->post('id')){
		
			// Form has been submitted (so the POST value exists)
			// Call model function to delete room
			$delete = $this->rooms_model->delete($this->input->post('id'));
			if($delete == FALSE){
				$this->msg->add('err', $this->rooms_model->lasterr, 'An error occured');
			} else {
				$this->msg->add('info', 'The room has been deleted.');
			}
			// Redirect
			redirect('rooms/manage');
			
		} else {
			
			if($room_id == NULL){
				
				$tpl['title'] = 'Delete room';
				$tpl['pagetitle'] = $tpl['title'];
				$tpl['body'] = $this->msg->err('Cannot find the room or no room ID given.');
				
			} else {
				
				// Get room info so we can present the confirmation page with a name
				$room = $this->rooms_model->get($room_id);
				
				if($room == FALSE){
					
					$tpl['title'] = 'Delete room';
					$tpl['pagetitle'] = $tpl['title'];
					$tpl['body'] = $this->msg->err('Could not find that room or no room ID given.');
					
				} else {
					
					// Initialise page
					$body['action'] = 'rooms/manage/delete';
					$body['id'] = $room_id;
					$body['cancel'] = 'rooms';
					$body['text'] = 'If you delete this room, all bookings made on it and its permissions will also be removed.';
					$tpl['title'] = 'Delete room';
					$tpl['pagetitle'] = 'Delete ' . $room->name;
					$tpl['body'] = $this->load->view('parts/deleteconfirm', $body, TRUE);
					
				}
				
			}
			
			$tpl['subnav'] = $this->rooms_model->subnav();
			$this->load->view($this->tpl, $tpl);
			
		}
		
	}
	
	
	
	
	
	// ---------- PERMISSIONS BELOW ---------- //
	
	
	
	
	
	/**
	 * Form destination: Save room permissions
	 */
	function save_permission(){
		
		#print_r($_POST);
		
		$room_id = $this->input->post('room_id');
		
		// Base validation
		$this->form_validation->set_rules('room_id', 'Room ID', 'required|integer');
		$this->form_validation->set_rules('object', 'Object type', 'required');
		
		// Set up appropriate validation + data array based on which object type is selected
		switch($this->input->post('object')){
			case 'everyone':
				$data['type'] = 'e';
				$object_id = 0;
			break;
			
			case 'roomowner':
				$data['type'] = 'o';
				$object_id = 0;
			break;
			
			case 'user':
				$this->form_validation->set_rules('user_id', 'User', 'required|integer');
				$data['type'] = 'u';
				$data['user_id'] = $this->input->post('user_id');
				$object_id = $data['user_id'];
			break;
			
			case 'group':
				$this->form_validation->set_rules('group_id', 'Group', 'required|integer');
				$data['type'] = 'g';
				$data['group_id'] = $this->input->post('group_id');
				$object_id = $data['group_id'];
			break;
			
			case 'department':
				$this->form_validation->set_rules('department_id', 'Department', 'required|integer');
				$data['type'] = 'd';
				$data['department_id'] = $this->input->post('department_id');
				$object_id = $data['department_id'];
			break;
			default:
			break;
			
		}
		
		$perms = 'permissions_' . $room_id;
		$this->form_validation->set_rules($perms, 'Permissions', 'required');
		$this->form_validation->set_error_delimiters('<li>', '</li>');
		
		
		if($this->form_validation->run() == FALSE){
			
			$this->edit($room_id, 'permissions');
			
		} else {
			
			$data['room_id'] = $room_id;
			$data['permissions'] = serialize($this->input->post($perms));
			
			// Unique permissions hash
			$data['hash'] = md5(sprintf('%d:%s:%d', $data['room_id'], $data['type'], $object_id));	#, $data['permissions']));
			
			// Add to the database
			$add = $this->rooms_model->add_permission_entry($data);
			
			if($add == TRUE){
				$this->msg->add('info', $this->lang->line('ROOMS_PERMS_ADD_OK'));
			} else {
				$this->msg->add('err', 'An error occured: ' . $this->rooms_model->lasterr);
			}
			
			redirect(sprintf('rooms/manage/edit/%d/%s', $room_id, 'permissions'));
			
		}
		
	}
	
	
	
	
	/**
	 * Delete a room permission entry
	 *
	 * @param	int		entry_id	Permission entry ID to delete
	 */
	function delete_permission($entry_id = NULL){
		
		$this->auth->check('rooms.permissions');
		
		// Check if a form has been submitted; if not - show it to ask user confirmation
		if($this->input->post('id')){
		
			// Form has been submitted (so the POST value exists)
			// Call model function to delete room
			$delete = $this->rooms_model->delete_permission($this->input->post('id'));
			if($delete == FALSE){
				$this->msg->add('err', $this->rooms_model->lasterr, 'An error occured');
			} else {
				$this->msg->add('info', 'The permission entry has been deleted.');
			}
			// Redirect
			redirect(sprintf('rooms/manage/edit/%d/%s', $this->session->userdata('editing_room_id'), 'permissions'));
			
		} else {
			
			if($entry_id == NULL){
				
				$tpl['title'] = 'Delete permission entry';
				$tpl['pagetitle'] = $tpl['title'];
				$tpl['body'] = $this->msg->err('Cannot find the permission entry or no permission ID given.');
				
			} else {
				
				// Get room info so we can present the confirmation page with a name
				$entry = $this->rooms_model->get_permission_entry($entry_id);
				
				if($entry == FALSE){
					
					$tpl['title'] = 'Delete permission entry';
					$tpl['pagetitle'] = $tpl['title'];
					$tpl['body'] = $this->msg->err('Could not find that permission entry or no entry ID given.');
					
				} else {
					
					// Initialise page
					$body['action'] = 'rooms/manage/delete_permission';
					$body['id'] = $entry_id;
					$body['cancel'] = sprintf('rooms/manage/edit/%d/%s', $this->session->userdata('editing_room_id'), 'permissions');
					#$body['text'] = 'If you delete this room, all bookings made on it and its permissions will also be removed.';
					$tpl['title'] = 'Delete permission entry';
					
					$title = 'Delete permission for %s on %s';
					$tpl['pagetitle'] = sprintf($title, $entry->nicename, $entry->room_name);
					$tpl['body'] = $this->load->view('parts/deleteconfirm', $body, TRUE);
					
				}
				
			}
			
			$tpl['subnav'] = $this->rooms_model->subnav();
			$this->load->view($this->tpl, $tpl);
			
		}
		
	}
	
	
	
	
	
	// ---------- OTHER MISC STUFF HERE ---------- //
	
	
	
	
	
	/**
	 * Carry out the uploading of the photo from the form
	 *
	 * @return	bool
	 */
	function _do_upload(){
		
		// Do upload if it was submitted
		$config['upload_path'] = 'temp';
		$config['allowed_types'] = 'jpg|jpeg|gif|png';
		$config['encrypt_name'] = TRUE;
		$this->load->library('upload', $config);
		
		$upload = $this->upload->do_upload();
		
		if($upload == TRUE){
			// Get data of uploaded file
			$data = $this->upload->data();
			// Check if it is an image
			if($data['is_image'] == 1){
				return $data;
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
		
	}
	
	
	
	
	/**
	 * Process an uploaded room photo image
	 *
	 * @param	array		data	Array containing the uploaded file information
	 * @return	mixed		Filename on success, FALSE on failure
	 */
	function _process_image($data){
		
		// Largest side dimensions for small and large thumbnails
		$px_sm = 320;
		$px_lg = 640;
		
		// Generate new base name for this image
		$new_name = uniqid(TRUE);
		
		// Initialise array for resizing errors
		$this->resize_errors = array();
		
		// Array to hold the new dimensions
		$dimensions = array();
		
		// Work out the dimensions of the image based on longest side, or set both to same if equal
		if ($data['image_width'] > $data['image_height']){
			$dimensions['sm']['w'] = $px_sm;
			$dimensions['lg']['w'] = $px_lg;
			$dimensions['sm']['h'] = $data['image_height'] * ($px_sm / $data['image_width']);
			$dimensions['lg']['h'] = $data['image_height'] * ($px_lg / $data['image_width']);
		} elseif($data['image_width'] < $data['image_height']){
			$dimensions['sm']['w'] = $data['image_width'] * ($px_sm / $data['image_height']);
			$dimensions['lg']['w'] = $data['image_width'] * ($px_lg / $data['image_height']);
			$dimensions['sm']['h'] = $px_sm;
			$dimensions['lg']['h'] = $px_lg;
		} elseif ($data['image_width'] == $data['image_height']){
			$dimensions['sm']['w'] = $px_sm;
			$dimensions['lg']['w'] = $px_lg;
			$dimensions['sm']['h'] = $px_sm;
			$dimensions['lg']['h'] = $px_lg;
		}
		
		// Global resize vars
		$config['image_library'] = 'gd2';
		$config['source_image']	= $data['full_path'];
		$config['create_thumb'] = FALSE;
		$config['maintain_ratio'] = TRUE;
		$config['quality'] = 100;
		$this->load->library('image_lib', $config);
		
		// Create small image
		$config['width'] = $dimensions['sm']['w'];
		$config['height'] = $dimensions['sm']['h'];
		$config['new_image'] = sprintf('%s/%s.sm%s', realpath('web/upload/'), $new_name, $data['file_ext']);
		$this->image_lib->initialize($config);
		$result_sm = $this->image_lib->resize();
		if($result_sm == FALSE){
			array_push($this->resize_errors, $this->image_lib->display_errors());
		}
		
		// Create larger image
		$config['width'] = $dimensions['lg']['w'];
		$config['height'] = $dimensions['lg']['h'];
		$config['new_image'] = sprintf('%s/%s.lg%s', realpath('web/upload/'), $new_name, $data['file_ext']);
		$this->image_lib->initialize($config);
		$result_lg = $this->image_lib->resize();
		if($result_lg == FALSE){
			array_push($this->resize_errors, $this->image_lib->display_errors());
		}
		
		// Delete the original source file now we're finished with it
		@unlink($data['full_path']);
		
		// Finished resizing functions - test for errors and return
		if($this->resize_errors == NULL){
			// No errors encountered - delete original image
			$name = sprintf('%s.#%s', $new_name, $data['file_ext']);
			return $name;
		} else {
			// One or more errors occured when resizing the images
			$this->lasterr = 'Failed to resize the images.';
			return FALSE;
		}
		
	}
	
	
	
	
	/**
	 * Function to delete the files associated with a given photo
	 *
	 * @param	string	Filename with # - will be replaced
	 */
	function _delete_photo($file){
		@unlink('web/upload/'.image_small($file));
		@unlink('web/upload/'.image_large($file));
	}
	
	
	
	
}


/* End of file: /app/controllers/rooms/manage.php */