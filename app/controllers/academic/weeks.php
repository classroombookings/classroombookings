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


class Weeks extends Controller {


	var $tpl;
	

	function Weeks(){
		parent::Controller();
		$this->load->model('academic');
		$this->load->model('weeks_model');
		$this->tpl = $this->config->item('template');
		$this->output->enable_profiler($this->config->item('profiler'));
	}
	
	
	
	
	
	function index(){
		$this->auth->check('weeks');
		
		$links[] = array('academic/weeks/add', 'Add a new week', 'add');
		/*$links[] = array('academic/main', 'Academic setup');
		$links[] = array('academic/years', 'Years');
		$links[] = array('academic/terms', 'Term dates');
		$links[] = array('academic/weeks', 'Timetable weeks', TRUE);
		$links[] = array('academic/periods', 'Periods');
		$links[] = array('academic/holidays', 'Holidays');*/
		$tpl['links'] = $this->load->view('parts/linkbar', $links, TRUE);
		
		// Get list of weeks
		$body['weeks'] = $this->weeks_model->get(NULL, NULL, $this->session->userdata('year_working'));
		if($body['weeks'] == FALSE){
			$tpl['body'] = $this->msg->err($this->weeks_model->lasterr);
		} else {
			$tpl['body'] = $this->load->view('academic/weeks/index', $body, TRUE);
		}
		
		$tpl['subnav'] = $this->academic->subnav();
		$tpl['title'] = 'Weeks';
		$tpl['pagetitle'] = $tpl['title'];
		$this->load->view($this->tpl, $tpl);
	}
	
	
	
	
	function add(){
		
		$this->auth->check('weeks.add');
		
		$body['week'] = NULL;
		$body['week_id'] = NULL;
		
		$tpl['subnav'] = $this->academic->subnav();
		$tpl['title'] = 'Add week';
		$tpl['pagetitle'] = 'Add a new week';
		
		$body['calendar'] = $this->weeks_model->calendar(NULL, $this->session->userdata('year_working'));
		$tpl['body'] = $this->load->view('academic/weeks/addedit', $body, TRUE);
		
		$this->load->view($this->tpl, $tpl);
		
	}
	
	
	
	
	function edit($week_id){
		
		$this->auth->check('weeks.edit');
		
		$body['week'] = $this->weeks_model->get($week_id);
		$body['week_id'] = $week_id;
		$body['calendar'] = $this->weeks_model->calendar($week_id, $this->session->userdata('year_working'));
		
		$tpl['subnav'] = $this->academic->subnav();
		$tpl['title'] = 'Edit week';
		
		if($body['week'] != FALSE){
			$tpl['pagetitle'] = 'Edit week: ' . $body['week']->name;
			$tpl['body'] = $this->load->view('academic/weeks/addedit', $body, TRUE);
		} else {
			$tpl['pagetitle'] = 'Error getting week';
			$tpl['body'] = $this->msg->err('Could not load the specified week. Please check the ID and try again.');
		}
		
		$this->load->view($this->tpl, $tpl);
		
	}
	
	
	
	
	function save(){
		
		$week_id = $this->input->post('week_id');
		
		$this->form_validation->set_rules('week_id', 'Week ID');
		$this->form_validation->set_rules('name', 'Name', 'required|max_length[20]|trim');
		$this->form_validation->set_rules('colour', 'Colour', 'required|max_length[7]');
		$this->form_validation->set_rules('dates[]', 'required|Week dates');
		$this->form_validation->set_error_delimiters('<li>', '</li>');
		
		if($this->form_validation->run() == FALSE){
			
			// Validation failed - load required action depending on the state of user_id
			($week_id == NULL) ? $this->add() : $this->edit($week_id);
			
		} else {
			
			// Validation OK
			$data['name'] = $this->input->post('name');
			$data['colour'] = $this->input->post('colour');
			$data['dates'] = ($this->input->post('dates')) ? $this->input->post('dates') : array();
			$data['year_id'] = $this->session->userdata('year_working');
			
			if($week_id == NULL){
				
				$add = $this->weeks_model->add($data);
				
				if($add == TRUE){
					$this->msg->add('info', sprintf($this->lang->line('WEEKS_ADD_OK'), $data['name']));
				} else {
					#$this->msg->add('err', sprintf($this->lang->line('WEEKS_ADD_FAIL', $this->weeks_model->lasterr)));
					$this->msg->add('err', $this->weeks_model->lasterr);
				}
				
			} else {
				
				$edit = $this->weeks_model->edit($week_id, $data);
				if($edit == TRUE){
					$this->msg->add('info', sprintf($this->lang->line('WEEKS_EDIT_OK'), $data['name']));
				} else {
					$this->msg->add('err', sprintf($this->lang->line('WEEkS_EDIT_FAIL', $this->weeks_model->lasterr)));
				}
				
			}
			
			// All done, redirect!
			redirect('academic/weeks');
			
		}
		
	}
	
	
	
	
	function delete($week_id = NULL){
		$this->auth->check('weeks.delete');
		
		// Check if a form has been submitted; if not - show it to ask user confirmation
		if($this->input->post('id')){
		
			// Form has been submitted (so the POST value exists)
			// Call model function to delete week
			$delete = $this->weeks_model->delete($this->input->post('id'));
			if($delete == FALSE){
				$this->msg->add('err', $this->weeks_model->lasterr, 'An error occured');
			} else {
				$this->msg->add('info', 'The week has been deleted.');
			}
			// Redirect
			redirect('academic/weeks');
			
		} else {
			
			if($week_id == NULL){
				
				$tpl['title'] = 'Delete week';
				$tpl['pagetitle'] = $tpl['title'];
				$tpl['body'] = $this->msg->err('Cannot find the week or no week ID given.');
				
			} else {
				
				// Get week info so we can present the confirmation page with a name
				$week = $this->weeks_model->get($week_id);
				
				if($week == FALSE){
				
					$tpl['title'] = 'Delete week';
					$tpl['pagetitle'] = $tpl['title'];
					$tpl['body'] = $this->msg->err('Could not find that week or no week ID given.');
					
				} else {
					
					// Initialise page
					$body['action'] = 'academic/weeks/delete';
					$body['id'] = $week_id;
					$body['cancel'] = 'academic/weeks';
					$body['text'] = 'If you delete this week, all bookings made on this week will also be removed.';
					$tpl['title'] = 'Delete week';
					$tpl['pagetitle'] = 'Delete ' . $week->name;
					$tpl['body'] = $this->load->view('parts/deleteconfirm', $body, TRUE);
					
				}
				
			}
			
			$tpl['subnav'] = $this->academic->subnav();
			$this->load->view($this->tpl, $tpl);
			
		}
		
	}

	
	
	
	
}


/* End of file app/controllers/academic/weeks.php */