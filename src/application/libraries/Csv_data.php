<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * contains a few csv file data access tools.
 *
 * PHP VERSION 5
 *
 * LICENSE: The MIT License
 *
 * Copyright (c) <2008> <Kazuyoshi Tlacaelel>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @category  File
 * @package   File_CSV_DataSource
 * @author    Kazuyoshi Tlacaelel <kazu.dev@gmail.com>
 * @copyright 2008 Kazuyoshi Tlacaelel
 * @license   The MIT License
 * @version   SVN: $Id: DataSource.php 285574 2009-03-09 15:22:24Z ktlacaelel $
 * @link      http://code.google.com/p/php-csv-parser/
 */

/**
 * csv data fetcher
 *
 * Sample snippets refer to this csv file for demonstration.
 * <code>
 *   name,age,skill
 *   john,13,knows magic
 *   tanaka,8,makes sushi
 *   jose,5,dances salsa
 * </code>
 *
 * @category  File
 * @package   File_CSV_DataSource
 * @author    Kazuyoshi Tlacaelel <kazu.dev@gmail.com>
 * @copyright 2008 Kazuyoshi Tlacaelel
 * @license   The MIT License
 * @link      http://code.google.com/p/php-csv-parser/
 */
class CSV_Data
{
    public

    /**
     * csv parsing default-settings
     *
     * @var array
     * @access public
     */
    $settings = array(
        'delimiter' => ',',
        'eol' => ";",
        'length' => 999999,
        'escape' => '"'
    );

    protected

    /**
     * imported data from csv
     *
     * @var array
     * @access protected
     */
    $rows = array(),

    /**
     * csv file to parse
     *
     * @var string
     * @access protected
     */
    $_filename = '',

    /**
     * csv headers to parse
     *
     * @var array
     * @access protected
     */
    $headers = array();

    /**
     * data load initialize
     *
     * @param mixed $filename please look at the load() method
     *
     * @access public
     * @see load()
     * @return void
     */
    public function __construct($filename = null)
    {
        $this->load($filename);
    }

    /**
     * csv file loader
     *
     * indicates the object which file is to be loaded
     *
     * <code>
     *
     *   require_once 'File/CSV/DataSource.php';
     *
     *   $csv = new File_CSV_DataSource;
     *   $csv->load('my_cool.csv');
     *   var_export($csv->connect());
     *
     *   array (
     *     0 =>
     *     array (
     *       'name' => 'john',
     *       'age' => '13',
     *       'skill' => 'knows magic',
     *     ),
     *     1 =>
     *     array (
     *       'name' => 'tanaka',
     *       'age' => '8',
     *       'skill' => 'makes sushi',
     *     ),
     *     2 =>
     *     array (
     *       'name' => 'jose',
     *       'age' => '5',
     *       'skill' => 'dances salsa',
     *     ),
     *   )
     *
     * </code>
     *
     * @param string $filename the csv filename to load
     *
     * @access public
     * @return boolean true if file was loaded successfully
     * @see isSymmetric(), getAsymmetricRows(), symmetrize()
     */
    public function load($filename)
    {
        $this->_filename = $filename;
        $this->flush();
        return $this->parse();
    }

    /**
     * settings alterator
     *
     * lets you define different settings for scanning
     *
     * Given array will override the internal settings
     *
     * <code>
     *  $settings = array(
     *      'delimiter' => ',',
     *      'eol' => ";",
     *      'length' => 999999,
     *      'escape' => '"'
     *  );
     * </code>
     *
     * @param mixed $array containing settings to use
     *
     * @access public
     * @return boolean true if changes where applyed successfully
     * @see $settings
     */
    public function settings($array)
    {
        $this->settings = array_merge($this->settings, $array);
    }

    /**
     * header fetcher
     *
     * gets csv headers into an array
     *
     * <code>
     *
     *   var_export($csv->getHeaders());
     *
     *   array (
     *     0 => 'name',
     *     1 => 'age',
     *     2 => 'skill',
     *   )
     *
     * </code>
     *
     * @access public
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * header counter
     *
     * retrives the total number of loaded headers
     *
     * @access public
     * @return integer gets the length of headers
     */
    public function countHeaders()
    {
        return count($this->headers);
    }

    /**
     * header and row relationship builder
     *
     * Attempts to create a relationship for every single cell that
     * was captured and its corresponding header. The sample below shows
     * how a connection/relationship is built.
     *
     * sample of a csv file "my_cool.csv"
     *
     * <code>
     *   name,age,skill
     *   john,13,knows magic
     *   tanaka,8,makes sushi
     *   jose,5,dances salsa
     * </code>
     *
     * php implementation
     *
     * <code>
     *
     *  $csv = new File_CSV_DataSource;
     *  $csv->load('my_cool.csv');
     *
     *  if (!$csv->isSymmetric()) {
     *      die('file has headers and rows with different lengths
     *      cannot connect');
     *  }
     *
     *  var_export($csv->connect());
     *
     *  array (
     *    0 =>
     *    array (
     *      'name' => 'john',
     *      'age' => '13',
     *      'skill' => 'knows magic',
     *    ),
     *    1 =>
     *    array (
     *      'name' => 'tanaka',
     *      'age' => '8',
     *      'skill' => 'makes sushi',
     *    ),
     *    2 =>
     *    array (
     *      'name' => 'jose',
     *      'age' => '5',
     *      'skill' => 'dances salsa',
     *    ),
     *  )
     *
     * </code>
     *
     *
     * You can pass a collection of headers in an array to build
     * a connection for those columns only!
     *
     * <code>
     *
     *  var_export($csv->connect(array('age')));
     *
     *  array (
     *    0 =>
     *    array (
     *      'age' => '13',
     *    ),
     *    1 =>
     *    array (
     *      'age' => '8',
     *    ),
     *    2 =>
     *    array (
     *      'age' => '5',
     *    ),
     *  )
     *
     * </code>
     *
     * @param array $columns the columns to connect, if nothing
     * is given all headers will be used to create a connection
     *
     * @access public
     * @return array If the data is not symmetric an empty array
     * will be returned instead
     * @see isSymmetric(), getAsymmetricRows(), symmetrize(), getHeaders()
     */
    public function connect($columns = array())
    {
        if (!$this->isSymmetric()) {
            return array();
        }
        if (!is_array($columns)) {
            return array();
        }
        if ($columns === array()) {
            $columns = $this->headers;
        }

        $ret_arr = array();

        foreach ($this->rows as $record) {
            $item_array = array();
            foreach ($record as $column => $value) {
                $header = $this->headers[$column];
                if (in_array($header, $columns)) {
                    $item_array[$header] = $value;
                }
            }

            // do not append empty results
            if ($item_array !== array()) {
                array_push($ret_arr, $item_array);
            }
        }

        return $ret_arr;
    }

