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
	
	
	
	
	function get($param, $type = 'main'){
		$sql = sprintf('SELECT %s FROM `settings-%s` LIMIT 1', $param, $type);
		$query = $this->CI->db->query($sql);
		if($query->num_rows() == 1){
			$row = $query->row();
			return $row->$param;
		} else {
			return FALSE;
		}	
	}
	
	
	
	
	function get_all($type){
		switch($type){
			case 'main':
				$sql = 'SELECT * FROM `settings-main` LIMIT 1';
				break;
			case 'auth':
				$sql = 'SELECT * FROM `settings-auth` LIMIT 1';
				break;
			default:
				$sql = NULL;
				$this->lasterr = 'The settings type you loaded was not valid.';
				break;
		}
		
		if($sql == NULL){
			return FALSE;
		} else {
			$query = $this->CI->db->query($sql);
			if($query->num_rows() == 1){
				// Got data ok
				#$row = $query->row();
				return $query->row();
			} else {
				$this->lasterr = 'No results found!';
				return FALSE;
			}
		}
	}
	
	
	
	
	function save($type = NULL, $data = NULL){
		if($data == NULL || !is_array($data)){
			$this->lasterr = 'Save function was not called with a data array.';
			return FALSE;
		}
		
		if($type == NULL){
			$this->lasterr = 'The save function was given some data, but the type of information is unknown or has not been specified.';
			return FALSE;
		}
		
		switch($type){
			case 'main': $table = 'settings-main'; break;
			case 'auth': $table = 'settings-auth'; break;
			default: $this->lasterr = 'No valid type of data was specified.'; return FALSE; break;
		}
		
		return $this->CI->db->update($table, $data);
	}
	
	
	
	
	function getldap(){
		$result1 = array();
		$result2 = array();
		
		$sql = 'SELECT * FROM `settings-ldap`LIMIT 1';
		$query1 = $this->CI->db->query($sql);
		
		if($query1->num_rows() == 1){
			$result1 = $query1->result_array();
		}

		$sql = 'SELECT * FROM `settings-ldap-rdns`';
		$query2 = $this->CI->db->query($sql);
		
		if($query2->num_rows() > 0){
			$result2 = $query2->result_array();
		}
			
		$result['ldap'] = $result1;
		$result['rnds'] = $result2;
		
		return $result;
		
	}
	
	
	
	
}
?>
