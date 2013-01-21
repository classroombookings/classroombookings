<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Classroombookings. Hassle-free resource booking for schools. <http://classroombookings.com/>
 * Copyright (C) Craig A Rodway <craig.rodway@gmail.com>
 *
 * Licensed under the Open Software License version 3.0
 * 
 * This source file is subject to the Open Software License (OSL 3.0) that is
 * bundled with this package in the files license.txt. It is also available 
 * through the world wide web at this URL:
 * http://opensource.org/licenses/OSL-3.0
 */

class Users_model extends School_model
{
	
	
	protected $_table = 'users';
	protected $_primary = 'u_id';
	
	protected $_sch_key = 'g_s_id';
	protected $_join = array('groups', 'u_g_id = g_id');
	
	// Specify the lookup type - where or like or IN - for each filterable parameter/db col
	// If the db column isn't here, we can't filter on it.
	protected $_filter_types = array(
		'where' => array('u_enabled', 'u_g_id'),
		'like' => array('u_username', 'u_display', 'u_email'),
		'in' => array(),
	);
	
	
	function __construct()
	{
		parent::__construct();
	}
	
	
	
	
	/**
	 * Get a single user by their username
	 *
	 * @param string $username		Username of user to get
	 * @return array 		DB row array on success
	 */
	public function get_by_username($username = '')
	{
		$sql = 'SELECT users.*
				FROM users
				LEFT JOIN groups ON u_g_id = g_id
				WHERE u_username = ?
				' . $this->sch_sql() . '
				LIMIT 1';
		
		return $this->db->query($sql, array($username))->row_array();
	}
	
	
	
	
	/**
	 * Set the last log in time for the user to NOW()
	 *
	 * @param int $u_id		ID of user to update the last login time for
	 * @return bool
	 */
	public function set_last_login($u_id = 0)
	{
		$sql = 'UPDATE users
				SET u_last_login = NOW()
				WHERE u_id = ?
				LIMIT 1';
		
		return $this->db->query($sql, array($u_id));
	}
	
	
	
	
	// ---------- Active Users ---------- //
	
	
	
	
	/**
	 * Get all active users
	 */
	public function get_active()
	{
		$sql = 'SELECT
					users.*,
					users_active.*
				FROM
					users_active
				LEFT JOIN
					users
					ON ua_u_id = u_id
				' . $this->join_sql() . '
				WHERE
					1 = 1
				' . $this->sch_sql() . '
				GROUP BY
					ua_u_id
				ORDER BY
					ua_timestamp DESC
				';
		
		return $this->db->query($sql)->result_array();
	}
	
	
	
	
	/**
	 * Insert a new record in the active users table for the given user or update existing entry
	 *
	 * @param int $u_id		ID of user to create a new active entry for
	 * @param string $token		Token string for the user's session
	 * @return mixed		String of token for this session on success
	 */
	public function set_active($u_id = 0, $token = NULL)
	{
		if ($token === NULL)
		{
			$token = sha1(uniqid($u_id, TRUE));
		}
		
		$sql = 'INSERT INTO
					users_active
				SET
					ua_u_id = ?,
					ua_token = ?,
					ua_timestamp = NOW()
				ON DUPLICATE KEY UPDATE
					ua_u_id = VALUES(ua_u_id),
					ua_token = VALUES(ua_token),
					ua_timestamp = NOW()';
		
		return ($this->db->query($sql, array($u_id, $token))) ? $token : FALSE;
	}
	
	
	
	
	/**
	 * Remove an active user entry for the given user and session
	 *
	 * @param int $u_id		ID of user to remove entry for
	 * @param string $token		Token of session to remove
	 * @return bool
	 */
	public function remove_active($u_id = 0, $token = '')
	{
		$sql = 'DELETE FROM users_active
				WHERE ua_u_id = ?
				AND ua_token = ?
				LIMIT 1';
		
		return $this->db->query($sql, array($u_id, $token));
	}
	
	
	
	
	/**
	 * Delete the entries from the active users table where their last activity time is greater than 5 minutes ago
	 *
	 * @return bool
	 */
	public function prune_active()
	{
		$sql = 'DELETE FROM users_active WHERE ua_timestamp <= (NOW() - INTERVAL 5 MINUTE)';
		return $this->db->query($sql);
	}
	
	
	
	
}

/* End of file: ./application/models/users_model.php */