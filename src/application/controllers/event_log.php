<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Classroombookings. Hassle-free resource booking for schools. <http://classroombookings.com/>
 * Copyright (C) 2006-2011 Craig A Rodway <craig.rodway@gmail.com>
 *
 * This file is part of Classroombookings.
 * Classroombookings is licensed under the Affero GNU GPLv3 license.
 * Please see license-classroombookings.txt for the full license text.
 */

class Event_log extends MY_Controller
{
	
	
	function __construct()
	{
		parent::__construct();
		
		$this->lang->load('event_log');
		$this->load->model(array('log_model'));
		$this->load->helper('event_log_helper');
		
		$this->layout->add_breadcrumb(lang('event_log'), 'event_log');
		
		$this->data['nav_current'][] = 'event_log';
		
	}
	
	
	
	
	// =======================================================================
	// Event log index
	// =======================================================================
	
	
	
	
	function index($page = 0)
	{
		$this->auth->restrict('crbs.eventlog.view');
		
		$filter = $this->input->get(NULL, TRUE);
		$filter['pp'] = element('pp', $filter, 50);
		$this->log_model->set_filter($filter);
		
		$this->load->library('pagination');
		$config = array(
			'base_url' => site_url('event_log/index'),
			'total_rows' => $this->log_model->count_all(),
			'per_page' => $filter['pp'],
			'uri_segment' => 3,
			'suffix' => '?' . @http_build_query($filter),
		);
		$this->pagination->initialize($config);
		
		$this->log_model->order_by('l_datetime', 'desc');
		$this->log_model->limit($config['per_page'], $page);
		
		$this->data['filter'] = $filter;
		$this->data['events'] = $this->log_model->get_all();
		
		$this->layout->set_title(lang('event_log'));
		$this->data['subnav_active'] = 'event_log';
		
		$this->data['areas'] = $this->log_model->get_areas();
		$this->data['types'] = $this->log_model->get_types();
		
		$this->session->set_return_uri('log');
	}
	
	
	
	
	public function view($l_id = 0)
	{
		$this->layout->add_breadcrumb(lang('event_log_view_event'), 'event_log/view/' . $l_id);
		$this->data['event'] = $this->log_model->get($l_id);
	}
	
	
	
	
}


/* End of file ./application/controllers/event_log.php */