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


class Attributes extends Controller{
	
	
	var $tpl;
	
	
	function Attributes(){
		parent::Controller();
		$this->load->model('rooms_model');
		#$this->load->helper('text');
		#$this->load->helper('file');
		$this->tpl = $this->config->item('template');
		$this->output->enable_profiler($this->config->item('profiler'));
	}
	
	
	
	
	function index(){
		
		$this->auth->check('rooms.attrs');
		
		$links[] = array('rooms/attributes/add', 'Add a new attribute', 'add');
		$tpl['links'] = $this->load->view('parts/linkbar', $links, TRUE);
		
		// Get list of room attributes
		$body['attrs'] = $this->rooms_model->get_attr_field();
		
		$body['fieldtypes'] = $this->rooms_model->fieldtypes;
		if($body['attrs'] == FALSE){
			$tpl['body'] = $this->msg->err($this->rooms_model->lasterr);
		} else {
			$tpl['body'] = $this->load->view('rooms/attributes/index', $body, TRUE);
		}
		
		$tpl['subnav'] = $this->rooms_model->subnav();
		$tpl['title'] = 'Room attribute fields';
		$tpl['pagetitle'] = $tpl['title'];
		$this->load->view($this->tpl, $tpl);
		
	}
	
	
	
	
	function add(){
		
		$this->auth->check('rooms.attrs');
		
		$body['field'] = NULL;
		$body['field_id'] = NULL;
		$body['fieldtypes'] = $this->rooms_model->fieldtypes;
		
		$tpl['sidebar'] = $this->load->view('rooms/attributes/addedit.field.side.php', NULL, TRUE);
		$tpl['subnav'] = $this->rooms_model->subnav();
		$tpl['title'] = 'Add a field';
		$tpl['pagetitle'] = $tpl['title'];
		$tpl['body'] = $this->load->view('rooms/attributes/addedit.field.php', $body, TRUE);
		$this->load->view($this->tpl, $tpl);
		
	}
	
	
	
	
	function edit($field_id){
		
		$this->auth->check('rooms.attrs');
		
		$body['field'] = $this->rooms_model->get_attr_field($field_id);
		$body['field_id'] = $field_id;
		$body['fieldtypes'] = $this->rooms_model->fieldtypes;
		
		if($body['field'] != FALSE){
			$tpl['pagetitle'] = 'Edit field: ' . $body['field']->name;
			$tpl['body'] = $this->load->view('rooms/attributes/addedit.field.php', $body, TRUE);
		} else {
			$tpl['pagetitle'] = 'Error getting field';
			$tpl['body'] = $this->msg->err('Could not load the specified field. Please check the ID and try again.');
		}
		
		$tpl['sidebar'] = $this->load->view('rooms/attributes/addedit.field.side.php', NULL, TRUE);
		$tpl['subnav'] = $this->rooms_model->subnav();
		$tpl['title'] = 'Edit field';
		
		$this->load->view($this->tpl, $tpl);
		
	}
	
	
	
	
	function save(){
		
		$field_id = $this->input->post('field_id');
		
		$this->form_validation->set_rules('field_id', 'Field ID');
		$this->form_validation->set_rules('name', 'Name', 'required|max_length[20]|trim');
		$this->form_validation->set_rules('type', 'Type', 'required');
		if($this->input->post('type') == 'select'){
			$this->form_validation->set_rules('options', 'Drop-down list options', 'required');
		}
		$this->form_validation->set_error_delimiters('<li>', '</li>');
		
		if($this->form_validation->run() == FALSE){
			
			// Validation failed - load required action depending on the state of user_id
			($field_id == NULL) ? $this->add() : $this->edit($field_id);
			
		} else {
			
			// Validation OK
			$data['name'] = $this->input->post('name');
			$data['type'] = $this->input->post('type');
			
			// Get options
			if($data['type'] == 'select'){
				$data['options'] = array();
				$sep = (strpos($this->input->post('options'), ',') === FALSE) ? "\n" : ",";
				$opts = explode($sep, $this->input->post('options'));
				foreach($opts as $opt){
					array_push($data['options'], trim($opt));
				}
			}
			
			#die(print_r($data));
			
			if($field_id == NULL){
				
				$add = $this->rooms_model->add_field($data);
				
				if($add == TRUE){
					$this->msg->add('info', sprintf($this->lang->line('FIELDS_ADD_OK'), $data['name']));
				} else {
					$this->msg->add('err', sprintf($this->lang->line('FIELDS_ADD_FAIL', $this->rooms_model->lasterr)));
				}
				
			} else {
				
				$edit = $this->rooms_model->edit_field($field_id, $data);
				
				if($edit == TRUE){
					$this->msg->add('info', sprintf($this->lang->line('FIELDS_EDIT_OK'), $data['name']));
				} else {
					$this->msg->add('err', sprintf($this->lang->line('FIELDS_EDIT_FAIL', $this->rooms_model->lasterr)));
				}
				
			}
			
			// All done, redirect!
			redirect('rooms/attributes');
			
		}
		
	}
	
	
	
	
	/**
	 * Delete a field
	 *
	 * @param	field_id		Field ID to delete
	 */
	function delete($field_id = NULL){
		
		$this->auth->check('rooms.attrs');
		
		// Check if a form has been submitted; if not - show it to ask user confirmation
		if($this->input->post('id')){
		
			// Form has been submitted (so the POST value exists)
			// Call model function to delete field
			$delete = $this->rooms_model->delete_field($this->input->post('id'));
			if($delete == FALSE){
				$this->msg->add('err', $this->rooms_model->lasterr, 'An error occured');
			} else {
				$this->msg->add('info', 'The field has been deleted.');
			}
			// Redirect
			redirect('rooms/attributes');
			
		} else {
			
			if($field_id == NULL){
				
				$tpl['title'] = 'Delete field';
				$tpl['pagetitle'] = $tpl['title'];
				$tpl['body'] = $this->msg->err('Cannot find the field or no field ID given.');
				
			} else {
				
				// Get field info so we can present the confirmation page with a name
				$field = $this->rooms_model->get_attr_field($field_id);
				
				if($field == FALSE){
					
					$tpl['title'] = 'Delete field';
					$tpl['pagetitle'] = $tpl['title'];
					$tpl['body'] = $this->msg->err('Could not find that field or no field ID given.');
					
				} else {
					
					// Initialise page
					$body['action'] = 'rooms/attributes/delete';
					$body['id'] = $field_id;
					$body['cancel'] = 'rooms/attributes';
					$body['text'] = 'If you delete this field, it will also disappear from all rooms.';
					$tpl['title'] = 'Delete field';
					$tpl['pagetitle'] = 'Delete ' . $field->name;
					$tpl['body'] = $this->load->view('parts/deleteconfirm', $body, TRUE);
					
				}
				
			}
			
			$tpl['subnav'] = $this->rooms_model->subnav();
			$this->load->view($this->tpl, $tpl);
			
		}
		
	}

	
	
	
}

/* End of file: /app/controllers/rooms/attributes.php */