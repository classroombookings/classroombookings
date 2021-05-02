<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use app\components\bookings\Context;
use app\components\bookings\Grid;


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
		$context = new Context();

		$context->autofill([
			'base_uri' => $this->uri->segment(1),
		]);

		$grid = new Grid($context);

		$this->data['title'] = 'Bookings';
		$this->data['showtitle'] = '';
		$this->data['body'] = $grid->render();

		$arr = $context->toArray();
		$json = json_encode($arr, JSON_PRETTY_PRINT);
		// $this->data['body'] .= "<pre>{$json}</pre>";

		return $this->render();
	}


}
