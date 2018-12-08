<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Holidays_model extends CI_Model
{


	public function __construct()
	{
		parent::__construct();
	}


	function Get($holiday_id = NULL)
	{
		if ($holiday_id == NULL) {
			return $this->crud_model->Get('holidays', NULL, NULL, NULL, 'date_start asc, date_end asc');
		} else {
			return $this->crud_model->Get('holidays', 'holiday_id', $holiday_id);
		}
	}


	function Add($data)
	{
		return $this->crud_model->Add('holidays', 'holiday_id', $data);
	}


	function Edit($holiday_id, $data)
	{
		return $this->crud_model->Edit('holidays', 'holiday_id', $holiday_id, $data);
	}


	/**
	 * Deletes a week with the given ID
	 *
	 * @param   int   $id   ID of week to delete
	 *
	 */
	function delete($id)
	{
		$this->db->where('holiday_id', $id);
		$this->db->delete('holidays');
	}


}
