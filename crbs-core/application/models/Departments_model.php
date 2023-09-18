<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Departments_model extends CI_Model
{


	public function __construct()
	{
		parent::__construct();
		$this->load->model('crud_model');
	}


	function Get($department_id = NULL, $pp = 10, $start = 0)
	{
		if ($department_id == NULL) {
			return $this->crud_model->Get('departments', NULL, NULL, NULL, 'name asc', $pp, $start);
		} else {
			return $this->crud_model->Get('departments', 'department_id', $department_id);
		}
	}



	public function insert($data = [])
	{
		$insert = $this->db->insert('departments', $data);
		return ($insert ? $this->db->insert_id() : FALSE);
	}


	public function update($department_id, $data = [])
	{
		$where = ['department_id' => $department_id];

		return $this->db->update('departments', $data, $where);
	}


	public function delete($department_id)
	{
		return $this->db->delete('departments', ['department_id' => $department_id]);
	}


}
