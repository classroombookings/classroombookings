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
		$this->tpl = $this->config->item('template');
		$this->output->enable_profiler($this->config->item('profiler'));
	}
	
	
	
	
	function index(){
		$this->auth->check('periods');
		
		$links[0] = array('academic/periods/add', 'Add a new period');
		$links[1] = array('academic/main', 'Academic setup');
		$tpl['links'] = $this->load->view('parts/linkbar', $links, TRUE);
		
		// Get list of periods
		$body['periods'] = $this->periods_model->get();
		if($body['periods'] == FALSE){
			$tpl['body'] = $this->msg->err($this->periods_model->lasterr);
		} else {
			$tpl['body'] = $this->load->view('academic/periods/index', $body, TRUE);
		}
		
		$tpl['title'] = 'Periods';
		$tpl['pagetitle'] = $tpl['title'];
		$this->load->view($this->tpl, $tpl);
	}
	
	
	
	
	function add(){
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
			$data['bookable'] = ($this->input->post('ldap') == '1') ? 1 : 0;
			
			if($period_id == NULL){
			
				$add = $this->periods_model->add($data);
				
				if($add == TRUE){
					$this->msg->add('info', sprintf($this->lang->line('PERIODS_ADD_OK'), $data['name']));
				} else {
					$this->msg->add('err', sprintf($this->lang->line('PERIODS_ADD_FAIL', $this->periods_model->lasterr)));
				}
			
			} else {
			
				// Updating existing period
				$edit = $this->periods_model->edit($period_id, $data);
				if($edit == TRUE){
					$this->msg->add('info', sprintf($this->lang->line('PERIODS_EDIT_OK'), $data['name']));
				} else {
					$this->msg->add('err', sprintf($this->lang->line('PERIODS_EDIT_FAIL', $this->periods_model->lasterr)));
				}
				
			}
			
			// All done, redirect!
			redirect('periods');
			
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
			$this->validation->set_message('_is_valid_time', 'You entered an invalid time. It must be between 00:00 and 23:59.');
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
			$this->validation->set_message('_is_after', 'The end time must be equal to or greater than the start time of '.$this->_fix_time( $this->input->post( 'time_start' ) ).'.' );
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