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

class MY_Model extends CI_Model
{


    /**
     * The database table to use
     *
     * @var string
     */
    protected $_table;


    /**
     * The primary key, by default set to `id`, for use in some functions.
     *
     * @var string
     */
    protected $_primary = 'id';


    /**
     * Order by data. Array($col, $sort[asc|desc])
     *
     * @var array
     */
    protected $_order = array();


    /**
     * SQL limit value
     *
     * @var int
     */
    protected $_limit;


    /**
     * SQL offset value
     *
     * @param int
     */
    protected $_offset;


    /**
     * Specify the lookup type (where or like) for each filterable parameter/db col.
     *
     * If the db column isn't here - it doesn't get filtered on.
     *
     * @var array
     */
    protected $_filter_types = array(
        'where' => array(),
        'like' => array(),
    );


    protected $_filter_data = array();
	
	
	protected $_join = NULL;


    /**
     * Wrapper to __construct for when loading
     * class is a superclass to a regular controller,
     * i.e. - extends Base not extends Controller.
     * 
     * @return void
     */
    public function MY_Model() { $this->__construct(); }


    /**
     * Class constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->db->protect_identifiers($this->_table);
    }
    
    
    
    
    /**
     * Set values for ORDER BY SQL
     *
     * @param string $col		Column name to order on
     * @param string $sort		Sort direction. ASC or DESC.
     * @return $this
     */
    public function order_by($col, $sort = 'ASC')
    {
        $this->_order = array($col, $sort);
        return $this;
    }
    
    
    
    
    /**
     * Apply given SQL limit to next get queries
     *
     * @param int $value		LIMIT value
     * @param int $offest		Optional offset value
     * @return $this
     */
    public function limit($value, $offset = NULL)
    {
        $this->_limit = (int) $value;
        if ($offset !== NULL)
        {
            $this->_offset = (int) $offset;
        }
        return $this;
    }
    
    
    
    
    public function clear_limit()
    {
        $this->_limit = '';
        $this->_offset = '';
    }




    /**
     * Take an array of keys and values and apply them as a filter to the database.
     *
     * Checks for valid columns in the filter_types array first and then fills an 
     * object instance array of the columns and data.
     *
     * @param array $params		Array of keys => values to filter data on, where key is DB column
     * @return $this
     */
    public function set_filter($params = array())
    {
        if (empty($params))
        {
            return $this;
        }
        
        // Loop through the valid filter columns.
        // This ensures we don't query on anything we shouldn't be.
        foreach ($this->_filter_types as $type => $fields)
        {
            // Loop through the fields that are acceptable for this type (where/like)
            foreach ($fields as $f)
            {
                // Get the filter param value for this field
                $value = element($f, $params, NULL);
                if ($value !== NULL)
                {
                    // Add the value and field to instance array if present
                    $this->_filter_data[$type][$f] = $value;
                }
            }
        }
        return $this;
    }
    
    
    
    
    /**
     * Clear the filter array data
     *
     * @return $this
     */
    public function clear_filter()
    {
        $this->_filter_data = array();
        return $this;
    }
    
    
    
    
    /**
     * Get all rows from the table
     *
     * @return array 		DB result array
     */
    public function get_all()
    {
        $sql = 'SELECT *
                FROM `' . $this->_table . '`
                WHERE 1 = 1 ' .
                $this->filter_sql() .
                $this->order_sql() .
                $this->limit_sql();
        
        return $this->db->query($sql)->result_array();
    }
    
    
    
    
    /**
     * Get one row where ID matches supplied value
     *
     * @param int $id		ID of row
     * @return array 		Result row
     */
    public function get($id = NULL)
    {
        $sql = 'SELECT *
                FROM `' . $this->_table . '`
				' . $this->join_sql() . '
                WHERE `' . $this->_primary . '` = ?
                LIMIT 1';
        
        return $this->db->query($sql, array($id))->row_array();
    }





