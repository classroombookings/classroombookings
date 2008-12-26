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
		$this->load->model('weeks_model');
		$this->tpl = $this->config->item('template');
		$this->output->enable_profiler($this->config->item('profiler'));
	}
	
	
	
	
	
	function index(){
		$this->auth->check('weeks');
		
		$links[0] = array('academic/weeks/add', 'Add a new week');
		$links[1] = array('academic/main', 'Academic setup');
		$tpl['links'] = $this->load->view('parts/linkbar', $links, TRUE);
		
		// Get list of weeks
		$body['weeks'] = $this->weeks_model->get();
		if($body['weeks'] == FALSE){
			$tpl['body'] = $this->msg->err($this->weeks_model->lasterr);
		} else {
			$tpl['body'] = $this->load->view('weeks/index', $body, TRUE);
		}
		
		$tpl['title'] = 'Weeks';
		$tpl['pagetitle'] = $tpl['title'];
		$this->load->view($this->tpl, $tpl);
	}
	
	
	
	
}


/* End of file app/controllers/academic/weeks.php */