<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Users_model extends CI_Model
{


	public function __construct()
	{
		parent::__construct();
	}


	function Get($user_id = NULL, $pp = 10, $start = 0)
	{
		if ($user_id == NULL) {
			return $this->crud_model->Get('users', NULL, NULL, NULL, 'enabled asc, username asc', $pp, $start);
		} else {
			return $this->crud_model->Get('users', 'user_id', $user_id);
		}
	}


	function Add($data)
	{
		$query = $this->db->insert('users', $data);
		return ($query ? $this->db->insert_id() : $query);
	}


	function Edit($user_id, $data)
	{
		$this->db->where('user_id', $user_id);
		$result = $this->db->update('users', $data);
		return ($result ? $user_id : FALSE);
	}


	/**
	 * Delete a user
	 *
	 * @param   int   $id   ID of user to delete
	 *
	 */
	function Delete($id)
	{
		$this->db->where('user_id', $id);
		$this->db->delete('bookings');

		$set = array('user_id' => 0);
		$where = array('user_id' => $id);
		$this->db->update('rooms', $set, $where);

		$this->db->where('user_id', $id);
		return $this->db->delete('users');
	}


}
