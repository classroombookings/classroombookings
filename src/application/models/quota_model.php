<?php
/*
	This file is part of Classroombookings.

	Classroombookings is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	Classroombookings is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with Classroombookings.  If not, see <http://www.gnu.org/licenses/>.
*/


class Quota_model extends CI_Model{
	
	
	var $lasterr;
	
	
	function __construct(){
		parent::__construct();
	}
	
	
	
	
	/**
	 * Update booking quota for a user
	 *
	 * @param	int		user_id		User ID
	 * @param	int		quota_num	Quota amount to set
	 * @return	bool	True on success
	 */
	function set_quota_u($user_id, $quota_num){
		
		if(!is_numeric($user_id)){
			$this->lasterr = 'Invalid User ID';
			return FALSE;
		}
		
		if(!is_numeric($quota_num)){
			$this->lasterr = 'Quota number not valid';
			return FALSE;
		}
		
		$sql = 'REPLACE INTO quota (user_id, quota_num) VALUES (?, ?)';
		
		$query = $this->db->query($sql, array($user_id, $quota_num));
		
		return ($this->db->affected_rows() == 1) ? TRUE : FALSE;
	}
	
	
	
	
	/**
	 * Update booking quota for all users in a group
	 *
	 * @param	int		group_id	Group ID
	 * @param	int		quota_num	Quota amount to set
	 * @return	bool	True on success, False if no rows updated
	 */
	function set_quota_g($group_id, $quota_num){
		
		if(!is_numeric($group_id)){
			$this->lasterr = 'Invalid User ID';
			return FALSE;
		}
		
		if(!is_numeric($quota_num)){
			$this->lasterr = 'Quota number not valid';
		}
		
		$sql = 'REPLACE INTO quota (user_id, quota_num)
				SELECT user_id, ? FROM users WHERE group_id = ?';
		
		$query = $this->db->query($sql, array($quota_num, $group_id));
		
		return ($this->db->affected_rows() > 0) ? TRUE : FALSE;
		
	}
	
	
	
	
	/**
	 * Reset the quotas for users in the group to the group default
	 */
	function reset_group($group_id){
		
		$sql = 'REPLACE INTO quota (user_id, quota_num)
				SELECT users.user_id, groups.quota_num
				FROM users, groups
				WHERE groups.group_id = ?';
		
		$query = $this->db->query($sql, array($group_id));
		
		return ($this->db->affected_rows() > 0) ? TRUE : FALSE;
		
	}
	
	
	
	
}




/* End of file: app/models/account_model.php */