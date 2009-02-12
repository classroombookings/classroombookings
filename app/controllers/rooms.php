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


class Rooms extends Controller{
	
	
	var $tpl;
	
	
	function Rooms(){
		parent::Controller();
		$this->load->model('security');
		$this->load->model('rooms_model');
		$this->load->model('departments_model');
		$this->load->helper('text');
		$this->load->helper('file');
		$this->tpl = $this->config->item('template');
		$this->output->enable_profiler($this->config->item('profiler'));
	}
	
	
	
	
	/**
	 * Page function: main rooms listing page
	 */
	function index(){
		$this->auth->check('rooms');
		
		$links[] = array('rooms/add', 'Add a new room');
		$links[] = array('rooms/attributes', 'Room attributes');
		$tpl['links'] = $this->load->view('parts/linkbar', $links, TRUE);
		
		$body['rooms'] = $this->rooms_model->get_in_categories();
		$body['cats'] = $this->rooms_model->get_categories_dropdown();
		$body['cats'][-1] = '(Uncategorised)';
		
		if($body['rooms'] == FALSE){
			$tpl['body'] = $this->msg->err($this->rooms_model->lasterr);
		} else {
			$tpl['body'] = $this->load->view('rooms/index', $body, TRUE);
		}
		
		$tpl['title'] = 'Rooms';
		$tpl['pagetitle'] = $tpl['title'];
		$this->load->view($this->tpl, $tpl);
	}
	
	
	
	
	/**
	 * Page function: add a room
	 */
	function add($tab = 'addedit-details'){
		$this->auth->check('rooms.add');
		$body['tab'] = ($this->session->flashdata('tab')) ? $this->session->flashdata('tab') : $tab;
		$body['room'] = NULL;
		$body['room_id'] = NULL;
		
		$body['users'] = $this->security->get_users_dropdown(TRUE);
		$body['cats'] = $this->rooms_model->get_categories_dropdown(TRUE);
		$body['cats'][-2] = 'Add new ...';
		
		$tpl['title'] = 'Add room';
		$tpl['pagetitle'] = 'Add a new room';
		$tpl['body'] = $this->load->view('rooms/addedit', $body, TRUE);
		$this->load->view($this->tpl, $tpl);
	}
	
	
	
	
	/**
	 * Page function: edit a room
	 */
	function edit($room_id, $tab = 'addedit-details'){
		$this->auth->check('rooms.edit');
		$body['tab'] = ($this->session->flashdata('tab')) ? $this->session->flashdata('tab') : $tab;
		$body['room'] = $this->rooms_model->get($room_id);
		$body['room_id'] = $room_id;
		
		$body['users'] = $this->security->get_users_dropdown(TRUE);
		$body['groups'] = $this->security->get_groups_dropdown();
		$body['departments'] = $this->departments_model->get_dropdown();
		
		$body['cats'] = $this->rooms_model->get_categories_dropdown(TRUE);
		$body['cats'][-2] = 'Add new ...';
		
		$tpl['title'] = 'Edit room';
		
		if($body['room'] != FALSE){
			$tpl['pagetitle'] = 'Edit room: ' . $body['room']->name;
			$tpl['body'] = $this->load->view('rooms/addedit', $body, TRUE);
		} else {
			$tpl['pagetitle'] = 'Error getting room';
			$tpl['body'] = $this->msg->err('Could not load the specified room. Please check the ID and try again.');
		}
		
		$this->load->view($this->tpl, $tpl);
	}
	
	
	
	
	function save(){
		
		$room_id = $this->input->post('room_id');
		
		$this->form_validation->set_rules('room_id', 'Room ID');
		$this->form_validation->set_rules('name', 'Name', 'required|max_length[20]|trim');
		$this->form_validation->set_rules('description', 'Description', 'max_length[50]|trim');
		$this->form_validation->set_rules('user_id', 'Room owner', 'required|integer');
		$this->form_validation->set_rules('category_id', 'Category', 'required');
		$this->form_validation->set_rules('bookable', 'Bookable', 'exact_length[1]');
		$this->form_validation->set_error_delimiters('<li>', '</li>');
		
		
		// Check if upload is successful.
		$upload = $this->_do_upload();
		#echo var_dump($upload);
		if($upload == FALSE){
			// Upload failed or was nothing upload
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
		#echo var_dump($upload);
		
		
		// Do certain actions based on category chosen
		$data['category_id'] = $this->input->post('category_id');
		if(is_numeric($data['category_id'])){
			// Haven't chosen to add a new category or it's not chosen, or no existing selected
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
			
			// OK
			if($upload != FALSE){
				$image = $this->_process_image($upload);
				$data['photo'] = '';	//TODO return value here
			}
			
			$data['name'] = $this->input->post('name');
			$data['description'] = $this->input->post('description');
			$data['bookable'] = ($this->input->post('bookable') == '1') ? 1 : 0;
			$data['user_id'] = ($this->input->post('user_id') != '-1') ? $this->input->post('user_id') : NULL;
			
		}
		
	}
	
	
	
	
	function _do_upload(){
		// Do upload if it was submitted
		$config['upload_path'] = 'temp';
		$config['allowed_types'] = 'jpg|jpeg|gif|png';
		$config['encrypt_name'] = TRUE;
		$this->load->library('upload', $config);
		
		$upload = $this->upload->do_upload();
		if($upload == TRUE){
			$data = $this->upload->data();
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
	 * @param array $data	Array containing the uploaded file information
	 * @return bool
	 */
	function _process_image($data){
		print_r($data);
		
		// Create small image
		$config['image_library'] = 'gd2';
		$config['source_image']	= $data['full_path'];
		$config['create_thumb'] = TRUE;
		$config['maintain_ratio'] = TRUE;
		$config['width'] = 320;
		$config['quality'] = 100;
		$config['new_image'] = sprintf('web/upload/%s.sm', $data['file_name']);
		$this->load->library('image_lib', $config);
		$this->image_lib->resize();
		$this->image_lib->clear();
		
		// Create larger image
		$config['image_library'] = 'gd2';
		$config['source_image']	= $data['full_path'];
		$config['create_thumb'] = TRUE;
		$config['maintain_ratio'] = TRUE;
		$config['width'] = 640;
		$config['quality'] = 100;
		$config['new_image'] = sprintf('web/upload/%s.lg', $data['file_name']);
		$this->load->library('image_lib', $config);
		$this->image_lib->resize();
		$this->image_lib->clear();
	}
	
	
	
	
}


?>
