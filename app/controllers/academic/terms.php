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
	
	}
	
	
	
	
}


/* End of file app/controllers/academic/terms.php */