    /**
     * data length/symmetry checker
     *
     * tells if the headers and all of the contents length match.
     * Note: there is a lot of methods that won't work if data is not
     * symmetric this method is very important!
     *
     * @access public
     * @return boolean
     * @see symmetrize(), getAsymmetricRows(), isSymmetric()
     */
    public function isSymmetric()
    {
        $hc = count($this->headers);
        foreach ($this->rows as $row) {
            if (count($row) != $hc) {
                return false;
            }
        }
        return true;
    }

    /**
     * asymmetric data fetcher
     *
     * finds the rows that do not match the headers length
     *
     * lets assume that we add one more row to our csv file.
     * that has only two values. Something like
     *
     * <code>
     *   name,age,skill
     *   john,13,knows magic
     *   tanaka,8,makes sushi
     *   jose,5,dances salsa
     *   niki,6
     * </code>
     *
     * Then in our php code
     *
     * <code>
     *   $csv->load('my_cool.csv');
     *   var_export($csv->getAsymmetricRows());
     * </code>
     *
     * The result
     *
     * <code>
     *
     *   array (
     *     0 =>
     *     array (
     *       0 => 'niki',
     *       1 => '6',
     *     ),
     *   )
     *
     * </code>
     *
     * @access public
     * @return array filled with rows that do not match headers
     * @see getHeaders(), symmetrize(), isSymmetric(),
     * getAsymmetricRows()
     */
    public function getAsymmetricRows()
    {
        $ret_arr = array();
        $hc      = count($this->headers);
        foreach ($this->rows as $row) {
            if (count($row) != $hc) {
                $ret_arr[] = $row;
            }
        }
        return $ret_arr;
    }

    /**
     * all rows length equalizer
     *
     * makes the length of all rows and headers the same. If no $value is given
     * all unexistent cells will be filled with empty spaces
     *
     * @param mixed $value the value to fill the unexistent cells
     *
     * @access public
     * @return array
     * @see isSymmetric(), getAsymmetricRows(), symmetrize()
     */
    public function symmetrize($value = '')
    {
        $max_length = 0;
        $headers_length = count($this->headers);

        foreach ($this->rows as $row) {
            $row_length = count($row);
            if ($max_length < $row_length) {
                $max_length = $row_length;
            }
        }

        if ($max_length < $headers_length) {
            $max_length = $headers_length;
        }

        foreach ($this->rows as $key => $row) {
            $this->rows[$key] = array_pad($row, $max_length, $value);
        }

        $this->headers = array_pad($this->headers, $max_length, $value);
    }

    /**
     * grid walker
     *
     * travels through the whole dataset executing a callback per each
     * cell
     *
     * Note: callback functions get the value of the cell as an
     * argument, and whatever that callback returns will be used to
     * replace the current value of that cell.
     *
     * @param string $callback the callback function to be called per
     * each cell in the dataset.
     *
     * @access public
     * @return void
     * @see walkColumn(), walkRow(), fillColumn(), fillRow(), fillCell()
     */
    public function walkGrid($callback)
    {
        foreach (array_keys($this->getRows()) as $key) {
            if (!$this->walkRow($key, $callback)) {
                return false;
            }
        }
        return true;
    }

    /**
     * column fetcher
     *
     * gets all the data for a specific column identified by $name
     *
     * Note $name is the same as the items returned by getHeaders()
     *
     * sample of a csv file "my_cool.csv"
     *
     * <code>
     *   name,age,skill
     *   john,13,knows magic
     *   tanaka,8,makes sushi
     *   jose,5,dances salsa
     * </code>
     *
     * php implementation
     *
     * <code>
     *   $csv = new File_CSV_DataSource;
     *   $csv->load('my_cool.csv');
     *   var_export($csv->getColumn('name'));
     * </code>
     *
     * the above example outputs something like
     *
     * <code>
     *
     *   array (
     *     0 => 'john',
     *     1 => 'tanaka',
     *     2 => 'jose',
     *   )
     *
     * </code>
     *
     * @param string $name the name of the column to fetch
     *
     * @access public
     * @return array filled with values of a column
     * @see getHeaders(), fillColumn(), appendColumn(), getCell(), getRows(),
     * getRow(), hasColumn()
     */
    public function getColumn($name)
    {
        if (!in_array($name, $this->headers)) {
            return array();
        }
        
        $ret_arr = array();
        $key     = array_search($name, $this->headers, true);
        foreach ($this->rows as $data) {
            $ret_arr[] = $data[$key];
        }
        return $ret_arr;
    }

    /**
     * column existance checker
     *
     * checks if a column exists, columns are identified by their
     * header name.
     *
     * sample of a csv file "my_cool.csv"
     *
     * <code>
     *   name,age,skill
     *   john,13,knows magic
     *   tanaka,8,makes sushi
     *   jose,5,dances salsa
     * </code>
     *
     * php implementation
     *
     * <code>
     *   $csv = new File_CSV_DataSource;
     *   $csv->load('my_cool.csv');
     *   $headers = $csv->getHeaders();
     * </code>
     *
     * now lets check if the columns exist
     *
     * <code>
     *   var_export($csv->hasColumn($headers[0]));    // true
     *   var_export($csv->hasColumn('age'));          // true
     *   var_export($csv->hasColumn('I dont exist')); // false
     * </code>
     *
     * @param string $string an item returned by getHeaders()
     *
     * @access public
     * @return boolean
     * @see getHeaders()
     */
    public function hasColumn($string)
    {
        return in_array($string, $this->headers);
    }

