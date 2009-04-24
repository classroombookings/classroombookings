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


class Holidays extends Controller {


	var $tpl;
	

	function Holidays(){
		parent::Controller();
		$this->load->model('academic');
		$this->load->model('holidays_model');
		$this->tpl = $this->config->item('template');
		$this->output->enable_profiler($this->config->item('profiler'));
	}
	
	
	
	
	function index(){
		$this->auth->check('holidays');
		
		/*$links[] = array('academic/main', 'Academic setup');
		$links[] = array('academic/years', 'Years');
		$links[] = array('academic/terms', 'Term dates');
		$links[] = array('academic/weeks', 'Timetable weeks');
		$links[] = array('academic/periods', 'Periods');
		$links[] = array('academic/holidays', 'Holidays', TRUE);
		$tpl['links'] = $this->load->view('parts/linkbar', $links, TRUE);*/
		
		$body['holidays'] = $this->holidays_model->get(NULL, NULL, $this->session->userdata('year_working'));
		$tpl['body'] = $this->load->view('academic/holidays/index', $body, TRUE);
		
		$tpl['sidebar'] = "<p class=\"bg-yellow\">Add a holiday if you want to define a stretch of time within a term where bookings can't be made.<br /><br />Make the dates the same if you only want it to last one day.</p>";
		
		$tpl['subnav'] = $this->academic->subnav();
		$tpl['title'] = 'Holidays';
		$tpl['pagetitle'] = 'Holidays';
		$this->load->view($this->tpl, $tpl);
	}
	
	
	
	
	function save(){
		
		/*
			Lots of validation stuff happening here.
			We need to see what we're actually doing so we validate the right stuff.
			By that, I mean just editing, just adding, or both?
		*/
		
		// TODO: create a comparable SHA1 hash of the items fetched from DB to see if we actually need to update any items
		
		$this->form_validation->set_rules('newholiday[name]', 'Name', 'max_length[50]|trim');
		
		if($this->input->post('holiday_ids')){
			
			// Existing holidays were submitted, could be editing
			$holidays = $this->input->post('holiday');
			$to_delete = array();
			
			foreach($this->input->post('holiday_ids') as $holiday_id){
				$this->form_validation->set_rules("holiday[{$holiday_id}][name]", 'Name', 'max_length[50]|trim');
				$this->form_validation->set_rules("holiday[{$holiday_id}][date_start]", 'Start date', 'required|exact_length[10]|trim|callback__is_valid_date');
				$this->form_validation->set_rules("holiday[{$holiday_id}][date_end]", 'End date', "required|exact_length[10]|trim|callback__is_valid_date");
				$this->form_validation->set_rules("holiday[{$holiday_id}][holiday_id]", "Holiday ID", "callback__datecheck");
				if(isset($holidays[$holiday_id]['delete'])){ array_push($to_delete, $holiday_id); }
			}
			
			// If we were asked to delete selected holidays, do that and nothing else.
			if($this->input->post('btn_delete') == 'delete'){
				$this->_delete_multiple($to_delete);
			}
			
		} else {
			
			$this->form_validation->set_rules('newholiday[name]', 'Name', 'required|max_length[50]|trim');
			
		}
		
		$newholiday = $this->input->post('newholiday');
		
		if(!empty($newholiday['name'])){
			$this->form_validation->set_rules('newholiday[date_start]', 'Start date', 'required|exact_length[10]|trim|callback__is_valid_date');
			$this->form_validation->set_rules('newholiday[date_end]', 'End date', 'required|exact_length[10]|trim|callback__is_valid_date|callback__is_after');
		}
		
		$this->form_validation->set_error_delimiters('<li>', '</li>');
		
		if($this->form_validation->run() == FALSE){
			
			// Re-show form with validation errors
			$this->index();
			
		} else {
			
			// Do we have any existing holidays to update?
			if(!empty($holidays)){
				
				// Update existing ones
				$edit = $this->holidays_model->edit($holidays);
				
				if($edit == FALSE){
					#$this->lasterr = 'Could not update the existing holidays';
					$this->msg->add('err', $this->holidays_model->lasterr);
				} else {
					$this->msg->add('info', 'Holidays have been updated.');
				}
				
			}
			
			// Get our new holiday to add if it exists
			if(!empty($newholiday['name'])){
				
				// New one to add
				
				$data['name'] = $newholiday['name'];
				$data['date_start'] = $newholiday['date_start'];
				$data['date_end'] = $newholiday['date_end'];
				$data['year_id'] = $this->session->userdata('year_working');
				
				$add = $this->holidays_model->add($data);
				
				if($add == FALSE){
					$this->msg->add('err', $this->holidays_model->lasterr);
				} else {
					$this->msg->add('info', 'The new holiday has been added successfully.');
				}
				
			}
			
			unset($data);
			
			#$this->index();
			redirect('academic/holidays');
			
			#print_r($term);
			
		}
		
	}
	
	
	
	
	function _delete_multiple($holidays){
		
		$this->auth->check('holidays.delete');
		
		if(empty($holidays)){
			
			$this->msg->add('err', 'No holidays were selected for deletion.');
			
		} else {
			
			$str = implode(',', $holidays);
			$str = preg_replace('/,$/', '', $str);
			
			$sql = sprintf('DELETE FROM holidays WHERE holiday_id IN (%s)', $str);
			$query = $this->db->query($sql);
			
			if($query == TRUE){
				$this->msg->add('info', 'The holidays have been deleted successfully.');
			} else {
				$this->msg->add('err', 'An error occured when trying to delete the holidays.');
			}
			
		}
		
		redirect('academic/holidays');
		
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
	 * VALIDATION	_datecheck
	 * 
	 * Checks if end date on supplied holiday is valid (after the start date).
	 * It works this way because of the way the form is created (and only used when editing/updating)
	 * 
	 * @param	int		term_id		ID of the holiday we are looking up
	 * @return	bool on success	 
	 */	 	 	 	 	 	 
	function _datecheck($holiday_id){
		
		// Get array of terms submitted
		$holidays = $this->input->post("holiday");
		
		// Get start + end dates from the form data
		$start = $holidays[$holiday_id]['date_start'];
		$end = $holidays[$holiday_id]['date_end'];
		// Also pick up the name as something to reference it to if it doesn't validate
		$name = $holidays[$holiday_id]['name'];
		
		// Convert dates into datatype we can easily work with
		$startarr = explode('-', $start);
		$startint = mktime(0, 0, 0, $startarr[1], $startarr[2], $startarr[0]);
		
		$endarr = explode('-', $end);
		$endint = mktime(0, 0, 0, $endarr[1], $endarr[2], $endarr[0]);
		
		// Check if end date is greater than start date
		if($endint >= $startint){
			return TRUE;
		} else {
			$this->form_validation->set_message('_datecheck', "The end date for {$name} must be after or equal to its start date ({$start})");
			return FALSE;
		}
		
	}
	
	
	
	
	/**
	 * Basic date checking to ensure end date is after the start date.
	 * Only used when adding a new term.
	 *
	 * @param	int		end		End date
	 * @return	bool
	 */
	function _is_after($end){
		$newterm =  $this->input->post('newterm');
		$start = $newterm['date_start'];
		
		$startarr = explode('-', $start);
		$startint = mktime(0, 0, 0, $startarr[1], $startarr[2], $startarr[0]);
		
		$endarr = explode('-', $end);
		$endint = mktime(0, 0, 0, $endarr[1], $endarr[2], $endarr[0]);
		
		if($endint > $startint){
			return TRUE;
		} else {
			$this->form_validation->set_message('_is_after', "The end date must be after the new term's start date ({$start})");
			return FALSE;
		}
	}
	
	
	
	
}


/* End of file: app/controllers/academic/holidays.php */