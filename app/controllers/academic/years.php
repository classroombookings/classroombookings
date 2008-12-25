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


class Years extends Controller {


	var $tpl;
	

	function Years(){
		parent::Controller();
		$this->load->model('academic_model');
		$this->tpl = $this->config->item('template');
		$this->output->enable_profiler($this->config->item('profiler'));
	}
	
	
	
	
	
	function index(){
		$this->auth->check('years');
		
		$links[0] = array('academic/years/add', 'Add a new academic year');
		$links[1] = array('academic/weeks', 'Weeks');
		$tpl['links'] = $this->load->view('parts/linkbar', $links, TRUE);
		
		// Get list of years
		$body['years'] = $this->academic_model->get_years();
		if($body['years'] == FALSE){
			$tpl['body'] = $this->msg->err($this->academic_model->lasterr);
		} else {
			$tpl['body'] = $this->load->view('academic/years.index.php', $body, TRUE);
		}
		
		$tpl['title'] = 'Academic years';
		$tpl['pagetitle'] = $tpl['title'];
		$this->load->view($this->tpl, $tpl);
	}
	
	
	
	
}


/* End of file /controllers/academic/years.php */