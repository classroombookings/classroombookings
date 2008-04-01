<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Msg{


	var $CI;
	var $msgs;
	var $types = array('err','help','info','note','warn','yes');


	function Msg(){
		// Load original CI object
		$this->CI =& get_instance();
	}
	
	
	
	
	function add($type = 'note', $text, $title = NULL){
		if(in_array($type, $this->types)){
			$data['title'] = $title;
			$data['text'] = $text;
			$thismsg = $this->CI->load->view('msg/'.$type, $data, TRUE);
			$this->msgs .= $thismsg . "\n";
			$this->CI->session->set_flashdata('msg', $this->msgs);
		}
	}
	
	
	
	
	function show(){
		return $this->msgs;
	}
	
	
	
	
	function showone($type = 'info', $text, $title = NULL){
		if(in_array($type, $this->types)){
			$data['title'] = $title;
			$data['text'] = $text;
			$thismsg = $this->CI->load->view('msg/'.$type, $data, TRUE);
			return $thismsg;
		}
	}
	
	
	
	
	function err($text, $title = NULL){
		return $this->showone('err', $text, $title);
	}
	
	
	function help($text, $title = NULL){
		return $this->showone('help', $text, $title);
	}
	
	
	function info($text, $title = NULL){
		return $this->showone('info', $text, $title);
	}
	
	
	function note($text, $title = NULL){
		return $this->showone('note', $text, $title);
	}
	
	
	function warn($text, $title = NULL){
		return $this->showone('warn', $text, $title);
	}
	
	
	function yes($text, $title = NULL){
		return $this->showone('yes', $text, $title);
	}
	
	
}
?>