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


class Periods extends Controller {


	var $tpl;
	

	function Periods(){
		parent::Controller();
		$this->load->model('periods_model');
		$this->load->model('years_model');
		$this->tpl = $this->config->item('template');
		$this->output->enable_profiler($this->config->item('profiler'));
	}
	
	
	
	
	function index(){
		$this->auth->check('periods');
		
		$links[] = array('academic/periods/add', 'Add a new period');
		$links[] = array('academic/main', 'Academic setup');
		$links[] = array('academic/years', 'Years');
		$links[] = array('academic/terms', 'Term dates');
		$links[] = array('academic/weeks', 'Timetable weeks');
		$links[] = array('academic/periods', 'Periods', TRUE);
		$links[] = array('academic/holidays', 'Holidays');
		$tpl['links'] = $this->load->view('parts/linkbar', $links, TRUE);
		$body['days'] = $this->periods_model->days;
		$body['years'] = $this->years_model->get_dropdown();
		// Get list of periods
		$body['periods'] = $this->periods_model->get(NULL, NULL, $this->session->userdata('year_working'));
		
		if($body['periods'] == FALSE){
			$tpl['body'] = $this->msg->err($this->periods_model->lasterr);
			if($this->session->userdata('year_working') != NULL){
				$tpl['body'] .= $this->load->view('academic/periods/copy', $body, TRUE);
			}
		} else {
			$tpl['body'] = $this->load->view('academic/periods/index', $body, TRUE);
		}
		
		$tpl['title'] = 'Periods';
		$tpl['pagetitle'] = $tpl['title'];
		$this->load->view($this->tpl, $tpl);
	}
	
	
	
	
	function add(){
		if($this->session->userdata('year_working') == NULL){
			$this->msg->add('warn', 'You cannot add a period until an academic year has been made active or you have selected a working academic year.');
			redirect('academic/periods');
		}
		$this->auth->check('periods.add');
		$body['period'] = NULL;
		$body['period_id'] = NULL;
		$body['days'] = $this->periods_model->days;
		$tpl['sidebar'] = $this->load->view('academic/periods/addedit.sidebar.php', NULL, TRUE);
		$tpl['title'] = 'Add period';
		$tpl['pagetitle'] = 'Add a new period';
		$tpl['body'] = $this->load->view('academic/periods/addedit', $body, TRUE);
		$this->load->view($this->tpl, $tpl);
	}
	
	
	
	
	function edit($period_id){
		$this->auth->check('periods.edit');
		$body['period'] = $this->periods_model->get($period_id, NULL, $this->session->userdata('year_working'));
		$body['period_id'] = $period_id;
		$body['days'] = $this->periods_model->days;
		
		$tpl['title'] = 'Edit period';
		
		if($body['period'] != FALSE){
			$tpl['pagetitle'] = 'Edit period: ' . $body['period']->name;
			$tpl['body'] = $this->load->view('academic/periods/addedit', $body, TRUE);
		} else {
			$tpl['pagetitle'] = 'Error getting period details';
			$tpl['body'] = $this->msg->err('Could not load the specified period. Please check the ID and try again.');
		}
		
		$this->load->view($this->tpl, $tpl);
	}
	
	
	
	
	function save(){
		
		$period_id = $this->input->post('period_id');
		
		$this->form_validation->set_rules('period_id', 'Period ID');
		$this->form_validation->set_rules('name', 'Name', 'required|max_length[20]|trim');
		$this->form_validation->set_rules('time_start', 'Start time', 'required|exact_length[5]|callback__is_valid_time');
		$this->form_validation->set_rules('time_end', 'End time', 'required|exact_length[5]|callback__is_valid_time|callback__is_after[time_start]');
		$this->form_validation->set_rules('days[]', 'Days');
		$this->form_validation->set_error_delimiters('<li>', '</li>');
		
		if($this->form_validation->run() == FALSE){
			
			// Validation failed - load required action depending on the state of user_id
			($period_id == NULL) ? $this->add() : $this->edit($period_id);
			
		} else {
			
			// Validation OK
			$data['name'] = $this->input->post('name');
			$data['time_start'] = $this->input->post('time_start');
			$data['time_end'] = $this->input->post('time_end');
			$data['days'] = $this->input->post('days');
			$data['bookable'] = ($this->input->post('bookable') == '1') ? 1 : 0;
			$data['year_id'] = $this->session->userdata('year_working');
			
			if($period_id == NULL){
				
				$add = $this->periods_model->add($data);
				
				if($add == TRUE){
					$this->msg->add('info', $this->lang->line('PERIODS_ADD_OK'));
				} else {
					$this->msg->add('err', $this->lang->line('PERIODS_ADD_FAIL') . '. ' . $this->periods_model->lasterr);
				}
			
			} else {
			
				// Updating existing period
				$edit = $this->periods_model->edit($period_id, $data);
				if($edit == TRUE){
					$this->msg->add('info', $this->lang->line('PERIODS_EDIT_OK'));
				} else {
					$this->msg->add('err', $this->lang->line('PERIODS_EDIT_FAIL') . '. ' . $this->periods_model->lasterr);
				}
				
			}
			
			// All done, redirect!
			redirect('academic/periods');
			
		}
		
	}
	
	
	
	
	function delete($period_id = NULL){
		$this->auth->check('periods.delete');
		
		// Check if a form has been submitted; if not - show it to ask user confirmation
		if($this->input->post('id')){
		
			// Form has been submitted (so the POST value exists)
			// Call model function to delete period
			$delete = $this->periods_model->delete($this->input->post('id'));
			if($delete == FALSE){
				$this->msg->add('err', $this->periods_model->lasterr, 'An error occured');
			} else {
				$this->msg->add('info', 'The period has been deleted.');
			}
			// Redirect
			redirect('academic/periods');
			
		} else {
			
			if($period_id == NULL){
				
				$tpl['title'] = 'Delete period';
				$tpl['pagetitle'] = $tpl['title'];
				$tpl['body'] = $this->msg->err('Cannot find the period or no period ID given.');
				
			} else {
				
				// Get period info so we can present the confirmation page with a name
				$period = $this->periods_model->get($period_id, NULL, $this->session->userdata('year_working'));
				
				if($period == FALSE){
				
					$tpl['title'] = 'Delete period';
					$tpl['pagetitle'] = $tpl['title'];
					$tpl['body'] = $this->msg->err('Could not find that period or no period ID given.');
					
				} else {
					
					// Initialise page
					$body['action'] = 'academic/periods/delete';
					$body['id'] = $period_id;
					$body['cancel'] = 'academic/periods';
					//$body['text'] = 'If you delete this period, all people assigned to it will be removed.';
					$tpl['title'] = 'Delete period';
					$tpl['pagetitle'] = 'Delete ' . $period->name;
					$tpl['body'] = $this->load->view('parts/deleteconfirm', $body, TRUE);
					
				}
				
			}
			
			$this->load->view($this->tpl, $tpl);
			
		}
		
	}
	
	
	
	
	function copy(){
		$year_id = $this->input->post('year_id');
		
		$this->form_validation->set_rules('year_id', 'Year ID', 'required');
		
		if($this->form_validation->run() === FALSE){
			
			$this->index();
			
		} else {
			
			// Got ID, attempty copy
			$copy = $this->periods_model->copy($year_id, $this->session->userdata('year_working'));
			if($copy == FALSE){
				$this->msg->add('err', $this->periods_model->lasterr);
			} else {
				$this->msg->add('info', 'The periods were successfully copied');
			}
			redirect('academic/periods');
			
		}
	}
	
	
	
	
	/**
	 * VALIDATION _is_valid_time
	 * 
	 * Check to see if time entered is a valid time between 00:00 and 23:59
	 * 
	 * @param		string		$time		Time
	 * @return	bool on success	 
	 * 
	 */	 	 	 	 	 	 	
	function _is_valid_time($time){
		$times['am'] = strtotime('00:00');
		$times['pm'] = strtotime('23:59');
		$times['data'] = strtotime($time);
		if( ($times['data'] >= $times['am'] && $times['data'] <= $times['pm']) || !isset($times['data']) ){
			$ret = true;
		} else {
			$this->form_validation->set_message('_is_valid_time', 'You entered an invalid time. It must be between 00:00 and 23:59.');
			$ret = false;
		}
		return $ret;
	}
	
	
	
	
	
	/**
	 * VALIDATION	_is_after
	 * 
	 * Check that the time entered (time_end) is greater than the start time
	 * 
	 * @param		string		$time		Time
	 * @return		bool on success	 
	 *
	 */	 	 	 	 	 	 
	function _is_after($time){
		$start = strtotime( $this->_fix_time( $this->input->post( 'time_start' ) ) );
		$end = strtotime( $this->_fix_time($time) );
		if( $end > $start ){
			$ret = true;
		} else {
			$this->form_validation->set_message('_is_after', 'The end time must be equal to or greater than the start time of '.$this->_fix_time( $this->input->post( 'time_start' ) ).'.' );
			$ret = false;
		}
		return $ret;
	}
	
	
	
	
	
	/**
	 * Fix the time format
	 * 
	 * Formats the time properly (HH:MM) for the database for any time given
	 * 
	 * @param		string		$time		Time
	 * @return		string		Formatted time
	 */	 	 	 	 	 
	function _fix_time($time){
		return strftime('%H:%M', strtotime($time));
	}
	
	
	
	
}


/* End of file app/controllers/academic/periods.php */