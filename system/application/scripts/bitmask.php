<?php
class bitmask {
	/**
     * Two-dimensional array containing all bits and their values.  This array is populated by the class
     *
     * @var array
     * @access public
     */
	var $mask_array = array();

	/**
     * Contains the mask created by the class.  It can contain multiple 32 bit masks separated by a hyphen (-)
     * in order to represent extremely large bitmasks on systems without gmp available.
     *
     * @var string
     * @access public
     */
	var $assoc_keys = array();
     /**
     * Contains the keys for associative array
     *
     * @var array
     * @access public
     */
	var $forward_mask = '';

    /**
     * Contains the actual binary number represented by the mask.
     *
     * @var string
     * @access public
     */
	var $bin_mask = '';
	
	/**
	* @return void
	* @desc Sets the $bin_mask variable based on the $forward_mask
	* @access private
	*/
	function _set_binmask() {
		$masks = explode('-',$this->forward_mask);
		if (count($masks) > 1) {
			for ($c = count($masks) - 2; $c >= 0; $c--) {
				$bin_val .= str_pad((string)(base_convert($masks[$c],10,2)),32,'0',STR_PAD_LEFT);
			}
			$bin_val = base_convert($masks[count($masks) - 1],10,2) . $bin_val;
		} else {
			$bin_val = base_convert($masks[0],10,2);
		}
		$this->bin_mask = $bin_val;
	}
	
	/**
	* @return void
	* @desc Sets the $mask_array variable based on the $bin_mask
	* @access private
	*/
	function _set_mask_array() {
		unset($this->mask_array);
		for ($c = (strlen($this->bin_mask) - 1); $c >= 0; $c--) {
			$this->mask_array[] = $this->bin_mask{$c};
		}
	}
	
	function _zeroclean() {
		$this->forward_mask = preg_replace('/(-0)+$/','',$this->forward_mask);
	}
	
	/**
	* @return bool
	* @param unknown $bitnum
	* @desc Check if the bit at position $bitnum is set.
	*/
	function bit_isset($bitnum) {
		$errorlevel = error_reporting();
		error_reporting($errorlevel & ~E_NOTICE);
		$ret = ($this->bin_mask{(strlen($this->bin_mask) - 1 - $bitnum)})?true:false;
		error_reporting($errorlevel);
		return $ret;
	}
	
	/**
	* @return bool
	* @param int $bitnum
	* @desc Set the bit at location $bitnum
	*/
	function set_bit($bitnum)  {
		if (!$this->bit_isset($bitnum)) {
			$masknum = (($bitnum - ($bitnum % 32)) / 32);
			$masks = explode('-',$this->forward_mask);
			if (($masknum - count($masks)) > 0) {
				for ($c = count($masks); $c <= $masknum; $c++) {
					$masks[$c] = 0;
				}
			}
			$masks[$masknum] += pow(2,($bitnum - (32 * $masknum)));
			$this->forward_mask = implode('-',$masks);
			$this->_set_binmask();
			$this->_set_mask_array();
			return true;
		} else {
			return false;
		}
	}

	/**
	* @return bool
	* @param int $bitnum
	* @desc Unset the bit at location $bitnum
	*/
	function unset_bit($bitnum) {
		if ($this->bit_isset($bitnum)) {
			$masknum = (($bitnum - ($bitnum % 32)) / 32);
			$masks = explode('-',$this->forward_mask);
			$masks[$masknum] -= pow(2,($bitnum - (32 * $masknum)));
			$this->forward_mask = implode('-',$masks);
			$this->_zeroclean();
			$this->_set_binmask();
			$this->_set_mask_array();
			return true;
		} else {
			return false;
		}
	}
	
	/**
	* @return bool
	* @param mixed $mask_element
	* @desc Can be either an array of values or empty.  If you wish to add empty values,
	* they can only be added in arrays where there is a non-empty value subsequent in the array.
	*/
	function add_element($mask_element = true) {
		$lastbit = strlen($this->bin_mask);
		if (is_array($mask_element)) {
			foreach ($mask_element as $value) {
				if ($value) $retval = $this->set_bit($lastbit);
				$lastbit++;
			}
		} else {
			$retval = $this->set_bit(strlen($this->bin_mask));
		}
		return $retval;
	}
	
	/**
	* @return void
	* @param array $assoc_bits
	* @desc Allows you to enter an associative array of bit values
	*/
	function add_assoc($assoc_bits) {
		foreach ($assoc_bits as $key => $value) {
			$keys[] = $key;
			$values[] = $value;
		}
		$this->add_element($values);
		$this->assoc_keys = $keys;
	}
	
	/**
	* @return array
	* @param bool $all_values
	* @param bool $force
	* @desc Returns associative array of either all values ($all_values == true) or only
	* selected values ($all_values == false).  By setting $force to true, you can force
	* the return of an array where not all selections were taken into account due to not
	* enough key values being entered.
	*/
	function assoc_get($all_values = true, $force = false) {
		if ((count($this->assoc_keys) < count($this->mask_array)) && !$force) die ('More bits than array keys'); 
		if ($all_values) {
			foreach ($this->assoc_keys as $key => $value) {
				$retval[$value] = $this->mask_array[$key];
			}
		} else {
			foreach ($this->assoc_keys as $key => $value) {
				if ($this->mask_array[$key]) $retval[$value] = $this->mask_array[$key];
			}
		}
		return $retval;
	}
		
	/**
	* @return void
	* @param string $mask
	* @desc Populates the object variables based on the value of $mask which is an integer.
	*/
	function reverse_mask($mask) {
		$this->forward_mask = $mask;
		$this->_set_binmask();
		$this->_set_mask_array();
	}
}
?>
