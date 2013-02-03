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
	
	
	
	
	public function get($u_id = 0)
	{
		$user = parent::get($u_id);
		
		if ($user)
		{
			$user['departments'] = $this->get_user_departments($u_id);
		}
		
		return $user;
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
	
	
	
	
	/**
	 * Handle the import of user accounts from CSV file.
	 *
	 * @param array $users		Array of user account details
	 * @return array 		Array of import results
	 */
	public function import($users = array(), $existing_action = 'skip')
	{
		// Existing usernames to check against
		$existing_users = $this->dropdown('u_username', 'u_id');
		
		// Status counters
		$total = count($users);
		$ignored = 0;
		$skipped = 0;
		$added = 0;
		$updated = 0;
		$failed = 0;
		
		foreach ($users as &$user)
		{
			// Ignore the user if it should not be imported
			if ( (int) $user['import'] !== 1)
			{
				$ignored++;
				$user['action'] = 'ignored';
				continue;
			}
			
			// Default action to carry out
			$action = 'insert';
			
			// Check for existing user
			if (array_key_exists($user['u_username'], $existing_users))
			{
				if ($existing_action === 'skip')
				{
					// Existing users should be skipped
					$skipped++;
					$user['action'] = 'skipped';
					continue;
				}
				elseif ($existing_action === 'update')
				{
					// Existing users should be updated
					$user['action'] = 'updated';
					$action = 'update';
				}
			}
			
			// Array of data specific to the table
			$user_data = array(
				'u_username' => $user['u_username'],
				'u_password' => $this->auth->local->hash_password($user['u_password']),
				'u_display' => $user['u_display'],
				'u_email' => $user['u_email'],
				'u_enabled' => (int) $user['u_enabled'],
				'u_g_id' => (int) $user['u_g_id'],
				'u_auth_method' => 'local',
			);
			
			if ($action === 'insert')
			{
				$u_id = $this->insert($user_data);
				if ($u_id)
				{
					$user['action'] = 'added';
					$user['u_id'] = $u_id;
					$added++;
				}
				else
				{
					$user['action'] = 'failed';
					$failed++;
				}
			}
			elseif ($action === 'update')
			{
				// Don't update their password
				unset($user_data['u_password']);
				
				$u_id = $existing_users[$user['u_username']];
				$u_id = $this->update($u_id, $user_data);
				
				if ($u_id)
				{
					$user['action'] = 'updated';
					$user['u_id'] = $u_id;
					$updated++;
				}
				else
				{
					$user['action'] = 'failed';
					$failed++;
				}
			}
			
			if ($user['action'] !== 'failed' && ! empty($user['d_id']))
			{
				// Add them to the department
				$sql = 'INSERT INTO u2d
						SET u2d_u_id = ?, u2d_d_id = ?
						ON DUPLICATE KEY UPDATE
						u2d_u_id = VALUES(u2d_u_id), u2d_d_id = VALUES(u2d_d_id)';
				$this->db->query($sql, array($u_id, $user['d_id']));
			}
		}
		
		// Return status array
		return array(
			'users' => $users,
			'ignored' => $ignored,
			'skipped' => $skipped,
			'added' => $added,
			'updated' => $updated,
			'failed' => $failed,
		);
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
	
	
	
	
	// ---------- Departments ---------- //
	
	
	
	
	/**
	 * Get simple list of department IDs => names that a user belongs to
	 */
	public function get_user_departments($u_id = 0)
	{
		$sql = 'SELECT
					d.*
				FROM
					u2d
				LEFT JOIN
					departments d ON u2d_d_id = d_id
				WHERE
					u2d_u_id = ?
				AND
					d_s_id = ?
				ORDER BY
					d_name ASC';
		
		$result = $this->db->query($sql, array($u_id, $this->config->item('s_id')))->result_array();
		
		$departments = array();
		
		if ($result)
		{
			foreach ($result as $row)
			{
				$departments[$row['d_id']] = $row['d_name'];
			}
		}
		
		return $departments;
	}
	
	
	
	
	/**
	 * Sets a user's department memberships
	 *
	 * @param int $u_id		ID of user to update departments for
	 * @param array $d_ids		1D array of department IDs to set for user
	 * @return bool
	 */
	public function set_user_departments($u_id = 0, $d_ids = array())
	{
		$sql = 'DELETE FROM u2d WHERE u2d_u_id = ?';
		$this->db->query($sql, array($u_id));
		
		if ( ! empty($d_ids))
		{
			$values = array();
			
			foreach ($d_ids as $d_id)
			{
				$values[] = '(' . $u_id . ', ' . (int) $d_id . ')';
			}
			
			$sql = 'INSERT INTO u2d (u2d_u_id, u2d_d_id) VALUES ' . implode(',', $values);
			//echo $sql; die();
			return $this->db->query($sql);
		}
		
		return TRUE;
	}
	
	
	
	
}

/* End of file: ./application/models/users_model.php */