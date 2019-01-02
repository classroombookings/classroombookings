<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Profile extends MY_Controller
{


	public function __construct()
	{
		parent::__construct();

		$this->require_logged_in();

		// Required libraries, models etc
		$this->load->library('email');
		$this->load->model('crud_model');
		$this->load->model('bookings_model');
		$this->load->model('users_model');
	}


	function index()
	{
		// Get User ID
		$user_id = $this->userauth->user->user_id;

		// Get bookings for a room if this user owns one
		$this->data['myroom'] = $this->bookings_model->ByRoomOwner($user_id);
		// Get all bookings made by this user (only staff ones)
		$this->data['mybookings'] = $this->bookings_model->ByUser($user_id);
		// Get totals
		$this->data['total'] = $this->bookings_model->TotalNum($user_id);

		$this->data['title'] = 'My Profile';
		$this->data['showtitle'] = $this->data['title'];
		$this->data['body'] = $this->load->view('profile/profile_index', $this->data, TRUE);

		return $this->render();
	}


	function edit()
	{
		// Get User ID
		$user_id = $this->userauth->user->user_id;

		$this->data['user'] = $this->users_model->Get($user_id);

		$columns = array(
			'c1' => array(
				'width' => '70%',
				'content' => $this->load->view('profile/profile_edit', $this->data, TRUE),
			),
			'c2' => array(
				'width' => '30%',
				'content' => $this->load->view('profile/profile_edit_side', $this->data, TRUE),
			),
		);

		$this->data['title'] = 'Edit my details';
		$this->data['showtitle'] = $this->data['title'];
		$this->data['body'] = $this->load->view('columns', $columns, TRUE);

		return $this->render();
	}


	function save()
	{
		// Get User ID
		$user_id = $this->userauth->user->user_id;

		$this->load->library('form_validation');
		$this->form_validation->set_rules('password1', 'Password', 'min_length[6]');
		$this->form_validation->set_rules('password2', 'Password (confirm)', 'min_length[6]|matches[password1]');
		$this->form_validation->set_rules('email', 'Email address', 'max_length[255]|valid_email');
		$this->form_validation->set_rules('firstname', 'First name', 'max_length[20]');
		$this->form_validation->set_rules('lastname', 'Last name', 'max_length[20]');
		$this->form_validation->set_rules('displayname', 'Display name', 'max_length[20]');
		$this->form_validation->set_rules('extension', 'Extension', 'max_length[10]');

		if ($this->form_validation->run() == FALSE) {
	  		// Validation failed
			return $this->edit();
		}

		// Validation passed!
		$data = array(
			'email' => $this->input->post('email'),
			'firstname' => $this->input->post('firstname'),
			'lastname' => $this->input->post('lastname'),
			'displayname' =>$this->input->post('displayname'),
			'ext' => $this->input->post('ext'),
		);

		// Only update password if one was supplied
		if ($this->input->post('password1') && $this->input->post('password2')) {
			$data['password'] = password_hash($this->input->post('password1'), PASSWORD_DEFAULT);
		}

		// Update session variable with displayname
		$this->session->set_userdata('displayname', $data['displayname']);

		// Now call database to update user and load appropriate message for return value
		if ( ! $this->crud_model->Edit('users', 'user_id', $user_id, $data)) {
			$flashmsg = msgbox('error', 'A database error occured while updating your details.');
		} else {
			$flashmsg = msgbox('info', 'Your details have been successfully updated.');
		}

		// Go back to index
		$this->session->set_flashdata('saved', $flashmsg);
		redirect('profile');
	}


}