    /**
     * Get rows where the key matches value
     *
     * @param string $key		DB column name to select on
     * @param string $value		Value the column should be to match
     */
    public function get_by($key, $value)
    {
        $sql = 'SELECT * 
                FROM `' . $this->_table . '`
				' . $this->join_sql() . ' 
                WHERE `' . $key .'` = ?' .
                $this->order_sql() .
                $this->limit_sql();
        
        $query = $this->db->query($sql, array($value));
        if ($this->_limit === 1)
        {
            return $query->row_array();
        }
        else
        {
            return $query->result_array();
        }
    }




    /**
     * Insert a new record into the DB table with the column values set by the array.
     *
     * @param array $data		Array of column names => values to set
     * @return mixed		Auto-increment ID on success, FALSE on failure
     */
    public function insert($data = array())
    {
        if ($this->db->query($this->db->insert_string($this->_table, $data)))
        {
            $id = $this->db->insert_id();
            return ($id) ? $id : TRUE;
        }
        else
        {
            return FALSE;
        }
    }




    /**
     * Perform an update query based on parameters.
     *
     * Example:
     * 	update(6, array('foo' => 'bar'), 'active = 1 AND date >= NOW()')
     *
     * @param int $id		ID/primary key value of row to update
     * @param array $data		Array of db cols => values to set
     * @param string $where_extra		In addition to updating on primary key - additional clause
     * @return bool
     */
    public function update($id, $data, $where_extra = '')
    {
        $where = ' `' . $this->_primary . '` = ' . $this->db->escape($id) . ' ';
            
        if ($where_extra != '')
        {
            $where .= ' AND ' . $where_extra;
        }
        
        if ($this->_join)
        {
            /**
             * If there's a join with another table (to satisfy school ID requirements),
             * then the update has to be constructed a bit differently to ensure the
             * correct keys are included and values match
             */ 
            $where .= ' AND ' . $this->_join[1];
            $tables = '`' . $this->_table . '` AS a, `' . $this->_join[0] . '` AS b';
            $update = $this->db->query($this->db->update_string($tables, $data, $where));
            return ($update) ? $id : $update;
        }
        else
        {
            $update = $this->db->query($this->db->update_string($this->_table, $data, $where));
            return ($update) ? $id : $update;
        }
    }
    
    
    
    
    /**
     * Delete a single row where primary key matches $id
     *
     * @param itn $id		ID of row stored in primary key field
     * @return bool
     */
    public function delete($id)
    {
		$sql = 'DELETE `' . $this->_table . '` FROM `' . $this->_table . '`
				' . $this->join_sql() . ' 
				WHERE `' . $this->_primary . '` = ?
				LIMIT 1';
		
		return $this->db->query($sql, array($id));
    }




    /**
     * Count all rows in table without any filtering at all
     */
     public function count_all()
     {
        $sql = 'SELECT COUNT(*) AS c
                FROM `' . $this->_table . '`
                WHERE 1=1'
                . $this->filter_sql();
        
        $row = $this->db->query($sql)->row_array();
        return $row['c'];
     }




    /**
     * Get the filter SQL
     *
     * @param string $operator		Specify whether to use AND or OR
     * @return the SQL string
     */
    protected function filter_sql($operator = 'AND')
    {
        $str = '';
        
        if ( ! empty($this->_filter_data['like']))
        {
            foreach ($this->_filter_data['like'] as $col => $val)
            {
                $str .= " $operator `$col` LIKE '%" . $this->db->escape_like_str($val) . "%' ";
            }
        }
        
        if ( ! empty($this->_filter_data['where']))
        {
            foreach ($this->_filter_data['where'] as $col => $val)
            {
                $str .= " $operator `$col` = " . $this->db->escape($val) . " ";
            }
        }
        
        return $str;
    }




    /**
     * Return the SQL string for ORDER BY statement
     */
    protected function order_sql()
    {
        if (empty($this->_order))
        {
            return '';
        }
        
        return ' ORDER BY `' . $this->_order[0] . '` ' . $this->_order[1] . ' ';
    }




