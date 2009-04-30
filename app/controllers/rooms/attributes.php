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
		
		$links[] = array('rooms/attributes/add', 'Add a new field');
		$tpl['links'] = $this->load->view('parts/linkbar', $links, TRUE);
		
		// Get list of room attributes
		$body['attrs'] = $this->rooms_model->get_all_attr_fields();
		if($body['attrs'] == FALSE){
			$tpl['body'] = $this->msg->err($this->rooms_model->lasterr);
		} else {
			$tpl['body'] = $this->load->view('rooms/attributes/index', $body, TRUE);
		}
		
		$tpl['subnav'] = $this->rooms_model->subnav();
		$tpl['title'] = 'Rooms';
		$tpl['pagetitle'] = $tpl['title'];
		$this->load->view($this->tpl, $tpl);
		
	}
	
	
	
	
	function add(){
		
		$this->auth->check('rooms.attrs');
		
	}
	
	
	
	
	function edit($field_id){
		
		$this->auth->check('rooms.attrs');
		
	}


}

/* End of file: /app/controllers/rooms/attributes.php */