<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Users_model extends CI_Model
{


	public function __construct()
	{
		parent::__construct();
	}


	/**
	 * Get an active/enabled user by the given username.
	 *
	 * @param  string $username Username
	 * @return  mixed FALSE on failure or DB row on success.
	 *
	 */
	public function get_by_username($username, $require_enabled = TRUE)
	{
		if ( ! strlen($username)) {
			return FALSE;
		}

		$where = [
			'username' => $username,
		];

		if ($require_enabled) {
			$where['enabled'] = 1;
		}

		$query = $this->db->get_where('users', $where, 1);

		if ($query->num_rows() === 1) {
			return $query->row();
		}

		return FALSE;
	}


	/**
	 * Get an active/enabled user by the given ID.
	 *
	 * @param  int $id User ID
	 * @return  mixed FALSE on failure or DB row on success.
	 *
	 */
	public function get_by_id($id)
	{
		$where = [
			'user_id' => $id,
			'enabled' => 1,
		];

		$query = $this->db->get_where('users', $where, 1);

		if ($query->num_rows() === 1) {
			return $query->row();
		}

		return FALSE;
	}


	/**
	 * Set a user password.
	 *
	 * @param  mixed $user ID or User row object.
	 * @param  string $password New password to set.
	 *
	 */
	public function set_password($user, $password)
	{
		if (is_object($user)) {
			$user_id = $user->user_id;
		} elseif (is_numeric($user)) {
			$user_id = $user;
		}

		if ( ! isset($user_id)) {
			return FALSE;
		}

		$user_data = [
			'password' => password_hash($password, PASSWORD_DEFAULT),
		];

		$where = [ 'user_id' => $user_id ];

		return $this->db->update('users', $user_data, $where);
	}


	public function insert($user_data = [])
	{
		$insert = $this->db->insert('users', $user_data);
		return ($insert ? $this->db->insert_id() : FALSE);
	}


	public function update($user_data = [], $where)
	{
		if ( ! is_array($where)) {
			$where = ['user_id' => (int) $where];
		}

		return $this->db->update('users', $user_data, $where);
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