    /**
     * Return the SQL string for the LIMIT statement
     */
    protected function limit_sql()
    {
        if ($this->_offset == '' && $this->_limit == '')
        {
            return '';
        }
        
        $offset = $this->_offset;
        
        if ($this->_offset == 0)
        {
            $offset = '';
        }
        else
        {
            $offset .= ', ';
        }
        
        return " LIMIT " . $offset . $this->_limit;
    }
	
	
	
	
	protected function join_sql()
	{
		if ($this->_join === NULL) return '';
		return ' LEFT JOIN `' . $this->_join[0] . '` ON ' . $this->_join[1];
	}




    /**
     * Retrieve a dropdown-friendly array of table data in key => value format.
     *
     * Examples:
     *
     * 	dropdown('c_name');		// uses primary key value from class
     * 	dropdown('c_id', 'c_name');		// specify primary key and the value
     * 	dropdown('c_id', 'c_name', 'c_enabled');		// only where c_enabled = 1
     *
     * @param string $key		The ID column. If not present, uses instance variable
     * @param string $value		The value column to get. Must be present
     * @param string $enabled		The boolean "enabled" column to check equals 1
     *
     * @return array
     */
    function dropdown()
    {
        $args =& func_get_args();
        
        if (count($args) == 3)
        {
            list($key, $value, $enabled) = $args;
        }
        if (count($args) == 2)
        {
            list($key, $value) = $args;
            $enabled = '';
        }
        else
        {
            $key = $this->_primary;
            $value = $args[0];
            $enabled = '';
        }
        
        // Build SQL string to select the key and value
        
        if (method_exists($this, 'dropdown_query'))
        {
            $sql = $this->dropdown_query();
        }
        else
        {
            $sql = " SELECT `$key`, `$value` FROM `{$this->_table}` ";
            if ($enabled != '')
            {
                $sql .= " AND `$enabled` = 1 ";
            }
            
            $sql .= " ORDER BY `$value` ASC ";
        }
        
        $result = $this->db->query($sql)->result_array();
        
        $options = array();
        
        foreach ($result as $row)
        {
            $options[$row[$key]] = $row[$value];
        }
        
        return $options;
    }


}




/**
 * School model.
 *
 * Ensures that all database access for school-related items
 * are linked to a school entry
 */

class School_model extends MY_Model {


    protected $_s_id;       // School ID


    public function __construct()
    {
        parent::__construct();
        
        // Get school ID from config
        $this->_s_id = config_item('s_id');
        log_message('debug', "School_model: __construct(): School model initialised with ID {$this->_s_id}.");
    }
    
    
    
    
    /**
     * Generate and return an AND part of an SQL query to ensure 
     * a query contains the school ID
     *
     * @return string       String containing table-relative SQL
     */  
    protected function sch_sql()
    {
        if ( (int) $this->_s_id === 0) log_message('error', 'School_model: sch_sql(): School ID should NOT be 0 here!');
        
        return ' AND `' . $this->_sch_key . '` = ' . (int) $this->_s_id . ' ';
    }
    
    
    
    
    /**
     * Count all rows in table without any filtering at all
     */
     public function count_all()
     {
        $sql = 'SELECT COUNT(*) AS c
                FROM `' . $this->_table . '`
                ' . $this->join_sql() . ' 
                WHERE 1=1
                ' . $this->filter_sql() . '
                ' . $this->sch_sql();
        
        $row = $this->db->query($sql)->row_array();
        return $row['c'];
     }
    
    
    
    
    /**
     * Insert a new record and automatically complete the school ID value
     *
     * @param array $data       Array of columns => values
     * @return mixed        AUTO_INCREMENT ID on success, FALSE on failure
     */ 
    public function insert($data = array())
    {
        // Add the school ID to this insert
        if ( ! $this->join_sql())
        {
            $data[$this->_sch_key] = (int) $this->_s_id;
        }
        
        // Delegate responsibility to parent
        return parent::insert($data);
    }
    
    
    
    
    public function update($id = 0, $data = array(), $where_extra = '')
    {
        $where = ' `' . $this->_sch_key . '` = ' . (int) $this->_s_id;
        
        if ($where_extra !== '')
        {
            $where .= ' AND ' . $where_extra;
        }
        
        return parent::update($id, $data, $where);
    }




