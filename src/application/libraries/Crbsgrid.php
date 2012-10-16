<?php


class Crbsgrid {


	var $CI;
	private $room_id;
	private $date;
	private $view_type;
	private $view_cols;
	private $periods;
	
	
	function Crbsgrid(){
		// Load original CI object
		$this->CI =& get_instance();
	}
	
	
	
	
	/**
	 * Set display type
	 */
	function set_type($type){
		$types = array('day', 'room');
		if(in_array($type, $types)){
			$this->view_type = $type;
		}
	}
	
	
	/**
	 * Set room ID
	 */
	function set_room($room_id){
		if(is_numeric($room_id)){
			$this->room_id = $room_id;
		}
	}
	
	
	/**
	 * Set date (or week-start if display type is room)
	 */
	function set_date($date){
		$preg = preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2}/', $date);
		if($preg === 1){
			$parts = explode('-', $date);
			$check = checkdate($parts[1], $parts[2], $parts[0]);
			if($check == TRUE){
				$this->date = $date;
			}
		}
	}
	
	
	/** 
	 * Set display columns
	 */
	function set_columns($col){
		$cols = array('periods', 'days');
		if(in_array($col, $cols)){
			$this->view_columns = $col;
		}
	}
	
	
	/**
	 * Set periods
	 */
	function set_periods($periods){
		$this->periods = $periods;
	}
	
	
	
	
	function generate(){
	}
	
	
}