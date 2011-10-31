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
		$this->load->model('security_model');
		$this->load->model('departments_model');
		$this->load->helper('text');
	}
	
	
	
	
	/**
	 * PAGE: Departments listing
	 */
	function index()
	{
		$this->auth->check('departments.view');
		
		// Get list of departments
		$body['departments'] = $this->departments_model->get();
		
		if ($body['departments'] == false)
		{
			$data['body'] = $this->msg->notice('No departments found. ' . $this->departments_model->lasterr);
		}
		else
		{
			$data['body'] = $this->load->view('departments/index', $body, true);
		}
		
		$data['title'] = 'Departments';
		$data['submenu'] = $this->menu_model->departments();
		
		$this->page($data);
	}
	
	
	
	
	/**
	 * PAGE: Add a new department
	 */
	function add()
	{
		$this->auth->check('departments.add');
		$body['department'] = null;
		$body['department_id'] = null;
		$body['ldapgroups'] = $this->security_model->get_ldap_groups();
		$data['title'] = 'Add new department';
		$data['body'] = $this->load->view('departments/addedit', $body, true);
		$this->page($data);
	}
	
	
	
	
	/**
	 * PAGE: Edit a department
	 */
	function edit($department_id)
	{
		$this->auth->check('departments.edit');
		
		$body['department'] = $this->departments_model->get($department_id);
		$body['department_id'] = $department_id;
		$body['ldapgroups'] = $this->security_model->get_ldap_groups();
		
		$data['title'] = 'Edit department';
		
		if ($body['department'])
		{
			$data['title'] = 'Edit department: ' . $body['department']->name;
			$data['body'] = $this->load->view('departments/addedit', $body, true);
		}
		else
		{
			$data['title'] = 'Error loading department';
			$data['body'] = $this->msg->err('Could not load requested department. ' . $this->departments_model->lasterr);
		}
		
		$this->page($data);
	}
	
	
	
	
	/**
	 * FORM DESTINATION: Add/Edit a department
	 */
	function save()
	{
		
		$department_id = $this->input->post('department_id');
		
		if ($department_id == null)
		{
			$this->auth->check('departments.add');
		}
		else
		{
			$this->auth->check('departments.edit');
		}
		
		$this->form_validation->set_rules('department_id', 'Department ID');
		$this->form_validation->set_rules('name', 'Name', 'required|max_length[64]|trim');
		$this->form_validation->set_rules('description', 'Description', 'max_length[255]|trim');
		$this->form_validation->set_rules('colour', 'Colour', 'max_length[7]');
		$this->form_validation->set_rules('ldapgroups[]', 'LDAP Groups');
		$this->form_validation->set_error_delimiters('<li>', '</li>');
		
		if ($this->form_validation->run() == false)
		{
			// Validation failed - load required action depending on the state of user_id
			return ($department_id == null) ? $this->add() : $this->edit($department_id);
		}
		else
		{
			// Validation OK
			$data['name'] = $this->input->post('name');
			$data['description'] = $this->input->post('description');
			$data['colour'] = $this->input->post('colour');
			$data['ldapgroups'] = ($this->input->post('ldapgroups')) 
				? $this->input->post('ldapgroups')
				: array();
			
			if ($department_id == null)
			{
				// Add a new department
				$add = $this->departments_model->add($data);
				if ($add == true)
				{
					$msg = sprintf(lang('DEPARTMENTS_ADD_OK'), $data['name']);
					$this->msg->add('notice', $msg);
				}
				else
				{
					$msg = sprintf(lang('DEPARTMENTS_ADD_FAIL'), $this->departments_model->lasterr);
					$this->msg->add('err', $msg);
				}
			}
			else
			{			
				// Updating existing department
				$edit = $this->departments_model->edit($department_id, $data);
				if ($edit == true)
				{
					$msg = sprintf(lang('DEPARTMENTS_EDIT_OK'), $data['name']);
					$this->msg->add('notice', $msg);
				}
				else
				{
					$msg = sprintf(lang('DEPARTMENTS_EDIT_FAIL'), $this->departments_model->lasterr);
					$this->msg->add('err', $msg);
				}
			}
			
			// All done, redirect!
			redirect('departments');
		}
	}
	
	
	
	
	/**
	 * Delete a deparment
	 */
	function delete($department_id = null)
	{
		$this->auth->check('departments.delete');
		
		// Check if a form has been submitted; if not - show it to ask user confirmation
		if ($this->input->post('id'))
		{
			// Form has been submitted (so the POST value exists)
			// Call model function to delete department
			$delete = $this->departments_model->delete($this->input->post('id'));
			if ($delete == false)
			{
				$this->msg->add('err', $this->departments_model->lasterr, 'An error occured');
			}
			else
			{
				$this->msg->add('info', 'The department has been deleted.');
			}
			// Redirect
			redirect('departments');
		}
		else
		{
			if ($department_id == null)
			{
				$data['title'] = 'Delete department';
				$data['body'] = $this->msg->err('Cannot find the department or no department ID given.');
			}
			else
			{
				// Get department info so we can present the confirmation page with a name
				$department = $this->departments_model->get($department_id);
				if ($department == false)
				{
					$data['title'] = 'Delete department';
					$data['body'] = $this->msg->err('Could not find that department or no department ID given.');
				}
				else
				{
					// Initialise page
					$body['action'] = 'departments/delete';
					$body['id'] = $department_id;
					$body['cancel'] = 'departments';
					$body['text'] = 'If you delete this department, all people assigned to it will be removed, and any role assignments will also be affected.';
					$body['title'] = 'Are you sure you want to delete department ' . $department->name . '?';
					$data['title'] = 'Delete department: ' . $department->name;
					$data['body'] = $this->load->view('parts/deleteconfirm', $body, true);
				}
			}
			$this->page($data);
		}
	}

	
	
	
	
}


/* End of file app/controllers/departments.php */