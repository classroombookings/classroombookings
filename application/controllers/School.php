<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class School extends MY_Controller
{


	public function __construct()
	{
		parent::__construct();

		$this->require_logged_in();
		$this->require_auth_level(ADMINISTRATOR);

		// Load models etc.
		$this->load->helper('file');
	}


	/**
	 * School Details
	 *
	 */
	public function index()
	{
		$this->data['settings'] = $this->settings_model->get_all('crbs');
		$this->data['title'] = 'School Information';
		$this->data['showtitle'] = $this->data['title'];

		if ($this->input->post()) {
			$this->save();
		}

		$this->data['body'] = $this->load->view('school/index', $this->data, TRUE);

		return $this->render();
	}


	/**
	* Controller function to handle a submitted form
	*
	*/
	private function save()
	{
		// Parse data input from view and carry out appropriate action.

		// Load image manipulation library
		$this->load->library('image_lib');

		$this->load->library('form_validation');
		$this->form_validation->set_rules('schoolname', 'School name', 'required|max_length[255]');
		$this->form_validation->set_rules('website', 'Website address', 'prep_url|valid_url|max_length[255]');
		$this->form_validation->set_rules('userfile', 'Logo', '');

		if ($this->form_validation->run() == FALSE) {
			return FALSE;
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
					return FALSE;
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
					return FALSE;
				}

				$upload = TRUE;

			}
		}

		$settings = array(
			'name' => $this->input->post('schoolname'),
			'website' => $this->input->post('website'),
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

		$this->settings_model->set($settings);

		$this->session->set_flashdata('saved', msgbox('info', 'School Details have been updated.'));

		redirect('school');
	}


}
