<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
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


class Settings{


	var $CI;
	var $lasterr;


	function Settings(){
		// Load original CI object
		$this->CI =& get_instance();
	}
	
	
	
	
	/**
	 * Get setting(s)
	 *
	 * $param:	a) NULL = get all settings.
	 *			b) auth. = get all auth.* settings
	 *			c) auth.ldap = get one setting
	 *
	 * @param	mixed	param	Parameter to get if desired
	 * @return mixed	array (many) or string (single)
	 */
	function get($param = NULL){
		
		if($param != NULL && preg_match('/\.$/', $param) === 1){
			// Search for multiple settings
			$sql = "SELECT * FROM settings WHERE `key` LIKE '%s%%'";
			$sql = sprintf($sql, $param);
			$query = $this->CI->db->query($sql);
		}
		
		if($param == NULL OR isset($sql)){
			
			// Initialise array for settings
			$settings = array();
		
			// get all, if no query previously created
			if(!isset($sql)){
				$sql = 'SELECT * FROM settings';
				$query = $this->CI->db->query($sql);
			}
			
			// Result of query (should have multiple rows)
			if($query->num_rows() > 0){
				$result = $query->result();
				foreach($result as $row){
					$settings[$row->key] = $row->value;
				}
				return $settings;
			} else {
				$this->lasterr = 'No settings to retrieve.';
				return FALSE;
			}
			
		} elseif($param != NULL){
		
			// Get one			
			$sql = 'SELECT value FROM settings WHERE `key` = ? LIMIT 1';
			$query = $this->CI->db->query($sql, array($param));
			if($query->num_rows == 1){
				$row = $query->row();
				return $row->value;
			} else {
				$this->lasterr = sprintf('Could not retrieve setting s.', $param);
				return FALSE;
			}
			
		}
		
	}
	
	
	
	
	function save($key = NULL, $value = NULL){
		
		// Check first parameter type
		
		if(is_array($key)){

			// $key is actually an array of settings
			$sql = 'REPLACE INTO settings (`key`, value) VALUES ';
			foreach($key as $k => $v){
				$sql .= sprintf("('%s', '%s'),", $k, $v);
			}
			$sql = preg_replace('/,$/', '', $sql);
			$query = $this->CI->db->query($sql);
			return $query;
			
		} else {
			
			// One key, one value.
			if($key != NULL && $value != NULL){
				
				$sql = 'REPLACE INTO settings (`key`, value) VALUES (?, ?)';
				$query = $this->CI->db->query($sql, array($key, $value));
				return $query;
				
			} else {
				
				$this->lasterr = 'Settings not supplied in correct format.';
				return FALSE;
			
			}
		
		}
		
	}
	
	
	
	
}
?>
