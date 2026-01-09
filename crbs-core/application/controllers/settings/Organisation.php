<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Organisation extends MY_Controller
{


	public function __construct()
	{
		parent::__construct();

		$this->require_logged_in();
		$this->require_permission(Permission::SETUP_SETTINGS);

		$this->load->helper('file');
	}


	/**
	 * Organisation
	 *
	 */
	public function index()
	{
		$this->data['settings'] = $this->settings_model->get_all('crbs');

		if ($this->input->post()) {
			$this->save();
		}

		$this->data['title'] = lang('organisation.organisation');
		$this->data['showtitle'] = $this->data['title'];

		$body = $this->load->view('settings/organisation', $this->data, TRUE);

		$this->data['body'] = $body;

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
		$this->form_validation->set_rules('schoolname', 'lang:organisation.fields.name', 'required|max_length[255]');
		$this->form_validation->set_rules('website', 'lang:organisation.fields.website', 'prep_url|valid_url|max_length[255]');
		// $this->form_validation->set_rules('userfile', 'lang:organisation.fields.logo', '');

		if ($this->form_validation->run() == FALSE) {
			return FALSE;
		}

		$upload = FALSE;

		if (isset($_FILES['userfile']) && isset($_FILES['userfile']['name']) && ! empty($_FILES['userfile']['name'])) {

			// Upload config
			$upload_config = array(
				'upload_path' => config_item('upload_path'),
				'allowed_types' => 'jpg|jpeg|png|gif',
				'max_width' => '1600',
				'max_height' => '1600',
				'encrypt_name' => true,
			);
			$this->load->library('upload', $upload_config);

			if ( ! $this->upload->do_upload()) {

				// Not uploaded
				$error = $this->upload->display_errors('','');
				$this->session->set_flashdata('image_error', $error);
				return FALSE;

			} else {

				// File uploaded
				$upload_data = $this->upload->data();

				$this->load->library('image_lib');

				$image_config = array(
					'image_library' => 'gd2',
					'source_image' => $upload_data['full_path'],
					'maintain_ratio' => true,
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

				$upload = true;

				handle_uploaded_file($upload_data['full_path']);
			}
		}

		$settings = array(
			'name' => $this->input->post('schoolname'),
			'website' => $this->input->post('website'),
		);

		if ($upload == true || $this->input->post('logo_delete')) {

			// Remove current one
			$logo = setting('logo');
			delete_user_file($logo);
			$settings['logo'] = '';

			if ($upload == true) {
				$settings['logo'] = $upload_data['file_name'];
			}
		}

		$this->settings_model->set($settings);

		$this->session->set_flashdata('saved', msgbox('info', lang('organisation.save.success')));

		redirect('settings/organisation');
	}


}
