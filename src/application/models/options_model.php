<?php

class Options_model extends School_Model
{
	
	
	protected $_table = 'options';		// DB table
	protected $_sch_key = 'o_s_id';		// Foreign key for school
	
	
	public function __construct()
	{
		parent::__construct();
	}
	
	
	
	
	/**
	 * Get one or more options and values
	 *
	 * @param mixed $option		NULL for all options & values; string for one option
	 * @return mixed		Array, string or boolean
	 */
	public function get($option = NULL)
	{
		if ($option === NULL)
		{
			return $this->get_all();
		}
		else
		{
			// One option was requested - get the value of it
			return $this->get_one($option);
		}
	}
	
	
	
	
	/**
	 * Get all options for school
	 *
	 * @return array 		Array of option => value
	 */
	public function get_all()
	{
		// Get all of the options!
		$sql = "SELECT o_name, o_value
				FROM `{$this->_table}`
				WHERE 1 = 1
				" . $this->sch_sql();
		
		$result = $this->db->query($sql)->result_array();
		return $this->_format_options($result);
	}
	
	
	
	
	/**
	 * Get a single option value
	 *
	 * @param string $name		Name of the option value to retrieve
	 * @return mixed		Raw value as string, or boolean if 1/0/yes/no
	 */
	public function get_one($name = '')
	{
		$sql = "SELECT o_value
				FROM `{$this->_table}`
				WHERE o_name = ?
				" . $this->sch_sql() . "
				LIMIT 1";
		
		$query = $this->db->query($sql, array($name));
		
		if ($query->num_rows() === 1)
		{
			$row = $query->row_array();
			return $this->_parse_value($row['o_value']);
		}
		else
		{
			return FALSE;
		}
	}
	
	
	
	
	/**
	 * Set one or more options
	 *
	 * @param mixed $name		String: option name to set. Array: option names => values
	 * @param string $value		Value of $name to set
	 * @return bool
	 */
	public function set($name = NULL, $value = NULL)
	{
		$errors = 0;
		
		if ($name !== NULL && $value !== NULL)
		{
			// One option to set using name and value
			$data = array($name => $value);
		}
		elseif (is_array($name) && $value === NULL)
		{
			// Lots of options in array format
			$data =& $name;
		}
		
		if (is_array($data))
		{
			$sql = "INSERT INTO
						`{$this->_table}`
					SET
						`{$this->_sch_key}` = ?,
						o_name = ?,
						o_value = ?
					ON DUPLICATE KEY UPDATE
						o_s_id = VALUES(o_s_id),
						o_name = VALUES(o_name),
						o_value = VALUES(o_value)";
			
			foreach ($name as $o_name => $o_value)
			{
				$query = $this->db->query($sql, array($this->_s_id, $o_name, $o_value));
				if ( ! $query) $errors++;
			}
		}
		
		return ($errors === 0);
	}
	
	
	
	
	/** 
	 * Format a DB result row to an array of keys and values
	 *
	 * @param array $result		DB result array to use
	 * @return array 		Array of option names => option values
	 */
	private function _format_options($result = array())
	{
		$options = array();
		
		foreach ($result as $row)
		{
			$options[$row['o_name']] = $this->_parse_value($row['o_value']);
		}
		
		return $options;
	}
	
	
	
	
	/**
	 * Parse a stored option value for booleanness to return
	 *
	 * @param string $value		The option value
	 * @return mixed		String of value, or boolean if value is 1, 0, yes, no
	 */
	private function _parse_value($value = '')
	{
		if (in_array(strtolower($value), array('1', '0', 'yes', 'no', 'true', 'false')))
		{
			return filter_var($value, FILTER_VALIDATE_BOOLEAN);
		}
		else
		{
			return $value;
		}
	}
	
	
	
	
}

/* End of file: ./application/models/options_model.php */