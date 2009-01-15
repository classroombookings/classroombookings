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


class Terms extends Controller {


	var $tpl;
	

	function Terms(){
		parent::Controller();
		$this->load->model('terms_model');
		$this->tpl = $this->config->item('template');
		$this->output->enable_profiler($this->config->item('profiler'));
	}
	
	
	
	
	
	function index(){
		$this->auth->check('terms');
		
		$links[] = array('academic/main', 'Academic setup');
		$links[] = array('academic/years', 'Years');
		$links[] = array('academic/weeks', 'Weeks');
		$links[] = array('academic/periods', 'Periods');
		$links[] = array('academic/holidays', 'Holidays');
		$tpl['links'] = $this->load->view('parts/linkbar', $links, TRUE);
		
		$body['terms'] = $this->terms_model->get(NULL, NULL, $this->session->userdata('year_working'));
		$tpl['body'] = $this->load->view('academic/terms/index', $body, TRUE);
		
		$tpl['title'] = 'Terms';
		$tpl['pagetitle'] = 'Term dates';
		$this->load->view($this->tpl, $tpl);
	}
	
	
	
	
	function save(){
		$period_id = $this->input->post('period_id');
		
		// Set validation rules 
		/* if(in_array(-1, $this->input->post('term_id'))){
			$this->form_validation->set_rules('name[-1]', 'Name', 'required|max_length[50]|trim');
			$this->form_validation->set_rules('date_start[-1]', 'required|Start date', 'exact_length[10]|trim|callback__is_valid_date');
			$this->form_validation->set_rules('date_end[-1]', 'End date', 'required|exact_length[10]|trim|callback__is_valid_date|callback__is_after[date_start]');
		} else {
			$this->form_validation->set_rules('term_id[]', 'Term ID');
			$this->form_validation->set_rules('name[]', 'Name', 'required|max_length[50]|trim');
			$this->form_validation->set_rules('date_start[]', 'required|Start date', 'exact_length[10]|trim|callback__is_valid_date');
			$this->form_validation->set_rules('date_end[]', 'End date', 'required|exact_length[10]|trim|callback__is_valid_date|callback__is_after[date_start]');
		} */
		$this->form_validation->set_rules('term[][name]', 'Name', 'required|min_length[0]|trim');
		$this->form_validation->set_error_delimiters('<li>', '</li>');
		
		
		if($this->form_validation->run() == FALSE){
			
			echo("didn't work");
			
		} else {
			
			/* $terms = array();
			
			foreach($this->input->post('name') as $term_id => $name){
				if(!empty($name)){ $terms[$term_id]['name'] = $name; }
			}
			foreach($this->input->post('date_start') as $term_id => $date_start){
				if(!empty($date_start)){ $terms[$term_id]['date_start'] = $date_start; }
			}
			foreach($this->input->post('date_end') as $term_id => $date_end){
				if(!empty($date_end)){ $terms[$term_id]['date_end'] = $date_end; }
			}
			
			// Get our new term to add if it exists
			if(array_key_exists(-1, $terms)){
				// New one to add
				// ...
				$data = $terms[-1];
				$data['year_id'] = $this->session->userdata('year_working');
				$add = $this->terms_model->add($data);
			} */
			
			unset($data);
			
			#print_r($term);
			
		}
		
	}
	
	
	
	
	/**
	 * VALIDATION _is_valid_date
	 * 
	 * Check to see if date entered is valid
	 * 
	 * @param		string		$date		Date
	 * @return	bool on success	 
	 * 
	 */	 	 	 	 	 	 	
	function _is_valid_date($date){
		$datearray = explode('-', $date);
		$check = @checkdate($datearray[1], $datearray[2], $datearray[0]);
		if($check == TRUE){
			return TRUE;
		} else {
			$this->form_validation->set_message('_is_valid_date', 'You entered an invalid date. It must be in the format YYYY-MM-DD.');
			return FALSE;
		}
	}
	
	
	
	
	
	/**
	 * VALIDATION	_is_after
	 * 
	 * Check that the date entered (date_end) is greater than the start date
	 * 
	 * @param		string		$date		Date
	 * @return		bool on success	 
	 *
	 */	 	 	 	 	 	 
	function _is_after($date){
		$start = $this->input->post('date_start');
		$startarr = explode('-', $start);
		$startint = mktime(0, 0, 0, $startarr[1], $startarr[2], $startarr[0]);
		
		$endarr = explode('-', $date);
		$endint = mktime(0, 0, 0, $endarr[1], $endarr[2], $endarr[0]);
		
		if($endint > $startint){
			return TRUE;
		} else {
			$this->form_validation->set_message('_is_after', 'The end date must be after the start date (' . $start . '.)');
			return FALSE;
		}
	}
	
	
	
	
}


/* End of file app/controllers/academic/terms.php */