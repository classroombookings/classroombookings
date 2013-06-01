<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Classroombookings. Hassle-free resource booking for schools. <http://classroombookings.com/>
 * Copyright (C) 2006-2011 Craig A Rodway <craig.rodway@gmail.com>
 *
 * This file is part of Classroombookings.
 * Classroombookings is licensed under the Affero GNU GPLv3 license.
 * Please see license-classroombookings.txt for the full license text.
 */

class Departments extends Configure_Controller
{
	
	
	function __construct()
	{
		parent::__construct();
		
		$this->lang->load('departments');
		$this->lang->load('configure');
		$this->load->model(array('departments_model', 'ldap_groups_model'));
		$this->load->helper('department_helper');
		
		$this->layout->add_breadcrumb(lang('configure_departments'), 'departments');
		
		$this->data['subnav'] = array(
			array(
				'uri' => 'departments',
				'text' => lang('configure_departments'),
				'test' => $this->auth->check('departments.view'),
			),
			array(
				'uri' => 'departments/set',
				'text' => lang('departments_add_new'),
				'test' => $this->auth->check('departments.add'),
			),
		);
		
	}
	
	
	
	
	// =======================================================================
	// Department management
	// =======================================================================
	
	
	
	
	function index($page = 0)
	{
		$this->auth->restrict('departments.view');
		
		$filter = $this->input->get(NULL, TRUE);
		$filter['pp'] = element('pp', $filter, 10);
		$this->departments_model->set_filter($filter);
		
		$this->load->library('pagination');
		$config = array(
			'base_url' => site_url('departments/index'),
			'total_rows' => $this->departments_model->count_all(),
			'per_page' => $filter['pp'],
			'uri_segment' => 3,
			'suffix' => '?' . @http_build_query($filter),
		);
		$this->pagination->initialize($config);
		
		$this->departments_model->order_by('d_name', 'asc');
		$this->departments_model->limit($config['per_page'], $page);
		
		$this->data['filter'] = $filter;
		$this->data['departments'] = $this->departments_model->get_all();
		
		$this->layout->set_title(lang('configure_departments'));
		$this->data['subnav_active'] = 'departments';
		
		$this->session->set_return_uri('d');
	}
	
	
	
	
	public function set($d_id = 0)
	{
		if ($d_id)
		{
			// Updating department $d_id
			$this->auth->restrict('departments.edit');
			$this->data['department'] = $this->departments_model->get($d_id);
			$title = lang('departments_edit');
			$this->layout->add_breadcrumb(lang('departments_edit'), 'departments/set/' . $d_id);
		}
		else
		{
			// Adding new department
			$this->auth->restrict('departments.add');
			$this->data['department'] = array();
			$title = lang('departments_add_new');
			$this->layout->add_breadcrumb(lang('departments_add_new'), 'departments/set');
			$this->data['subnav_active'] = 'departments/set';
		}
		
		if ($this->input->post())
		{
			$this->form_validation->set_rules(array(
				array('field' => 'd_name', 'label' => lang('departments_department_name'), 'rules' => 'required|max_length[64]|trim'),
				array('field' => 'd_description', 'label' => lang('departments_department_description'), 'rules' => 'max_length[255]|trim'),
				array('field' => 'd_colour', 'label' => lang('departments_department_colour'), 'rules' => 'min_length[6]|max_length[7]|trim'),
			));
			
			
			if ($this->form_validation->run())
			{
				$department_data = array(
					'd_name' => $this->input->post('d_name'),
					'd_description' => $this->input->post('d_description'),
					'd_colour' => '#' . str_replace('#', '', $this->input->post('d_colour')),
				);
				
				if ($d_id)
				{
					// Update
					$d_id = $this->departments_model->update($d_id, $department_data);
					$success = sprintf(lang('departments_update_success'), $department_data['d_name']);
					$error = sprintf(lang('departments_update_error'), $department_data['d_name']);
					$event = 'department_update';
				}
				else
				{
					// Insert
					$d_id = $this->departments_model->insert($department_data);
					$success = sprintf(lang('departments_insert_success'), $department_data['d_name']);
					$error = sprintf(lang('departments_insert_error'), $department_data['d_name']);
					$event = 'department_insert';
				}
				
				if ($d_id)
				{
					// Success
					
					// Set LDAP groups membership
					$this->departments_model->set_ldap_groups($d_id, $this->input->post('ldap_groups'));
					
					Events::trigger($event, array(
						'd_id' => $d_id,
						'department' => $department_data,
					));
					
					$this->flash->set('success', $success, TRUE);
					redirect($this->session->get_return_uri('d', 'departments'));
				}
				else
				{
					$this->flash->set('error', $error);
				}
				
			}  // end validation->run()
			
		}  // end POST check
		
		$this->layout->set_css('colorPicker', 'vendor/rscp/');
		$this->layout->set_js('jquery.colorPicker.min', 'vendor/rscp/');
		$this->data['ldap_groups'] = $this->ldap_groups_model->dropdown('lg_name');
		
		$this->load->library('form');
	}
	
	
	
	
	/**
	 * Delete a department
	 */
	function delete()
	{
		$this->auth->restrict('departments.delete');
		
		$id = $this->input->post('id');
		
		if ( ! $id)
		{
			redirect('departments');
		}
		
		$department = $this->departments_model->get($id);
		
		if ($this->departments_model->delete($id))
		{
			$this->flash->set('success', lang('departments_delete_success'), TRUE);
			
			Events::trigger('department_delete', array(
				'd_id' => $id,
				'department' => $department,
			));
		}
		else
		{
			$this->flash->set('error', lang('departments_delete_error'), TRUE);
		}
		
		redirect($this->input->post('redirect'));
	}
	
	
	
	
}


/* End of file ./application/controllers/departments.php */