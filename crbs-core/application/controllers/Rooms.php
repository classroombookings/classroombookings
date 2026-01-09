<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rooms extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();

		$this->require_logged_in();

		$this->load->model([
			'crud_model',
			'rooms_model',
			'users_model',
			'room_groups_model',
		]);

		$this->load->helper('number');
	}



	function info($room_id)
	{
		$room = $this->data['room'] = $this->find_room($room_id);

		$this->load->library('table');

		$this->data['photo_url'] = image_url($room->photo);

		// Get all info with formatted values
		$this->data['room_info'] = $this->rooms_model->room_info($room_id);

		$this->data['fields'] = $this->rooms_model->GetFields();
		$this->data['fieldvalues'] = $this->rooms_model->GetFieldValues($room_id);

		$this->load->view('rooms/room_info', $this->data);
	}


	public function photo($room_id)
	{
		$room = $this->find_room($room_id);

		$image_url = image_url($room->photo);
		if ( ! $image_url) {
			show_error(lang('room.error.no_photo'));
		}

		$room_name = html_escape($room->name);
		$img_el = img($image_url, false, ['alt' => sprintf(lang('room.photo.alt'), $room_name)]);
		$title = "<h4>{$room_name}</h4>";

		echo "<div class='room-photo'>{$title}{$img_el}</div>";
	}

	private function find_room($room_id)
	{
		if (empty($room_id)) {
			show_404();
		}

		$room = $this->rooms_model->get_by_id($room_id);

		if (empty($room)) {
			show_404();
		}

		return $room;
	}


}
