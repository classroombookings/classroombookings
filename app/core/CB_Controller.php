<?php

class CB_Controller extends CI_Controller {
	
	function __construct(){
		parent::__construct();
		$this->tpl = 'template/layout-desktop';
		$this->output->enable_profiler($this->config->item('profiler'));
	}
	
}