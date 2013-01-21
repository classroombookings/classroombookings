<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Classroombookings. Hassle-free resource booking for schools. <http://classroombookings.com/>
 * Copyright (C) 2006-2011 Craig A Rodway <craig.rodway@gmail.com>
 *
 * This file is part of Classroombookings.
 * Classroombookings is licensed under the Affero GNU GPLv3 license.
 * Please see license-classroombookings.txt for the full license text.
 */


class Years_model extends School_Model
{
	
	
	protected $_table = 'years';		// DB table
	protected $_sch_key = 'y_s_id';		// Foreign key for school
	
	public $error;


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
			
			if (isset($page) && is_array($page))
			{
				$this->db->limit($page[0], $page[1]);
			}
			
			$query = $this->db->get();
			if ($query->num_rows() > 0)
			{
				return $query->result();
			}
			else
			{
				$this->lasterr = 'There are no academic years defined.';
				return 0;
			}
		}
		else
		{
			if (!is_numeric($year_id))
			{
				return false;
			}
			// Getting one year
			$sql = 'SELECT * FROM years WHERE year_id = ? LIMIT 1';
			$query = $this->db->query($sql, array($year_id));
			if ($query->num_rows() == 1)
			{
				// Got the year
				$year = $query->row();				
				return $year;
			}
			else
			{
				return false;
			}
		}
	}
	
	
	
	
	function add($data)
	{
		$current = false;
		// Use the proper function to make it active (to de-activate other years)
		if ($data['current'] == 1)
		{
			// Set flag to check to activate it later as we don't have the ID yet
			$current = true;
		}
		$data['current'] = null;
		
		$add = $this->db->insert('years', $data);
		$year_id = $this->db->insert_id();
		
		if ($current == true)
		{
			// Now we have the ID of the new year, and we need to make it active
			$this->make_current($year_id);
		}
		return $year_id;
	}
	
	
	
	
	function edit($year_id = null, $data)
	{
		if ($year_id == null)
		{
			$this->lasterr = 'Cannot update a year without its ID.';
			return false;
		}
		
		$active = false;
		// Use the proper function to make it active (to de-activate other years)
		if ($data['current'] == 1)
		{
			$active = true;
		}
		$data['current'] = null;
		
		// Update year info
		$this->db->where('year_id', $year_id);
		$edit = $this->db->update('years', $data);
		
		if ($active == true)
		{
			$this->make_current($year_id);
		}
		
		return $edit;
	}
	
	
	
	
	function delete($year_id)
	{
		$sql = 'DELETE FROM years WHERE year_id = ? LIMIT 1';
		$query = $this->db->query($sql, array($year_id));
		
		if ($query == false)
		{
			$this->lasterr = 'Could not delete year. Does it exist?';
			return false;
		}
		else
		{
			return true;
		}
	}
	
	
	
	
	/**
	 * Make the given year the current one
	 *
	 */
	function make_current($year_id)
	{
		// Check the year is valid first
		$sql = 'SELECT year_id FROM years WHERE year_id = ?';
		$query = $this->db->query($sql, array($year_id));
		if ($query->num_rows() != 1)
		{
			$this->lasterr = 'No year by that ID';
			return false;
		}
		
		// Clear all other years making them inactive
		$sql = 'UPDATE years SET current = NULL';
		$query = $this->db->query($sql);
		
		// Now set the given year as the active one
		$sql = 'UPDATE years SET current = 1 WHERE year_id = ? LIMIT 1';
		$query = $this->db->query($sql, array($year_id));
		
		return $query;
	}
	
	
	
	
	function get_dropdown()
	{
		$sql = 'SELECT year_id, name, current FROM years ORDER BY date_start ASC';
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0)
		{
			$result = $query->result();
			$years = array();
			foreach ($result as $year)
			{
				$year->name = ($year->current == 1) ? $year->name . ' (current)' : $year->name;
				$years[$year->year_id] = $year->name;
			}
			return $years;
		}
		else
		{
			$this->lasterr = 'No years found';
			return false;
		}
	}
	
	
	
	
	/**
	 * Get the 'current' academic year
	 */
	function get_current()
	{
		$sql = 'SELECT *
				FROM years
				WHERE y_current = 1
				' . $this->sch_sql() . '
				LIMIT 1';
		
		return $this->db->query($sql)->row_array();
	}
	
	
	
	
}


/* End of file: app/models/years_model.php */