    /**
     * column appender
     *
     * Appends a column and each or all values in it can be
     * dinamically filled. Only when the $values argument is given.
     * <code>
     *
     *
     *  var_export($csv->fillColumn('age', 99));
     *  true
     *
     *  var_export($csv->appendColumn('candy_ownership', array(99, 44, 65)));
     *  true
     *
     *  var_export($csv->appendColumn('import_id', 111111111));
     *  true
     *
     *  var_export($csv->connect());
     *
     *  array (
     *    0 =>
     *    array (
     *      'name' => 'john',
     *      'age' => 99,
     *      'skill' => 'knows magic',
     *      'candy_ownership' => 99,
     *      'import_id' => 111111111,
     *    ),
     *    1 =>
     *    array (
     *      'name' => 'tanaka',
     *      'age' => 99,
     *      'skill' => 'makes sushi',
     *      'candy_ownership' => 44,
     *      'import_id' => 111111111,
     *    ),
     *    2 =>
     *    array (
     *      'name' => 'jose',
     *      'age' => 99,
     *      'skill' => 'dances salsa',
     *      'candy_ownership' => 65,
     *      'import_id' => 111111111,
     *    ),
     *  )
     *
     * </code>
     *
     * @param string $column an item returned by getHeaders()
     * @param mixed  $values same as fillColumn()
     *
     * @access public
     * @return boolean
     * @see getHeaders(), fillColumn(), fillCell(), createHeaders(),
     * setHeaders()
     */
    public function appendColumn($column, $values = null)
    {
        if ($this->hasColumn($column)) {
            return false;
        }
        $this->headers[] = $column;
        $length          = $this->countHeaders();
        $rows            = array();

        foreach ($this->rows as $row) {
            $rows[] = array_pad($row, $length, '');
        }

        $this->rows = $rows;

        if ($values === null) {
            $values = '';
        }

        return $this->fillColumn($column, $values);
    }

    /**
     * collumn data injector
     *
     * fills alll the data in the given column with $values
     *
     * sample of a csv file "my_cool.csv"
     *
     * <code>
     *   name,age,skill
     *   john,13,knows magic
     *   tanaka,8,makes sushi
     *   jose,5,dances salsa
     * </code>
     *
     * php implementation
     *
     * <code>
     *   $csv = new File_CSV_DataSource;
     *   $csv->load('my_cool.csv');
     *
     *   // if the csv file loads
     *   if ($csv->load('my_cool.csv')) {
     *
     *      // grab all data within the age column
     *      var_export($csv->getColumn('age'));
     *
     *      // rename all values in it with the number 99
     *      var_export($csv->fillColumn('age', 99));
     *
     *      // grab all data within the age column
     *      var_export($csv->getColumn('age'));
     *
     *      // rename each value in a column independently
     *      $data = array(1, 2, 3);
     *      $csv->fillColumn('age', $data);
     *
     *      var_export($csv->getColumn('age'));
     *   }
     * </code>
     *
     * standard output
     *
     * <code>
     *   array (
     *     0 => '13',
     *     1 => '8',
     *     2 => '5',
     *   )
     * </code>
     *
     * <code>
     *   true
     * </code>
     *
     * <code>
     *   array (
     *     0 => 99,
     *     1 => 99,
     *     2 => 99,
     *   )
     * </code>
     *
     * <code>
     *   array (
     *     0 => 1,
     *     1 => 2,
     *     2 => 3,
     *   )
     * </code>
     *
     * @param mixed $column the column identified by a string
     * @param mixed $values ither one of the following
     *  - (Number) will fill the whole column with the value of number
     *  - (String) will fill the whole column with the value of string
     *  - (Array) will fill the while column with the values of array
     *    the array gets ignored if it does not match the length of rows
     *
     * @access public
     * @return void
     */
    public function fillColumn($column, $values = null)
    {
        if (!$this->hasColumn($column)) {
            return false;
        }

        if ($values === null) {
            return false;
        }

        if (!$this->isSymmetric()) {
            return false;
        }

        $y = array_search($column, $this->headers);

        if (is_numeric($values) || is_string($values)) {
            foreach (range(0, $this->countRows() -1) as $x) {
                $this->fillCell($x, $y, $values);
            }
            return true;
        }

        if ($values === array()) {
            return false;
        }

        $length = $this->countRows();
        if (is_array($values) && $length == count($values)) {
            for ($x = 0; $x < $length; $x++) {
                $this->fillCell($x, $y, $values[$x]);
            }
            return true;
        }

        return false;
    }

    /**
     * column remover
     *
     * Completly removes a whole column identified by $name
     * Note: that this function will only work if data is symmetric.
     *
     * sample of a csv file "my_cool.csv"
     *
     * <code>
     *   name,age,skill
     *   john,13,knows magic
     *   tanaka,8,makes sushi
     *   jose,5,dances salsa
     * </code>
     *
     * load the library and csv file
     *
     * <code>
     *  require_once 'File/CSV/DataSource.php';
     *  $csv = new File_CSV_DataSource;
     *  $csv->load('my_cool.csv');
     * </code>
     *
     * lets dump currently loaded data
     * <code>
     *  var_export($csv->connect());
     * </code>
     *
     * output
     *
     * <code>
     *  array (
     *    0 =>
     *    array (
     *      'name' => 'john',
     *      'age' => '13',
     *      'skill' => 'knows magic',
     *    ),
     *    1 =>
     *    array (
     *      'name' => 'tanaka',
     *      'age' => '8',
     *      'skill' => 'makes sushi',
     *    ),
     *    2 =>
     *    array (
     *      'name' => 'jose',
     *      'age' => '5',
     *      'skill' => 'dances salsa',
     *    ),
     *  )
     * </code>
     *
     * and now let's remove the second column
     *
     * <code>
     *  var_export($csv->removeColumn('age'));
     * </code>
     *
     * output
     *
     * <code>
     *  true
     * </code>
     *
     * those changes made let's dump the data again and see what we got
     *
     * <code>
     *  array (
     *    0 =>
     *    array (
     *      'name' => 'john',
     *      'skill' => 'knows magic',
     *    ),
     *    1 =>
     *    array (
     *      'name' => 'tanaka',
     *      'skill' => 'makes sushi',
     *    ),
     *    2 =>
     *    array (
     *      'name' => 'jose',
     *      'skill' => 'dances salsa',
     *    ),
     *  )
     * </code>
     *
     * @param string $name same as the ones returned by getHeaders();
     *
     * @access public
     * @return boolean
     * @see hasColumn(), getHeaders(), createHeaders(), setHeaders(),
     * isSymmetric(), getAsymmetricRows()
     */
    public function removeColumn($name)
    {
        if (!in_array($name, $this->headers)) {
            return false;
        }

        if (!$this->isSymmetric()) {
            return false;
        }

        $key = array_search($name, $this->headers);
        unset($this->headers[$key]);
        $this->resetKeys($this->headers);

        foreach ($this->rows as $target => $row) {
            unset($this->rows[$target][$key]);
            $this->resetKeys($this->rows[$target]);
        }

        return $this->isSymmetric();
    }

