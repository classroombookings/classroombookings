<?php
class School extends Controller {





  function School(){
    parent::Controller();

    // Check to see if it's installed
    $this->installed();

		// Load language
  	$this->lang->load('crbs', 'english');

		// Get school id
    $this->school_id = $this->session->userdata('school_id');

    $this->output->enable_profiler($this->session->userdata('profiler'));

    // Check loggedin status
    if(!$this->userauth->loggedin()){
    	$this->session->set_flashdata('login', $this->load->view('msgbox/error', 'Please log in to access this page.', True) );
    	$this->loggedin = False;
			redirect('login', 'location');
		} else {
			$this->loggedin = True;
			$this->authlevel = $this->userauth->GetAuthLevel($this->session->userdata('user_id'));
		}

		// Load models etc.
		#$this->load->script('gradient');
		$this->load->helper('file');
		$this->load->model('school_model', 'M_school', True);
	}





	function installed(){
		$query_str = "SHOW TABLES";
		$query = $this->db->query($query_str);
		if($query->num_rows() == 0){
			redirect('install');
		}
	}





  /**
   * Page: index
   *
   * This function simply returns the home() function
   */
  function index(){
    return $this->manage();
  }





  /**
   * Page: home
   */
	function manage(){
		$layout['showtitle'] = 'Tasks';
		$layout['title'] = 'Manage your school ('.$this->session->userdata('schoolname').')';

		// Columns view
	  /* $cols[0]['content'] = $this->load->view('school/manage/school_manage_index_1', NULL, True);
	  $cols[0]['width'] = '50%';
	  $cols[1]['content'] = $this->load->view('school/manage/school_manage_index_2', NULL, True);
	  $cols[1]['width'] = '50%'; */

	  // Initialise with empty string
	  $layout['body'] = '';


	  // Now check for existance of install.php - tell user to remove it if it's still there.
	  if( file_exists('system/application/controllers/install.php') && $this->authlevel == 1){
	  	$layout['body'] .= $this->load->view('msgbox/warning', '<strong>Security notice:</strong> Please remove install.php from the controllers directory or <a href="school/delete_install">click here</a> to delete it now.', True);
	  }

		$layout['body'] .= $this->session->flashdata('auth');
		$layout['body'] .= $this->load->view('school/manage/school_manage_index', NULL, True);
		$this->load->view('layout', $layout);
	}





	function details(){
    if( !$this->userauth->loggedin() ){
    	$this->session->set_flashdata('login', $this->load->view('msgbox/error', $this->lang->line('crbs_auth_mustbeloggedin'), True) );
			redirect('site/home', 'location');
		} else {
			if(!$this->userauth->CheckAuthLevel(ADMINISTRATOR)){
				$this->session->set_flashdata('auth', $this->load->view('msgbox/error', $this->lang->line('crbs_auth_mustbeadmin'), True) );
				redirect('controlpanel', 'location');
			}
		}
		$body['info'] = $this->M_school->GetInfo();	//ByCode($this->session->userdata('schoolcode'));
		$layout['title'] = 'School Information';
		$layout['showtitle'] = $layout['title'];
		$layout['body'] = $this->load->view('school/details/school_details_edit.php', $body, True);
		$this->load->view('layout', $layout);
	}




