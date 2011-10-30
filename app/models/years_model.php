<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Classroombookings. Hassle-free resource booking for schools. <http://classroombookings.com/>
 * Copyright (C) 2006-2011 Craig A Rodway <craig.rodway@gmail.com>
 *
 * This file is part of Classroombookings.
 * Classroombookings is licensed under the Affero GNU GPLv3 license.
 * Please see license-classroombookings.txt for the full license text.
 */


class Years_model extends CI_Model
{
	
	
	function __construct()
	{
		parent::__construct();
	}
	
	
	
	
	/**
	 * Get one or more academic years
	 *
	 * @param int week_id
	 * @param arr pagination limit,start
	 * @return mixed (object on success, false on failure)
	 */
	function get($year_id = null, $page = null){
		
		if ($year_id == null)
		{
		
			// Getting all years
			$this->db->select('*', FALSE);
			$this->db->from('years');
			
			$this->db->order_by('date_start ASC');
			
			if(isset($page) && is_array($page)){
				$this->db->limit($page[0], $page[1]);
			}
			
			$query = $this->db->get();
			if ($query->num_rows() > 0){
				return $query->result();
			} else {
				$this->lasterr = 'There are no academic years defined.';
				return 0;
			}
			
		} else {
			
			if (!is_numeric($year_id)) {
				return FALSE;
			}
			
			// Getting one year
			$sql = 'SELECT * FROM years WHERE year_id = ? LIMIT 1';
			$query = $this->db->query($sql, array($year_id));
			
			if($query->num_rows() == 1){
				// Got the year
				$year = $query->row();				
				return $year;
			} else {
				return FALSE;
			}
			
		}
		
	}
	
	
	
	
	function add($data){
		
		$active = FALSE;
		// Use the proper function to make it active (to de-activate other years)
		if($data['active'] == 1){
			// Set flag to check to activate it later as we don't have the ID yet
			$active = TRUE;
		}
		$data['active'] = NULL;
		
		$add = $this->db->insert('years', $data);
		$year_id = $this->db->insert_id();
		
		if($active == TRUE){
			// Now we have the ID of the new year, and we need to make it active
			$this->activate($year_id);
		}
		
		return $year_id;
	}
	
	
	
	
	function edit($year_id = NULL, $data){
		if($year_id == NULL){
			$this->lasterr = 'Cannot update a year without its ID.';
			return FALSE;
		}
		
		$active = FALSE;
		// Use the proper function to make it active (to de-activate other years)
		if($data['current'] == 1){
			$active = true;
		}
		$data['current'] = null;
		
		// Update year info
		$this->db->where('year_id', $year_id);
		$edit = $this->db->update('years', $data);
		
		if($active == TRUE){
			$this->activate($year_id);
		}
		
		return $edit;
	}
	
	
	
	
	function delete($year_id){
		
		$sql = 'DELETE FROM years WHERE year_id = ? LIMIT 1';
		$query = $this->db->query($sql, array($year_id));
		
		if($query == FALSE){
			
			$this->lasterr = 'Could not delete year. Does it exist?';
			return FALSE;
			
		} else {
			
			/* $sql = 'DELETE FROM bookings WHERE user_id = ?';
			$query = $this->db->query($sql, array($user_id));
			if($query == FALSE){ $failed[] = 'bookings'; }*/
			
			/*$sql = 'UPDATE rooms SET user_id = NULL WHERE user_id = ?';
			$query = $this->db->query($sql, array($user_id));
			if($query == FALSE){ $failed[] = 'rooms'; }
			
			if(isset($failed)){
				$this->lasterr = 'The user was deleted successfully, but an error occured while removing their bookings and/or updating any rooms they owned.';
			}*/
			
			return TRUE;
			
		}
		
	}
	
	
	
	
	/* Activate
	 *
	 * Make a given year the active one
	 */
	function activate($year_id){
		
		// Check the year is valid first
		$sql = 'SELECT year_id FROM years WHERE year_id = ?';
		$query = $this->db->query($sql, array($year_id));
		if($query->num_rows() != 1){
			$this->lasterr = 'No year by that ID';
			return FALSE;
		}
		
		// Clear all other years making them inactive
		$sql = 'UPDATE years SET current = NULL';
		$query = $this->db->query($sql);
		
		// Now set the given year as the active one
		$sql = 'UPDATE years SET current = 1 WHERE year_id = ? LIMIT 1';
		$query = $this->db->query($sql, array($year_id));
		
		return $query;
	}
	
	
	
	
	function get_dropdown(){
		$sql = 'SELECT year_id, name, current FROM years ORDER BY date_start ASC';
		$query = $this->db->query($sql);
		if($query->num_rows() > 0){
			$result = $query->result();
			$years = array();
			foreach($result as $year){
				$year->name = ($year->current == 1) ? $year->name . ' (current)' : $year->name;
				$years[$year->year_id] = $year->name;
			}
			return $years;
		} else {
			$this->lasterr = 'No years found';
			return FALSE;
		}
	}
	
	
	
	
	function get_active_id(){
		$sql = 'SELECT year_id FROM years WHERE current = 1 LIMIT 1';
		$query = $this->db->query($sql);
		if($query->num_rows() == 1){
			$row = $query->row();
			$year_id = $row->year_id;
			return $year_id;
		} else {
			$this->lasterr = 'No current year defined.';
			return FALSE;
		}
	}
	
	
	
	
}




/* End of file: app/models/years_model.php */