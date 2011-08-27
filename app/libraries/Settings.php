<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Classroombookings. Hassle-free resource booking for schools. <http://classroombookings.com/>
 * Copyright (C) 2006-2011 Craig A Rodway <craig.rodway@gmail.com>
 *
 * This file is part of Classroombookings.
 * Classroombookings is licensed under the Affero GNU GPLv3 license.
 * Please see license-classroombookings.txt for the full license text.
 */


class Settings{


	var $CI;
	var $lasterr;
	
	private $_settings;
	
	
	function Settings(){
		// Load original CI object
		$this->CI =& get_instance();
		// Get all settings and store in local array
		$this->init();
	}
	
	
	
	
	/**
	 * Pull all settings from DB and store in local array
	 */
	function init()
	{
		$settings = array();
		$this->CI->db->select('*', FALSE);
		$this->CI->db->from('settings');
		$query = $this->CI->db->get();
		if ($query->num_rows() > 0)
		{
			$result = $query->result();
			foreach($result as $r)
			{
				$settings[$r->key] = $r->value;
			}
			
		}
		$this->settings = $settings;
		if (is_array($this->settings))
		{
			log_message('debug', 'Settings have been initialised');
		}
	}
	
	
	
	
	/**
	 * Get one or mroe setting values
	 *
	 * @param string|array $key One setting name or 1D array of keys
	 * @return string|array String value, or 2D array of keys => values
	 */
	function get($key)
	{
		if (is_array($key))
		{
			$ret = array();
			foreach($key as $k)
			{
				$ret[$k] = $this->_get_one($k);
			}
			return $ret;
		}
		else
		{
			return $this->_get_one($key);
		}
	}
	
	
	
	private function _get_one($key)
	{
		if (array_key_exists($key, $this->settings))
		{
			return $this->settings[$key];
		} else {
			return false;
		}
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
	/*
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
		
	}*/
	
	
	
	
	/**
	 * Save one or more settings
	 *
	 * @param string|array $key One key name, or 2D array of keys => values
	 * @param string $value Value of $key if saving only one item
	 * @return bool
	 */
	function save($key = NULL, $value = NULL)
	{
		// Check first parameter type
		
		if(is_array($key))
		{
			// $key is actually an array of settings
			$sql = 'REPLACE INTO settings (`key`, value) VALUES ';
			foreach($key as $k => $v){
				$sql .= sprintf("('%s', '%s'),", $k, $v);
			}
			$sql = preg_replace('/,$/', '', $sql);
			$query = $this->CI->db->query($sql);
			return ($query) ? true : false;
		}
		else
		{
			// One key, one value.
			if($key != NULL && $value != NULL)
			{
				$sql = 'REPLACE INTO settings (`key`, value) VALUES (?, ?)';
				$query = $this->CI->db->query($sql, array($key, $value));
				return ($query) ? true : false;
			}
			else
			{
				$this->lasterr = 'Settings not supplied in correct format.';
				return false;
			}
		}	// else is_array($key)
	}
	
	
	
	
}
?>
