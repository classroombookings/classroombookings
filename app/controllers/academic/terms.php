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
		$links[] = array('academic/terms', 'Term dates', TRUE);
		$links[] = array('academic/weeks', 'Timetable weeks');
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
		
		/*
			Lots of validation stuff happening here.
			We need to see what we're actually doing so we validate the right stuff.
			By that, I mean just editing, just adding, or both?
		*/
		
		// TODO: create a comparable SHA1 hash of the items fetched from DB to see if we actually need to update any items
		
		#$period_id = $this->input->post('period_id');
		
		$this->form_validation->set_rules('newterm[name]', 'Name', 'max_length[50]|trim');
		
		if($this->input->post('term_ids')){
			
			// Existing terms were submitted, could be editing
			$terms = $this->input->post('term');
			$to_delete = array();
			
			foreach($this->input->post('term_ids') as $term_id){
				$this->form_validation->set_rules("term[{$term_id}][name]", 'Name', 'max_length[50]|trim');
				$this->form_validation->set_rules("term[{$term_id}][date_start]", 'Start date', 'required|exact_length[10]|trim|callback__is_valid_date');
				$this->form_validation->set_rules("term[{$term_id}][date_end]", 'End date', "required|exact_length[10]|trim|callback__is_valid_date");
				$this->form_validation->set_rules("term[{$term_id}][term_id]", "Term ID", "callback__datecheck");
				if(isset($terms[$term_id]['delete'])){ array_push($to_delete, $term_id); }
			}
			
			// If we were asked to delete selected terms, do that and nothing else.
			if($this->input->post('btn_delete') == 'delete'){
				$this->_delete_multiple($to_delete);
			}
			
		} else {
			
			$this->form_validation->set_rules('newterm[name]', 'Name', 'required|max_length[50]|trim');
			
		}
		
		$newterm = $this->input->post('newterm');
		
		if(!empty($newterm['name'])){
			$this->form_validation->set_rules('newterm[date_start]', 'Start date', 'required|exact_length[10]|trim|callback__is_valid_date');
			$this->form_validation->set_rules('newterm[date_end]', 'End date', 'required|exact_length[10]|trim|callback__is_valid_date|callback__is_after');
		}
		
		$this->form_validation->set_error_delimiters('<li>', '</li>');
		
		if($this->form_validation->run() == FALSE){
			
			// Re-show form with validation errors
			$this->index();
			
		} else {
			
			// Do we have any existing terms to update?
			if(!empty($terms)){
				
				// Update existing ones
				$edit = $this->terms_model->edit($terms);
				
				if($edit == FALSE){
					#$this->lasterr = 'Could not update the existing terms';
					$this->msg->add('err', $this->terms_model->lasterr);
				} else {
					$this->msg->add('info', 'Terms have been updated.');
				}
				
			}
			
			// Get our new term to add if it exists
			if(!empty($newterm['name'])){
				
				// New one to add
				
				$data['name'] = $newterm['name'];
				$data['date_start'] = $newterm['date_start'];
				$data['date_end'] = $newterm['date_end'];
				$data['year_id'] = $this->session->userdata('year_working');
				
				$add = $this->terms_model->add($data);
				
				if($add == FALSE){
					#$this->lasterr = $this->terms_model->lasterr;
					$this->msg->add('err', $this->terms_model->lasterr);
				} else {
					$this->msg->add('info', 'The new term has been added successfully.');
				}
				
			}
			
			unset($data);
			
			#$this->index();
			redirect('academic/terms');
			
			#print_r($term);
			
		}
		
	}
	
	
	
	
	function delete($term_id = NULL){
		
		$this->auth->check('terms.delete');
		
		// Check if a form has been submitted; if not - show it to ask user confirmation
		if($this->input->post('id')){
			
			// Form has been submitted (so the POST value exists)
			// Call model function to delete term
			$delete = $this->terms_model->delete($this->input->post('id'));
			if($delete == FALSE){
				$this->msg->add('err', $this->terms_model->lasterr, 'An error occured');
			} else {
				$this->msg->add('info', 'The term has been deleted.');
			}
			// Redirect
			redirect('academic/terms');
			
		} else {
			
			$tpl['title'] = 'Delete term';
			$tpl['pagetitle'] = $tpl['title'];
			
			if($term_id == NULL){
				
				$tpl['title'] = 'Delete term';
				$tpl['pagetitle'] = $tpl['title'];
				$tpl['body'] = $this->msg->err('Could not find that term or no term ID given.');
				
			} else {
				
				// Get term info so we can present the confirmation page with a name
				$term = $this->terms_model->get($term_id, NULL, $this->session->userdata('year_working'));
				
				if($term == FALSE){
				
					$tpl['body'] = $this->msg->err('Could not find that term or no term ID given.');
					
				} else {
					
					// Initialise page
					$body['action'] = 'academic/terms/delete';
					$body['id'] = $term_id;
					$body['cancel'] = 'academic/terms';
					$body['text'] = 'If you delete this term, the following associated items will also be removed:';
					$body['text'] .= '<ul><li>Bookings</li><li>Holidays</li></ul>';
					$tpl['title'] = 'Delete term';
					$tpl['pagetitle'] = 'Delete term ' . $term->name;
					$tpl['body'] = $this->load->view('parts/deleteconfirm', $body, TRUE);
					
				}
				
			}
			
			$this->load->view($this->tpl, $tpl);
			
		}
		
	}
	
	
	
	
	function _delete_multiple($terms){
		
		$this->auth->check('terms.delete');
		
		if(empty($terms)){
			
			$this->msg->add('err', 'No terms were selected for deletion.');
			
		} else {
			
			$str = implode(',', $terms);
			$str = preg_replace('/,$/', '', $str);
			
			$sql = sprintf('DELETE FROM terms WHERE term_id IN (%s)', $str);
			$query = $this->db->query($sql);
			
			if($query == TRUE){
				$this->msg->add('info', 'The terms have been deleted successfully.');
			} else {
				$this->msg->add('err', 'An error occured when trying to delete the terms.');
			}
			
		}
		
		redirect('academic/terms');
		
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
	 * Checks if end date on supplied term is valid (after the start date).
	 * It works this way because of the way the form is created (and only used when editing/updating)
	 * 
	 * @param	int		term_id		ID of the term we are looking up
	 * @return	bool on success	 
	 */	 	 	 	 	 	 
	function _datecheck($term_id){
		
		// Get array of terms submitted
		$terms = $this->input->post("term");
		
		// Get start + end dates from the form data
		$start = $terms[$term_id]['date_start'];
		$end = $terms[$term_id]['date_end'];
		// Also pick up the name as something to reference it to if it doesn't validate
		$name = $terms[$term_id]['name'];
		
		// Convert dates into datatype we can easily work with
		$startarr = explode('-', $start);
		$startint = mktime(0, 0, 0, $startarr[1], $startarr[2], $startarr[0]);
		
		$endarr = explode('-', $end);
		$endint = mktime(0, 0, 0, $endarr[1], $endarr[2], $endarr[0]);
		
		// Check if end date is greater than start date
		if($endint > $startint){
			return TRUE;
		} else {
			$this->form_validation->set_message('_datecheck', "The end date for {$name} must be after its start date ({$start})");
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




/* End of file: app/controllers/academic/terms.php */