    /**
     * column walker
     *
     * goes through the whole column and executes a callback for each
     * one of the cells in it.
     *
     * Note: callback functions get the value of the cell as an
     * argument, and whatever that callback returns will be used to
     * replace the current value of that cell.
     *
     * @param string $name     the header name used to identify the column
     * @param string $callback the callback function to be called per
     * each cell value
     *
     * @access public
     * @return boolean
     * @see getHeaders(), fillColumn(), appendColumn()
     */
    public function walkColumn($name, $callback)
    {
        if (!$this->isSymmetric()) {
            return false;
        }

        if (!$this->hasColumn($name)) {
            return false;
        }

        if (!function_exists($callback)) {
            return false;
        }

        $column = $this->getColumn($name);
        foreach ($column as $key => $cell) {
            $column[$key] = $callback($cell);
        }
        return $this->fillColumn($name, $column);
    }

    /**
     * cell fetcher
     *
     * gets the value of a specific cell by given coordinates
     *
     * Note: That indexes start with zero, and headers are not
     * searched!
     *
     * For example if we are trying to grab the cell that is in the
     * second row and the third column
     *
     * <code>
     *   name,age,skill
     *   john,13,knows magic
     *   tanaka,8,makes sushi
     *   jose,5,dances salsa
     * </code>
     *
     * we would do something like
     * <code>
     *   var_export($csv->getCell(1, 2));
     * </code>
     *
     * and get the following results
     * <code>
     *   'makes sushi'
     * </code>
     *
     * @param integer $x the row to fetch
     * @param integer $y the column to fetch
     *
     * @access public
     * @return mixed|false the value of the cell or false if the cell does
     * not exist
     * @see getHeaders(), hasCell(), getRow(), getRows(), getColumn()
     */
    public function getCell($x, $y)
    {
        if ($this->hasCell($x, $y)) {
            $row = $this->getRow($x);
            return $row[$y];
        }
        return false;
    }

    /**
     * cell value filler
     *
     * replaces the value of a specific cell
     *
     * sample of a csv file "my_cool.csv"
     *
     * <code>
     *   name,age,skill
     *   john,13,knows magic
     *   tanaka,8,makes sushi
     *   jose,5,dances salsa
     * </code>
     *
     * php implementation
     *
     * <code>
     *
     *   $csv = new File_CSV_DataSource;
     *
     *   // load the csv file
     *   $csv->load('my_cool.csv');
     *
     *   // find out if the given coordinate is valid
     *   if($csv->hasCell(1, 1)) {
     *
     *       // if so grab that cell and dump it
     *       var_export($csv->getCell(1, 1));       // '8'
     *
     *       // replace the value of that cell
     *       $csv->fillCell(1, 1, 'new value');  // true
     *
     *       // output the new value of the cell
     *       var_export($csv->getCell(1, 1));       // 'new value'
     *
     *   }
     * </code>
     *
     * now lets try to grab the whole row
     *
     * <code>
     *   // show the whole row
     *   var_export($csv->getRow(1));
     * </code>
     *
     * standard output
     *
     * <code>
     *   array (
     *     0 => 'tanaka',
     *     1 => 'new value',
     *     2 => 'makes sushi',
     *   )
     * </code>
     *
     * @param integer $x     the row to fetch
     * @param integer $y     the column to fetch
     * @param mixed   $value the value to fill the cell with
     *
     * @access public
     * @return boolean
     * @see hasCell(), getRow(), getRows(), getColumn()
     */
    public function fillCell($x, $y, $value)
    {
        if (!$this->hasCell($x, $y)) {
            return false;
        }
        $row            = $this->getRow($x);
        $row[$y]        = $value;
        $this->rows[$x] = $row;
        return true;
    }

    /**
     * checks if a coordinate is valid
     *
     * sample of a csv file "my_cool.csv"
     *
     * <code>
     *   name,age,skill
     *   john,13,knows magic
     *   tanaka,8,makes sushi
     *   jose,5,dances salsa
     * </code>
     *
     * load the csv file
     *
     * <code>
     *   $csv = new File_CSV_DataSource;
     *   var_export($csv->load('my_cool.csv'));   // true if file is
     *                                            // loaded
     * </code>
     *
     * find out if a coordinate is valid
     *
     * <code>
     *   var_export($csv->hasCell(99, 3)); // false
     * </code>
     *
     * check again for a know valid coordinate and grab that cell
     *
     * <code>
     *   var_export($csv->hasCell(1, 1));  // true
     *   var_export($csv->getCell(1, 1));            // '8'
     * </code>
     *
     * @param mixed $x the row to fetch
     * @param mixed $y the column to fetch
     *
     * @access public
     * @return void
     */
    public function hasCell($x, $y)
    {
        $has_x = array_key_exists($x, $this->rows);
        $has_y = array_key_exists($y, $this->headers);
        return ($has_x && $has_y);
    }

