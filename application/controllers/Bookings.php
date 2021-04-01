<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use app\components\bookings\Context;


class Bookings extends MY_Controller
{


	public function __construct()
	{
		parent::__construct();

		$this->require_logged_in();

		$this->lang->load('bookings');

		if ($this->userauth->is_level(TEACHER) && setting('maintenance_mode')) {
			$this->data['title'] = 'Bookings';
			$this->data['showtitle'] = '';
			$this->data['body'] = '';
			$this->render();
			$this->output->_display();
			exit();
		}

	}


	public function index()
	{
		redirect('bookings/view');
	}


	public function view()
	{
		$context = new Context();
		$context->autofill();

		print_r($context->toArray());
	}


}
