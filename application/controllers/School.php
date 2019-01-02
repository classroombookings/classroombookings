<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class School extends MY_Controller
{


	public function __construct()
	{
		parent::__construct();

		$this->require_logged_in();

		// Load models etc.
		$this->load->helper('file');
	}


	/**
	* Page: index
	*
	* This function simply returns the manage() function
	*
	*/
	function index()
	{
		return $this->manage();
	}



	/**
	* Page: home
	*/
	function manage()
	{
		$layout['showtitle'] = 'Tasks';
		$layout['title'] = 'Manage ' . setting('name');

		// Initialise with empty string
		$layout['body'] = '';

		$layout['body'] .= $this->session->flashdata('auth');
		$layout['body'] .= $this->load->view('school/manage/school_manage_index', NULL, TRUE);

		$this->load->view('layout', $layout);
	}





	function details()
	{
		$this->require_auth_level(ADMINISTRATOR);

		$this->data['settings'] = $this->settings_model->get_all('crbs');

		$this->data['title'] = 'School Information';
		$this->data['showtitle'] = $this->data['title'];
		$this->data['body'] = $this->load->view('school/details/school_details_edit', $this->data, TRUE);

		return $this->render();
	}




	/**
	* Controller function to handle a submitted form
	*
	*/
	function details_submit()
	{
		// Parse data input from view and carry out appropriate action.

		// Load image manipulation library
		$this->load->library('image_lib');

		$this->load->library('form_validation');
		$this->form_validation->set_rules('schoolname', 'School name', 'required|max_length[255]');
		$this->form_validation->set_rules('website', 'Website address', 'prep_url|valid_url|max_length[255]');
		$this->form_validation->set_rules('colour', 'Colour', 'max_length[7]|callback__is_valid_colour');
		$this->form_validation->set_rules('userfile', 'Logo', '');
		$this->form_validation->set_rules('d_columns', 'Display columns', 'callback__valid_columns');
		$this->form_validation->set_rules('bia', 'Booking in advance', 'max_length[3]|numeric');

		if ($this->form_validation->run() == FALSE) {
			// Validation failed
			return $this->details();
		}

		$upload = FALSE;

		if (isset($_FILES['userfile']) && isset($_FILES['userfile']['name']) && ! empty($_FILES['userfile']['name'])) {

			// Upload config
			$upload_config = array(
				'upload_path' => FCPATH . 'uploads',
				'allowed_types' => 'jpg|jpeg|png|gif',
				'max_width' => '1600',
				'max_height' => '1600',
				'encrypt_name' => TRUE,
			);
			$this->load->library('upload', $upload_config);

			if ( ! $this->upload->do_upload()) {

				// Not uploaded
				$error = $this->upload->display_errors('','');
				if ($error != 'You did not select a file to upload') {
					$this->session->set_flashdata('image_error', $error);
					$image_error = $error;
					return $this->details();
				}

			} else {

				// File uploaded
				$upload_data = $this->upload->data();

				$this->load->library('image_lib');

				$image_config = array(
					'image_library' => 'gd2',
					'source_image' => $upload_data['full_path'],
					'maintain_ratio' => TRUE,
					'width' => 400,
					'height' => 400,
					'master_dim' => 'auto',
				);

				$this->image_lib->initialize($image_config);

				$res = $this->image_lib->resize();

				if ( ! $res) {
					$this->session->set_flashdata('image_error', $this->image_lib->display_errors());
					return $this->details();
				}

				$upload = TRUE;

			}
		}

		$settings = array(
			'name' => $this->input->post('schoolname'),
			'website' => $this->input->post('website'),
			'colour' => $this->_makecol($this->input->post('colour')),
			'bia' => (int) $this->input->post('bia'),
			'displaytype' => $this->input->post('displaytype'),
			'd_columns' => $this->input->post('d_columns'),
		);

		if ($upload == TRUE || $this->input->post('logo_delete')) {

			// Remove current one
			$logo = setting('logo');
			@unlink(FCPATH . 'uploads/' . $logo);
			$settings['logo'] = '';

			if ($upload == TRUE) {
				$settings['logo'] = $upload_data['file_name'];
			}
		}

		// If colour is empty then set the default so Gradient still works
		if ( ! strlen($settings['colour'])) {
			$settings['colour'] = '468ED8';
		}

		$this->settings_model->set($settings);

		$this->session->set_flashdata('saved', msgbox('info', 'School Details have been updated.'));

		redirect('school/details');
	}


	function _is_valid_colour($colour)
	{
		if ($colour == '' ) {
			return true;
		}

		$hex = array('0','1','2','3','4','5','6','7','8','9','A','B','C','D','E','F');
		#print_r($hex);
		// Remove the hash
		$colour = strtoupper(str_replace('#', '', $colour));
		// Make sure we do have 6 digits
		if (strlen($colour) == 6) {
			$ret = true;
			for ($i=0;$i<strlen($colour);$i++) {
				#echo $colour{$i};
				if ( ! in_array($colour{$i}, $hex)) {
					$this->form_validation->set_message('_is_valid_colour', 'You entered an invalid colour value.');
					return false;
					$ret = false;
				}
			}
		} else {
			$this->form_validation->set_message('_is_valid_colour', 'You entered an invalid colour value.');
			$ret = FALSE;
		}

		return $ret;
	}


	function _valid_columns($cols)
	{
		// Day: Periods / Rooms
		// Room: Periods / Days
		$valid['day'] = array('periods', 'rooms');
		$valid['room'] = array('periods', 'days');

		$displaytype = $this->input->post('displaytype');

		switch ($displaytype) {

			case 'day':
				if (in_array($cols, $valid['day'])) {
					$ret = TRUE;
				} else {
					$ret = FALSE;
				}
			break;

			case 'room':
				if (in_array($cols, $valid['room'])) {
					$ret = TRUE;
				} else {
					$ret = FALSE;
				}
			break;
		}

		if ($ret == FALSE) {
			$this->form_validation->set_message('_valid_columns', 'The column you selected is incompatible with the display type.');
		}

		return $ret;
	}


	function _makecol($colour)
	{
		return strtoupper(str_replace('#', '', $colour));
	}


}
