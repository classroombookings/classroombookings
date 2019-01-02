<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Departments_model extends CI_Model
{


	public function __construct()
	{
		parent::__construct();
	}


	function Get($department_id = NULL, $pp = 10, $start = 0)
	{
		if ($department_id == NULL) {
			return $this->crud_model->Get('departments', NULL, NULL, NULL, 'name asc', $pp, $start);
		} else {
			return $this->crud_model->Get('departments', 'department_id', $department_id);
		}
	}


	function Add($data)
	{
		return $this->crud_model->Add('departments', 'department_id', $data);
	}


	function Edit($department_id, $data)
	{
		return $this->crud_model->Edit('departments', 'department_id', $department_id, $data);
	}


	/**
	 * Deletes a department
	 *
	 * @param   int   $id   ID of department to delete
	 *
	 */
	function Delete($id)
	{
		$this->db->where('department_id', $id);
		return $this->db->delete('departments');
	}




}