    /**
     * row fetcher
     *
     * Note: first row is zero
     *
     * sample of a csv file "my_cool.csv"
     *
     * <code>
     *   name,age,skill
     *   john,13,knows magic
     *   tanaka,8,makes sushi
     *   jose,5,dances salsa
     * </code>
     *
     * load the library and csv file
     *
     * <code>
     *  require_once 'File/CSV/DataSource.php';
     *  $csv = new File_CSV_DataSource;
     *  $csv->load('my_cool.csv');
     * </code>
     *
     * lets dump currently loaded data
     * <code>
     *  var_export($csv->connect());
     * </code>
     *
     * output
     *
     * <code>
     *  array (
     *    0 =>
     *    array (
     *      'name' => 'john',
     *      'age' => '13',
     *      'skill' => 'knows magic',
     *    ),
     *    1 =>
     *    array (
     *      'name' => 'tanaka',
     *      'age' => '8',
     *      'skill' => 'makes sushi',
     *    ),
     *    2 =>
     *    array (
     *      'name' => 'jose',
     *      'age' => '5',
     *      'skill' => 'dances salsa',
     *    ),
     *  )
     * </code>
     *
     * Now let's fetch the second row
     *
     * <code>
     *  var_export($csv->getRow(1));
     * </code>
     *
     * output
     *
     * <code>
     *  array (
     *    0 => 'tanaka',
     *    1 => '8',
     *    2 => 'makes sushi',
     *  )
     * </code>
     *
     * @param integer $number the row number to fetch
     *
     * @access public
     * @return array the row identified by number, if $number does
     * not exist an empty array is returned instead
     */
    public function getRow($number)
    {
        $raw = $this->rows;
        if (array_key_exists($number, $raw)) {
            return $raw[$number];
        }
        return array();
    }

    /**
     * multiple row fetcher
     *
     * Extracts a rows in the following fashion
     *   - all rows if no $range argument is given
     *   - a range of rows identified by their key
     *   - if rows in range are not found nothing is retrived instead
     *   - if no rows were found an empty array is returned
     *
     * sample of a csv file "my_cool.csv"
     *
     * <code>
     *   name,age,skill
     *   john,13,knows magic
     *   tanaka,8,makes sushi
     *   jose,5,dances salsa
     * </code>
     *
     * load the library and csv file
     *
     * <code>
     *  require_once 'File/CSV/DataSource.php';
     *  $csv = new File_CSV_DataSource;
     *  $csv->load('my_cool.csv');
     * </code>
     *
     * lets dump currently loaded data
     * <code>
     *  var_export($csv->connect());
     * </code>
     *
     * output
     *
     * <code>
     *  array (
     *    0 =>
     *    array (
     *      'name' => 'john',
     *      'age' => '13',
     *      'skill' => 'knows magic',
     *    ),
     *    1 =>
     *    array (
     *      'name' => 'tanaka',
     *      'age' => '8',
     *      'skill' => 'makes sushi',
     *    ),
     *    2 =>
     *    array (
     *      'name' => 'jose',
     *      'age' => '5',
     *      'skill' => 'dances salsa',
     *    ),
     *  )
     * </code>
     *
     * now get the second and thirdh row
     *
     * <code>
     *  var_export($csv->getRows(array(1, 2)));
     * </code>
     *
     * output
     *
     * <code>
     *  array (
     *    0 =>
     *    array (
     *      0 => 'tanaka',
     *      1 => '8',
     *      2 => 'makes sushi',
     *    ),
     *    1 =>
     *    array (
     *      0 => 'jose',
     *      1 => '5',
     *      2 => 'dances salsa',
     *    ),
     *  )
     * </code>
     *
     * now lets try something odd and the goodie third row
     *
     * <code>
     *  var_export($csv->getRows(array(9, 2)));
     * </code>
     *
     * output
     *
     * <code>
     *  array (
     *    0 =>
     *    array (
     *      0 => 'jose',
     *      1 => '5',
     *      2 => 'dances salsa',
     *    ),
     *  )
     * </code>
     *
     * @param array $range a list of rows to retrive
     *
     * @access public
     * @return array
     */
    public function getRows($range = array())
    {
        if (is_array($range) && ($range === array())) {
            return $this->rows;
        }

        if (!is_array($range)) {
            return $this->rows;
        }

        $ret_arr = array();
        foreach ($this->rows as $key => $row) {
            if (in_array($key, $range)) {
                $ret_arr[] = $row;
            }
        }
        return $ret_arr;
    }

    /**
     * row counter
     *
     * This function will exclude the headers
     *
     * sample of a csv file "my_cool.csv"
     *
     * <code>
     *   name,age,skill
     *   john,13,knows magic
     *   tanaka,8,makes sushi
     *   jose,5,dances salsa
     * </code>
     *
     * php implementation
     *
     * <code>
     *   $csv = new File_CSV_DataSource;
     *   $csv->load('my_cool.csv');
     *   var_export($csv->countRows()); // returns 3
     * </code>
     *
     * @access public
     * @return integer
     */
    public function countRows()
    {
        return count($this->rows);
    }

    /**
     * row appender
     *
     * Aggregates one more row to the currently loaded dataset
     *
     * sample of a csv file "my_cool.csv"
     *
     * <code>
     *   name,age,skill
     *   john,13,knows magic
     *   tanaka,8,makes sushi
     *   jose,5,dances salsa
     * </code>
     *
     *
     * first let's load the file and output whatever was retrived.
     *
     * <code>
     *  require_once 'File/CSV/DataSource.php';
     *  $csv = new File_CSV_DataSource;
     *  $csv->load('my_cool.csv');
     *  var_export($csv->connect());
     * </code>
     *
     * output
     *
     * <code>
     *
     *  array (
     *    0 =>
     *    array (
     *      'name' => 'john',
     *      'age' => '13',
     *      'skill' => 'knows magic',
     *    ),
     *    1 =>
     *    array (
     *      'name' => 'tanaka',
     *      'age' => '8',
     *      'skill' => 'makes sushi',
     *    ),
     *    2 =>
     *    array (
     *      'name' => 'jose',
     *      'age' => '5',
     *      'skill' => 'dances salsa',
     *    ),
     *  )
     * </code>
     *
     * now lets do some modifications, let's try adding three rows.
     *
     * <code>
     *  var_export($csv->appendRow(1));
     *  var_export($csv->appendRow('2'));
     *  var_export($csv->appendRow(array(3, 3, 3)));
     * </code>
     *
     * output
     *
     * <code>
     *  true
     *  true
     *  true
     * </code>
     *
     * and now let's try to see what has changed
     *
     * <code>
     *  var_export($csv->connect());
     * </code>
     *
     * output
     *
     * <code>
     *  array (
     *    0 =>
     *    array (
     *      'name' => 'john',
     *      'age' => '13',
     *      'skill' => 'knows magic',
     *    ),
     *    1 =>
     *    array (
     *      'name' => 'tanaka',
     *      'age' => '8',
     *      'skill' => 'makes sushi',
     *    ),
     *    2 =>
     *    array (
     *      'name' => 'jose',
     *      'age' => '5',
     *      'skill' => 'dances salsa',
     *    ),
     *    3 =>
     *    array (
     *      'name' => 1,
     *      'age' => 1,
     *      'skill' => 1,
     *    ),
     *    4 =>
     *    array (
     *      'name' => '2',
     *      'age' => '2',
     *      'skill' => '2',
     *    ),
     *    5 =>
     *    array (
     *      'name' => 3,
     *      'age' => 3,
     *      'skill' => 3,
     *    ),
     *  )
     * </code>
     *
     * @param array $values the values to be appended to the row
     *
     * @access public
     * @return boolean
     */
    public function appendRow($values)
    {
        $this->rows[] = array();
        $this->symmetrize();
        return $this->fillRow($this->countRows() - 1, $values);
    }

