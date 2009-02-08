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


class Rooms extends Controller
{
	
	
	var $tpl;
	
	
	function Rooms(){
		parent::Controller();
		$this->load->model('security');
		$this->load->model('rooms_model');
		$this->load->helper('text');
		$this->tpl = $this->config->item('template');
		$this->output->enable_profiler($this->config->item('profiler'));
	}
	
	
	
	
	/**
	 * Page function: main rooms listing page
	 */
	function index(){
		$this->auth->check('rooms');
		
		$links[] = array('rooms/add', 'Add a new room');
		$links[] = array('rooms/attributes', 'Room attributes');
		$tpl['links'] = $this->load->view('parts/linkbar', $links, TRUE);
		
		$body['rooms'] = $this->rooms_model->get_in_categories();
		$body['cats'] = $this->rooms_model->get_categories_dropdown();
		$body['cats'][-1] = '(Uncategorised)';
		
		if($body['rooms'] == FALSE){
			$tpl['body'] = $this->msg->err($this->rooms_model->lasterr);
		} else {
			$tpl['body'] = $this->load->view('rooms/index', $body, TRUE);
		}
		
		$tpl['title'] = 'Rooms';
		$tpl['pagetitle'] = $tpl['title'];
		$this->load->view($this->tpl, $tpl);
	}
	
	
	
	
	/**
	 * Page function: add a room
	 */
	function add(){
		$this->auth->check('rooms.add');
		$body['room'] = NULL;
		$body['room_id'] = NULL;
		$body['cats'] = $this->rooms_model->get_categories_dropdown();
		
		$body['users'] = $this->security->get_users_dropdown();
		$body['users'][-1] = '(None)';
		
		$tpl['title'] = 'Add room';
		$tpl['pagetitle'] = 'Add a new room';
		$tpl['body'] = $this->load->view('rooms/addedit', $body, TRUE);
		$this->load->view($this->tpl, $tpl);
	}
	
	
	
	
}


?>