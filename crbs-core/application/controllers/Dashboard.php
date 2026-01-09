<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller
{


	public function __construct()
	{
		parent::__construct();
		$this->require_logged_in(FALSE);
	}


	/**
	* Dashboard
	*
	*/
	public function index()
	{
		$this->load->model('bookings_model');
		$this->load->model('users_model');

		// Get User ID
		$user_id = $this->userauth->user->user_id;

		// Get bookings for a room if this user owns one
		$this->data['room_bookings'] = $this->bookings_model->ByRoomOwner($user_id);
		// Get all bookings made by this user (only staff ones)
		$this->data['user_bookings'] = $this->bookings_model->ByUser($user_id);
		// Get totals
		$this->data['totals'] = $this->bookings_model->TotalNum($user_id);

		$this->data['constraints'] = $this->users_model->get_constraints($user_id);

		$this->data['title'] = 'Dashboard';
		$this->data['showtitle'] = '';	//$this->data['title'];

		$this->data['body'] = '';
		$this->data['body'] .= $this->session->flashdata('auth');
		$this->data['body'] .= $this->load->view('dashboard/index', $this->data, TRUE);

		return $this->render();
	}


	public function changelog()
	{
		$this->changelog->touch();

		$url = $this->changelog->public_url();
		if (is_null($url)) {
			redirect('https://www.classroombookings.com/');
		} else {
			redirect($url);
		}
	}


	public function changelog_status()
	{
		echo $this->changelog->get_indicator_icon();
	}



}