    /**
     * fillRow
     *
     * Replaces the contents of cells in one given row with $values.
     *
     * sample of a csv file "my_cool.csv"
     *
     * <code>
     *   name,age,skill
     *   john,13,knows magic
     *   tanaka,8,makes sushi
     *   jose,5,dances salsa
     * </code>
     *
     * if we load the csv file and fill the second row with new data?
     *
     * <code>
     *  // load the library
     *  require_once 'File/CSV/DataSource.php';
     *  $csv = new File_CSV_DataSource;
     *
     *  // load csv file
     *  $csv->load('my_cool.csv');
     *
     *  // fill exitent row
     *  var_export($csv->fillRow(1, 'x'));
     * </code>
     *
     * output
     *
     * <code>
     *  true
     * </code>
     *
     * now let's dump whatever we have changed
     *
     * <code>
     *  var_export($csv->connect());
     * </code>
     *
     * output
     *
     * <code>
     *  array (
     *    0 =>
     *    array (
     *      'name' => 'john',
     *      'age' => '13',
     *      'skill' => 'knows magic',
     *    ),
     *    1 =>
     *    array (
     *      'name' => 'x',
     *      'age' => 'x',
     *      'skill' => 'x',
     *    ),
     *    2 =>
     *    array (
     *      'name' => 'jose',
     *      'age' => '5',
     *      'skill' => 'dances salsa',
     *    ),
     *  )
     * </code>
     *
     * now lets try to fill the row with specific data for each cell
     *
     * <code>
     *  var_export($csv->fillRow(1, array(1, 2, 3)));
     * </code>
     *
     * output
     *
     * <code>
     *  true
     * </code>
     *
     * and dump the results
     *
     * <code>
     *  var_export($csv->connect());
     * </code>
     *
     * output
     *
     * <code>
     *
     *  array (
     *    0 =>
     *    array (
     *      'name' => 'john',
     *      'age' => '13',
     *      'skill' => 'knows magic',
     *    ),
     *    1 =>
     *    array (
     *      'name' => 1,
     *      'age' => 2,
     *      'skill' => 3,
     *    ),
     *    2 =>
     *    array (
     *      'name' => 'jose',
     *      'age' => '5',
     *      'skill' => 'dances salsa',
     *    ),
     *  )
     * </code>
     *
     * @param integer $row    the row to fill identified by its key
     * @param mixed   $values the value to use, if a string or number
     * is given the whole row will be replaced with this value.
     * if an array is given instead the values will be used to fill
     * the row. Only when the currently loaded dataset is symmetric
     *
     * @access public
     * @return boolean
     * @see isSymmetric(), getAsymmetricRows(), symmetrize(), fillColumn(),
     * fillCell(), appendRow()
     */
    public function fillRow($row, $values)
    {
        if (!$this->hasRow($row)) {
            return false;
        }

        if (is_string($values) || is_numeric($values)) {
            foreach ($this->rows[$row] as $key => $cell) {
                 $this->rows[$row][$key] = $values;
            }
            return true;
        }

        $eql_to_headers = ($this->countHeaders() == count($values));
        if (is_array($values) && $this->isSymmetric() && $eql_to_headers) {
            $this->rows[$row] = $values;
            return true;
        }

        return false;
    }

    /**
     * row existance checker
     *
     * Scans currently loaded dataset and
     * checks if a given row identified by $number exists
     *
     * sample of a csv file "my_cool.csv"
     *
     * <code>
     *   name,age,skill
     *   john,13,knows magic
     *   tanaka,8,makes sushi
     *   jose,5,dances salsa
     * </code>
     *
     * load library and csv file
     *
     * <code>
     *  require_once 'File/CSV/DataSource.php';
     *  $csv = new File_CSV_DataSource;
     *  $csv->load('my_cool.csv');
     * </code>
     *
     * build a relationship and dump it so we can see the rows we will
     * be working with
     *
     * <code>
     *   var_export($csv->connect());
     * </code>
     *
     * output
     *
     * <code>
     *  array (
     *    0 =>
     *    array (
     *      'name' => 'john',
     *      'age' => '13',
     *      'skill' => 'knows magic',
     *    ),
     *    1 =>  // THIS ROW EXISTS!!!
     *    array (
     *      'name' => 'tanaka',
     *      'age' => '8',
     *      'skill' => 'makes sushi',
     *    ),
     *    2 =>
     *    array (
     *      'name' => 'jose',
     *      'age' => '5',
     *      'skill' => 'dances salsa',
     *    ),
     *  )
     * </code>
     *
     * now lets check for row existance
     *
     * <code>
     *  var_export($csv->hasRow(1));
     *  var_export($csv->hasRow(-1));
     *  var_export($csv->hasRow(9999));
     * </code>
     *
     * output
     *
     * <code>
     *  true
     *  false
     *  false
     * </code>
     *
     * @param mixed $number a numeric value that identifies the row
     * you are trying to fetch.
     *
     * @access public
     * @return boolean
     * @see getRow(), getRows(), appendRow(), fillRow()
     */
    public function hasRow($number)
    {
        return (in_array($number, array_keys($this->rows)));
    }