    /**
     * Delete an item, with additional clause for school foreign key
     *
     * @param mixed $id		ID of value in the primary key field of the row to delete
     * @return bool
     */
    public function delete($id = 0)
    {
        if ($this->join_sql())
        {
			$sql = 'DELETE `' . $this->_table . '` FROM `' . $this->_table . '`
				' . $this->join_sql() . '
				WHERE `' . $this->_primary . '` = ?
				' . $this->sch_sql();
        }
		else
		{
			$sql = 'DELETE FROM `' . $this->_table . '`
				WHERE `' . $this->_primary . '` = ?
				' . $this->sch_sql() . '
				LIMIT 1';
		}
		
        return $this->db->query($sql, array($id));
    }
    
    
    
    
    /**
     * Get all rows from the table with additional school ID clause
     *
     * @return array
     */
    public function get_all()
    {
        $sql = 'SELECT *
                FROM `' . $this->_table . '`
				' . $this->join_sql() . '
                WHERE 1 = 1
				' . $this->sch_sql() . '
                ' . $this->filter_sql() . '
				' . $this->order_sql() . '
				' . $this->limit_sql();
		
        return $this->db->query($sql)->result_array();
    }
    
    
    
    
    /**
     * Get one row from the table where PK ID matches supplied ID along with school ID
     *
     * @param mixed $id		Primary key ID of row to retrieve
     * @return array
     */
    public function get($id = 0)
    {
        $sql = 'SELECT *
                FROM `' . $this->_table . '`
				' . $this->join_sql() . '
                WHERE `' . $this->_primary . '` = ?
				' . $this->sch_sql() . '
                LIMIT 1';
		
        return $this->db->query($sql, array($id))->row_array();
    }
    
    
    
    
    /**
     * Get rows where the key matches value
     *
     * @param string $key		Which column to use on WHERE
     * @param string $value		Value for $key to match
     * @param string $where_extra		Extra clauses for WHERE
     * @return array 		DB results. If LIMIT is 1, row_array(); otherwise result_array()
     */
    public function get_by($key = '', $value = '', $where_extra = '')
    {
        $where_extra .= $this->sch_sql();
        return parent::get_by($key, $value, $where_extra);
    }
    
    
    
    
    /**
     * Retrieve a dropdown-friendly array of table data in key => value format.
     *
     * Examples:
     *
     * 	dropdown('c_name');		// uses primary key value from class
     * 	dropdown('c_id', 'c_name');		// specify primary key and the value
     * 	dropdown('c_id', 'c_name', 'c_enabled');		// only where c_enabled = 1
     *
     * @param string $key		The ID column. If not present, uses instance variable
     * @param string $value		The value column to get. Must be present
     * @param string $enabled		The boolean "enabled" column to check equals 1
     *
     * @return array
     */
    function dropdown()
    {
        $args =& func_get_args();
        
        if (count($args) == 3)
        {
            list($key, $value, $enabled) = $args;
        }
        if (count($args) == 2)
        {
            list($key, $value) = $args;
            $enabled = '';
        }
        else
        {
            $key = $this->_primary;
            $value = $args[0];
            $enabled = '';
        }
        
        // Build SQL string to select the key and value
        
        $sql = "SELECT `$key`, `$value` 
                FROM `{$this->_table}` 
				" . $this->join_sql() . "
                WHERE 1 = 1
				" . $this->sch_sql() . "
				ORDER BY `$value` ASC";
        
        $result = $this->db->query($sql)->result_array();
        
        $options = array();
        
        foreach ($result as $row)
        {
            $options[$row[$key]] = $row[$value];
        }
        
        return $options;
    }
    
    
    
}




/* End of file: ./application/core/MY_Model.php */