  /**
   * Controller function to handle a submitted form
   */
  function details_submit(){
		// Parse data input from view and carry out appropriate action.

		// Load image manipulation library
		$this->load->library('image_lib');

		// Load upload library
		$this->load->library('upload');

		// Upload config
		$upload['upload_path'] 			= './webroot/images/schoollogo/temp';
		$upload['allowed_types']		= 'jpg|jpeg|png|gif';
		$upload['max_size']					= '2048';
		$upload['max_width']				= '1600';
		$upload['max_height']				= '1200';
		$this->upload->initialize($upload);

		// Validation rules
		$vrules['schoolname']			= 'required|max_length[255]';
		$vrules['website']	  		= 'prep_url|max_length[255]';
		$vrules['colour']					= 'max_length[7]|callback__is_valid_colour';
		$vrules['userfile']				= 'max_length[255]';
		$vrules['d_columns']				= 'callback__valid_columns';
		$vrules['bia']						= 'max_length[3]|numeric';
		#$vrules['bquota']					= 'max_length[3]|numeric';
		$this->validation->set_rules($vrules);

		// Pretty it up a bit for error validation message
		$vfields['schoolname']		= 'School name';
		$vfields['website']	  		= 'Website address';
		$vfields['colour']				= 'Header colour';
		$vfields['userfile']			= 'Logo';
		$vfields['bia']						= 'Booking in advance';
		#$vfields['bquota']				= 'Booking Quota';
		$vfields['displaytype']		= 'Display type';
		$vfields['d_columns']				= 'Booking columns';
		#$vfields['recurring']			= 'Recurring option';
		#$vfields['holidays']			= 'Holiday bookings';
		$this->validation->set_fields($vfields);

		// Set the error delims to a nice styled red box
		#$this->validation->set_error_delimiters('<p class="msgbox error">', '</p>');
		$this->validation->set_error_delimiters('<p class="hint error"><span>', '</span></p>');


		#print_r($_POST);


    if ($this->validation->run() == FALSE){

      // Validation failed
			$this->details();

		} else {

			if( !$this->upload->do_upload() ){
				// Not uploaded
				$error = $this->upload->display_errors('','');
				if( $error != 'You did not select a file to upload' ){
					$this->session->set_flashdata('image_error', $error);
					$image_error = $error;
					#echo $error;
					return $this->details();
				}
				$upload = false;

			} else {

				// File uploaded
				$logo = $this->upload->data();

				// new filename is <md5(rawname sessionid)>.<extension>
				$newfile = md5($logo['raw_name'].$this->session->userdata('session_id')) . $logo['file_ext'];

				$thumbs['image_library']		= 'GD2';
				$thumbs['source_image']			= $logo['full_path'];
				$thumbs['create_thumb']			= false;
				$thumbs['maintain_ratio']		= true;
				$thumbs['master_dim']				= 'auto';
				$this->image_lib->initialize($thumbs);

				$thumbs['new_image']				= 'webroot/images/schoollogo/300/'.$newfile;
				$thumbs['width']						= 300;
				$this->image_lib->initialize($thumbs);
				$this->image_lib->resize();

				$thumbs['new_image']				= 'webroot/images/schoollogo/200/'.$newfile;
				$thumbs['width']						= 200;
				$this->image_lib->initialize($thumbs);
				$this->image_lib->resize();

				$thumbs['new_image']				= 'webroot/images/schoollogo/100/'.$newfile;
				$thumbs['width']						= 100;
				$this->image_lib->initialize($thumbs);
				$this->image_lib->resize();

				@unlink($logo['full_path']);

				// Move file & rename it
				#@unlink('webroot/images/roomphotos/'.$newfile);
				#$ren = rename($photo['full_path'], 'webroot/images/roomphotos/'.$newfile);

				// Done
				$upload = true;
				#print_r($photo);
			}

			// Database info
			$data['name'] 				= $this->input->post('schoolname');
			$data['website']			= $this->input->post('website');
			$data['colour'] 			= $this->_makecol($this->input->post('colour'));
			$data['bia']					= (int) $this->input->post('bia');
			#$data['bquota']				= $this->input->post('bquota');
			#$data['recurring']		= ($this->input->post('recurring') == 1) ? 1 : 0;
			#$data['holidays']		= ($this->input->post('holidays') == 1) ? 1 : 0;
			$data['displaytype']	= $this->input->post('displaytype');
			$data['d_columns']			= $this->input->post('d_columns');

			// Set no logo first, then if the upload succeeded then we set that
			#$data['logo']				= '';
			if($upload == true){
				$data['logo'] = $newfile;
			}

			// If user clicked the 'delete logo' button on an edit, delete logo
			if( $this->input->post('logo_delete') != NULL ){
				$this->M_school->delete_logo($this->school_id);
			}

			// If colour is empty then set the default so Gradient still works
			if(!$data['colour']){ $data['colour'] = '468ED8'; }

			// Generate gradient
			$file = 'webroot/images/bg/'.$this->school_id.'.png';
			$gradient['width'] = 1;
			$gradient['height'] = 80;
			$gradient['type'] = 'vertical';
			$gradient['start_colour'] = '#'.$data['colour'];
			$gradient['end_colour'] = '#ffffff';
			$this->gradient->Generate($gradient, $file);

			#$this->M_school->delete_logo($this->session->userdata('schoolcode'));
			$this->M_school->edit('school_id', $this->session->userdata('school_id'), $data);

		  $this->session->set_flashdata('saved', $this->load->view('msgbox/info', 'School Details have been updated.', True) );
		  #$this->load->view('layout', $layout);
			$this->session->close();	//
		  redirect('controlpanel', 'location');

		}

	}