    /**
     * row remover
     *
     * removes one row from the current data set.
     *
     * sample of a csv file "my_cool.csv"
     *
     * <code>
     *   name,age,skill
     *   john,13,knows magic
     *   tanaka,8,makes sushi
     *   jose,5,dances salsa
     * </code>
     *
     * first let's load the file and output whatever was retrived.
     *
     * <code>
     *  require_once 'File/CSV/DataSource.php';
     *  $csv = new File_CSV_DataSource;
     *  $csv->load('my_cool.csv');
     *  var_export($csv->connect());
     * </code>
     *
     * output
     *
     * <code>
     *
     *  array (
     *    0 =>
     *    array (
     *      'name' => 'john',
     *      'age' => '13',
     *      'skill' => 'knows magic',
     *    ),
     *    1 =>
     *    array (
     *      'name' => 'tanaka',
     *      'age' => '8',
     *      'skill' => 'makes sushi',
     *    ),
     *    2 =>
     *    array (
     *      'name' => 'jose',
     *      'age' => '5',
     *      'skill' => 'dances salsa',
     *    ),
     *  )
     * </code>
     *
     * now lets remove the second row
     *
     * <code>
     *  var_export($csv->removeRow(1));
     * </code>
     *
     * output
     *
     * <code>
     *  true
     * </code>
     *
     * now lets dump again the data and see what changes have been
     * made
     *
     * <code>
     *  var_export($csv->connect());
     * </code>
     *
     * output
     *
     * <code>
     *  array (
     *    0 =>
     *    array (
     *      'name' => 'john',
     *      'age' => '13',
     *      'skill' => 'knows magic',
     *    ),
     *    1 =>
     *    array (
     *      'name' => 'jose',
     *      'age' => '5',
     *      'skill' => 'dances salsa',
     *    ),
     *  )
     * </code>
     *
     * @param mixed $number the key that identifies that row
     *
     * @access public
     * @return boolean
     * @see hasColumn(), getHeaders(), createHeaders(), setHeaders(),
     * isSymmetric(), getAsymmetricRows()
     */
    public function removeRow($number)
    {
        $cnt = $this->countRows();
        $row = $this->getRow($number);
        if (is_array($row) && ($row != array())) {
            unset($this->rows[$number]);
        } else {
            return false;
        }
        $this->resetKeys($this->rows);
        return ($cnt == ($this->countRows() + 1));
    }

    /**
     * row walker
     *
     * goes through one full row of data and executes a callback
     * function per each cell in that row.
     *
     * Note: callback functions get the value of the cell as an
     * argument, and whatever that callback returns will be used to
     * replace the current value of that cell.
     *
     * @param string|integer $row      anything that is numeric is a valid row
     * identificator. As long as it is within the range of the currently
     * loaded dataset
     *
     * @param string         $callback the callback function to be executed
     * per each cell in a row
     *
     * @access public
     * @return boolean
     *  - false if callback does not exist
     *  - false if row does not exits
     */
    public function walkRow($row, $callback)
    {
        if (!function_exists($callback)) {
            return false;
        }
        if ($this->hasRow($row)) {
            foreach ($this->getRow($row) as $key => $value) {
                $this->rows[$row][$key] = $callback($value);
            }
            return true;
        }
        return false;
    }

    /**
     * raw data as array
     *
     * Gets the data that was retrived from the csv file as an array
     *
     * Note: that changes and alterations made to rows, columns and
     * values will also reflect on what this function retrives.
     *
     * @access public
     * @return array
     * @see connect(), getHeaders(), getRows(), isSymmetric(), getAsymmetricRows(),
     * symmetrize()
     */
    public function getRawArray()
    {
        $ret_arr   = array();
        $ret_arr[] = $this->headers;
        foreach ($this->rows as $row) {
            $ret_arr[] = $row;
        }
        return $ret_arr;
    }

    /**
     * header creator
     *
     * uses prefix and creates a header for each column suffixed by a
     * numeric value
     *
     * by default the first row is interpreted as headers but if we
     * have a csv file with data only and no headers it becomes really
     * annoying to work with the current loaded data.
     *
     * this function will create a set dinamically generated headers
     * and make the current headers accessable with the row handling
     * functions
     *
     * Note: that the csv file contains only data but no headers
     * sample of a csv file "my_cool.csv"
     *
     * <code>
     *   john,13,knows magic
     *   tanaka,8,makes sushi
     *   jose,5,dances salsa
     * </code>
     *
     * checks if the csv file was loaded
     *
     * <code>
     *   $csv = new File_CSV_DataSource;
     *   if (!$csv->load('my_cool.csv')) {
     *      die('can not load csv file');
     *   }
     * </code>
     *
     * dump current headers
     *
     * <code>
     *   var_export($csv->getHeaders());
     * </code>
     *
     * standard output
     *
     * <code>
     *   array (
     *     0 => 'john',
     *     1 => '13',
     *     2 => 'knows magic',
     *   )
     * </code>
     *
     * generate headers named 'column' suffixed by a number and interpret
     * the previous headers as rows.
     *
     * <code>
     *   $csv->createHeaders('column')
     * </code>
     *
     * dump current headers
     *
     * <code>
     *   var_export($csv->getHeaders());
     * </code>
     *
     * standard output
     *
     * <code>
     *   array (
     *     0 => 'column_1',
     *     1 => 'column_2',
     *     2 => 'column_3',
     *   )
     * </code>
     *
     * build a relationship and dump it
     *
     * <code>
     *   var_export($csv->connect());
     * </code>
     *
     * output
     *
     * <code>
     *
     *  array (
     *    0 =>
     *    array (
     *      'column_1' => 'john',
     *      'column_2' => '13',
     *      'column_3' => 'knows magic',
     *    ),
     *    1 =>
     *    array (
     *      'column_1' => 'tanaka',
     *      'column_2' => '8',
     *      'column_3' => 'makes sushi',
     *    ),
     *    2 =>
     *    array (
     *      'column_1' => 'jose',
     *      'column_2' => '5',
     *      'column_3' => 'dances salsa',
     *    ),
     *  )
     * </code>
     *
     * @param string $prefix string to use as prefix for each
     * independent header
     *
     * @access public
     * @return boolean fails if data is not symmetric
     * @see isSymmetric(), getAsymmetricRows()
     */
    public function createHeaders($prefix)
    {
        if (!$this->isSymmetric()) {
            return false;
        }

        $length = count($this->headers) + 1;
        $this->moveHeadersToRows();

        $ret_arr = array();
        for ($i = 1; $i < $length; $i ++) {
            $ret_arr[] = $prefix . "_$i";
        }
        $this->headers = $ret_arr;
        return $this->isSymmetric();
    }