	function delete_install(){
		$file = 'system/application/controllers/install.php';
		if(file_exists($file)){
			if(@unlink($file)){
				// Delete successful
				$msgbox = $this->load->view('msgbox/info', 'install.php has been successfully removed.', True);
			} else {
				// Delete failed
				$msgbox = $this->load->view('msgbox/error', 'install.php has not been removed (check permissions), please delete it manually.', True);
			}
		} else {
			// File not found
			$msgbox = $this->load->view('msgbox/error', 'install.php does not exist.', True);
		}
		$this->session->set_flashdata('auth', $msgbox);
		redirect('controlpanel');
	}





	function gradient($school_id = NULL, $colour = NULL){
		if($school_id == NULL){ $school_id = $this->uri->segment(3, $this->session->userdata('school_id')); }
		if($colour == NULL){
			$query = $this->db->query("SELECT colour FROM schools WHERE school_id='$school_id' AND colour Is Not NULL LIMIT 1");
			if($query->num_rows() == 1){
				$row = $query->row();
				$colour = $row->colour;
			} else {
				$colour = '#AFCEEE';
			}
			#echo $colour;
		}
		#echo $colour;
		$image = new gd_gradient_fill('1', '50', 'vertical', '#'.$colour, '#fff');
		$image->display($image->image);
	}





	function _is_valid_colour($colour){
		if( $colour == '' ){ return true; }
		$hex = array('0','1','2','3','4','5','6','7','8','9','A','B','C','D','E','F');
		#print_r($hex);
		// Remove the hash
		$colour = strtoupper(str_replace('#', '', $colour));
		// Make sure we do have 6 digits
		if(strlen($colour) == 6){
			$ret = true;
			for($i=0;$i<strlen($colour);$i++){
				#echo $colour{$i};
				if(!in_array($colour{$i}, $hex)){
					$this->validation->set_message('_is_valid_colour', 'You entered an invalid colour value.');
					return false;
					$ret = false;
				}
			}
		} else {
			$this->validation->set_message('_is_valid_colour', 'You entered an invalid colour value.');
			$ret = false;
		}
		return $ret;
	}





	function _valid_columns($cols){
		// Day: Periods / Rooms
		// Room: Periods / Days
		$valid['day'] = array('periods', 'rooms');
		$valid['room'] = array('periods', 'days');

		$displaytype = $this->input->post('displaytype');
		switch($displaytype){
			case 'day':
				if(in_array($cols, $valid['day'])){
					$ret = true;
				} else {
					$ret = false;
				}
			break;
			case 'room':
				if(in_array($cols, $valid['room'])){
					$ret = true;
				} else {
					$ret = false;
				}
			break;
		}
	if($ret == false){
		$this->validation->set_message('_valid_columns', 'The column you selected is incompatible with the display type.');
	}
 	return $ret;
	}





	function _makecol($colour){
		return strtoupper(str_replace('#', '', $colour));
	}






}