    /**
     * header injector
     *
     * uses a $list of values which wil be used to replace current
     * headers.
     *
     * Note: that given $list must match the length of all rows.
     * known as symmetric. see isSymmetric() and getAsymmetricRows() methods
     *
     * Also, that current headers will be used as first row of data
     * and consecuently all rows order will change with this action.
     *
     * sample of a csv file "my_cool.csv"
     *
     * <code>
     *   name,age,skill
     *   john,13,knows magic
     *   tanaka,8,makes sushi
     *   jose,5,dances salsa
     * </code>
     *
     * load the library and csv file
     *
     * <code>
     *  require_once 'File/CSV/DataSource.php';
     *  $csv = new File_CSV_DataSource;
     *  $csv->load('my_cool.csv');
     * </code>
     *
     * lets dump currently loaded data
     * <code>
     *  var_export($csv->connect());
     * </code>
     *
     * output
     *
     * <code>
     *  array (
     *    0 =>
     *    array (
     *      'name' => 'john',
     *      'age' => '13',
     *      'skill' => 'knows magic',
     *    ),
     *    1 =>
     *    array (
     *      'name' => 'tanaka',
     *      'age' => '8',
     *      'skill' => 'makes sushi',
     *    ),
     *    2 =>
     *    array (
     *      'name' => 'jose',
     *      'age' => '5',
     *      'skill' => 'dances salsa',
     *    ),
     *  )
     * </code>
     *
     * And now lets create a new set of headers and attempt to inject
     * them into the current loaded dataset
     *
     * <code>
     *  $new_headers = array('a', 'b', 'c');
     *  var_export($csv->setHeaders($new_headers));
     * </code>
     *
     * output
     *
     * <code>
     *  true
     * </code>
     *
     * Now lets try the same with some headers that do not match the
     * current headers length. (this should fail)
     *
     * <code>
     *  $new_headers = array('a', 'b');
     *  var_export($csv->setHeaders($new_headers));
     * </code>
     *
     * output
     *
     * <code>
     *  false
     * </code>
     *
     * now let's dump whatever we have changed
     *
     * <code>
     *  var_export($csv->connect());
     * </code>
     *
     * output
     *
     * <code>
     *  array (
     *    0 =>
     *    array (
     *      'a' => 'name',
     *      'b' => 'age',
     *      'c' => 'skill',
     *    ),
     *    1 =>
     *    array (
     *      'a' => 'john',
     *      'b' => '13',
     *      'c' => 'knows magic',
     *    ),
     *    2 =>
     *    array (
     *      'a' => 'tanaka',
     *      'b' => '8',
     *      'c' => 'makes sushi',
     *    ),
     *    3 =>
     *    array (
     *      'a' => 'jose',
     *      'b' => '5',
     *      'c' => 'dances salsa',
     *    ),
     *  )
     * </code>
     *
     * @param array $list a collection of names to use as headers,
     *
     * @access public
     * @return boolean fails if data is not symmetric
     * @see isSymmetric(), getAsymmetricRows(), getHeaders(), createHeaders()
     */
    public function setHeaders($list)
    {
        if (!$this->isSymmetric()) {
            return false;
        }
        if (!is_array($list)) {
            return false;
        }
        if (count($list) != count($this->headers)) {
            return false;
        }
        $this->moveHeadersToRows();
        $this->headers = $list;
        return true;
    }

    /**
     * csv parser
     *
     * reads csv data and transforms it into php-data
     *
     * @access protected
     * @return boolean
     */
    protected function parse()
    {
        if (!$this->validates()) {
            return false;
        }

        $c = 0;
        $d = $this->settings['delimiter'];
        $e = $this->settings['escape'];
        $l = $this->settings['length'];

        $res = fopen($this->_filename, 'r');

        while ($keys = fgetcsv($res, $l, $d, $e)) {

            if ($c == 0) {
                $this->headers = $keys;
            } else {
                array_push($this->rows, $keys);
            }

            $c ++;
        }

        fclose($res);
        $this->removeEmpty();
        return true;
    }

    /**
     * empty row remover
     *
     * removes all records that have been defined but have no data.
     *
     * @access protected
     * @return array containing only the rows that have data
     */
    protected function removeEmpty()
    {
        $ret_arr = array();
        foreach ($this->rows as $row) {
            $line = trim(join('', $row));
            if (!empty($line)) {
                $ret_arr[] = $row;
            }
        }
        $this->rows = $ret_arr;
    }

    /**
     * csv file validator
     *
     * checks wheather if the given csv file is valid or not
     *
     * @access protected
     * @return boolean
     */
    protected function validates()
    {
        // file existance
        if (!file_exists($this->_filename)) {
            return false;
        }

        // file readability
        if (!is_readable($this->_filename)) {
            return false;
        }

        return true;
    }

    /**
     * header relocator
     *
     * @access protected
     * @return void
     */
    protected function moveHeadersToRows()
    {
        $arr   = array();
        $arr[] = $this->headers;
        foreach ($this->rows as $row) {
            $arr[] = $row;
        }
        $this->rows    = $arr;
        $this->headers = array();
    }

    /**
     * array key reseter
     *
     * makes sure that an array's keys are setted in a correct numerical order
     *
     * Note: that this function does not return anything, all changes
     * are made to the original array as a reference
     *
     * @param array &$array any array, if keys are strings they will
     * be replaced with numeric values
     *
     * @access protected
     * @return void
     */
    protected function resetKeys(&$array)
    {
        $arr = array();
        foreach ($array as $item) {
            $arr[] = $item;
        }
        $array = $arr;
    }

    /**
     * object data flusher
     *
     * tells this object to forget all data loaded and start from
     * scratch
     *
     * @access protected
     * @return void
     */
    protected function flush()
    {
        $this->rows    = array();
        $this->headers = array();
    }

}

?>
