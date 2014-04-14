<?php
class Bookings extends Controller {





  function Bookings(){
    parent::Controller();

    // Load language
  	$this->lang->load('crbs', 'english');

		// Set school ID
		$this->school_id = $this->session->userdata('school_id');

		$this->output->enable_profiler(false);

    // Check user is logged in
    if(!$this->userauth->loggedin()){
    	$this->session->set_flashdata('login', $this->load->view('msgbox/error', $this->lang->line('crbs_auth_mustbeloggedin'), True) );
			redirect('site/home', 'location');
		} else {
			$this->loggedin = True;
			$this->authlevel = $this->userauth->GetAuthLevel($this->session->userdata('user_id'));
		}

		#$this->load->library('parser');

		$this->load->script('bitmask');
		$this->load->model('crud_model', 'crud');

		$this->load->model('rooms_model', 'M_rooms');
		$this->load->model('periods_model', 'M_periods');
		$this->load->model('weeks_model', 'M_weeks');

		$this->load->model('users_model', 'M_users');

		#$this->load->model('holidays_model', 'M_holidays');
		$this->load->model('bookings_model', 'M_bookings');
		#$this->load->library('table');

		// Array containing all the data we need (everything but the kitchen sink)
  	#$school['rooms']					= $this->M_rooms->Get(NULL, $this->school_id);
  	#$school['periods']				= $this->M_periods->Get();
  	#$school['weeks']					= $this->M_weeks->Get();
		#$school['holidays']				= $this->M_holidays->Get();
  	#$school['mondays']				= $this->M_weeks->GetMondays(NULL, $school['holidays']);
  	#$school['weekdateids']		= $this->M_weeks->WeekDateIDs();

		$school['users']					= $this->M_users->Get();
		$school['days_list'] 			= $this->M_periods->days;
		$school['days_bitmask']		= $this->M_periods->days_bitmask;
  	$this->school = $school;
  }





  function index(){

  	$uri = $this->uri->uri_to_assoc(3);

  	$this->session->set_userdata('uri', $this->uri->uri_string());

		if( ! isset($uri['date']) ){
			$uri['date'] = date("Y-m-d");
			/*if( $this->session->userdata('chosen_date') ){
				#echo "session: {$this->session->userdata('chosen_date')}<br />";
				$this->school['chosen_date'] = $this->session->userdata('chosen_date');
			}*/
			// Day number of the chosen date
			$day_num = date('w', strtotime($uri['date']));
			#$this->school['chosen_date'] = $chosen_date;
			#$this->session->set_userdata('chosen_date', $this->school['chosen_date']);
		}


		$room_of_user = $this->M_rooms->GetByUser($this->school_id, $this->session->userdata('user_id'));

		if(isset($uri['room'])){
			$uri['room'] = $uri['room'];
		} else {
			if($room_of_user != False){
				$uri['room'] = $room_of_user->room_id;
			} else {
				$uri['room'] = false;
			}
		}


		#$this->school['room'] = $uri['room'];


		$body['html'] = $this->M_bookings->html(
			$this->school_id,
			NULL,
			NULL,
			strtotime($uri['date']),
			$uri['room'],
			$this->school,
			$uri
		);

		/*$body['html'] = $this->M_bookings->htmltable(
			$this->school_id,
			'day',
			'periods',
			$chosen_date,
			$this->school
		);*/

		$layout['title'] = 'Bookings';
		$layout['showtitle'] = '';	//$layout['title'];
		//$layout['body'] = $this->load->view('bookings/bookings_index', $this->school, True);
		$layout['body'] = $this->session->flashdata('saved');
		$layout['body'] .= $body['html'];
		$this->load->view('layout', $layout);
		#print_r($_SESSION);
  }




  /**
   * This function takes the date that was POSTed and loads the view()
   */
  function load(){

  	$style = $this->M_bookings->BookingStyle($this->school_id);

  	#$chosen_date = $this->input->post('chosen_date');

		// Validation rules
		$vrules['chosen_date']		= 'max_length[10]|callback__is_valid_date';
		$vrules['room_id']				= 'numeric';
		$this->validation->set_rules($vrules);
		$vfields['chosen_date']		= 'Date';
		$vfields['room_id']				= 'Room';
		$vfields['direction']			= 'Direction';
		$this->validation->set_fields($vfields);

		// Set the error delims to a nice styled red hint under the fields
		$this->validation->set_error_delimiters('<p class="hint error"><span>', '</span></p>');

    if ($this->validation->run() == FALSE){

			show_error('validation failed');

    } else {

    	switch($style['display']){
    		case 'day':
    			// Display type is one day at a time - all rooms/periods
		    	if($this->input->post('chosen_date')){
						$datearr = explode('/', $this->input->post('chosen_date'));
						if(count($datearr) == 3){
							$chosen_date = sprintf("%s-%s-%s", $datearr[2], $datearr[1], $datearr[0]);
							$url = sprintf('bookings/index/date/%s/direction/%s', $chosen_date, $this->input->post('direction'));
							#$this->session->set_flashdata('uri', $url);
							redirect($url, 'redirect');
						} else {
							show_error('invalid date');
						}
					} else {
						show_error('no date chosen');
					}
				break;
				case 'room':
					if($this->input->post('room_id')){
						$url = sprintf(
							'bookings/index/date/%s/room/%s/direction/%s',
							$this->input->post('chosen_date'),
							$this->input->post('room_id'),
							$this->input->post('direction')
						);
						#$this->session->set_flashdata('uri', $url);
						redirect($url, 'redirect');
					} else {
						show_error('no day selected');
					}
				break;
			} // End switch

    }
	}





	function book(){
		$uri = $this->uri->uri_to_assoc(3);
		#$this->session->set_userdata('uri', $uri);

		$layout['title'] = 'Book a room';
		$layout['showtitle'] = $layout['title'];

		$seg_count = $this->uri->total_segments();
		if($seg_count != 2 && $seg_count != 12){

			// Not all info in URI
			$layout['body'] = $this->load->view('msgbox/error', 'Not enough information specified to book a room.', True);

		} else {

			// Either no URI, or all URI info specified

			// 12 segments means we have all info - adding a booking
			if($seg_count == 12){

				// Create array of data from the URI
				$booking['booking_id'] = 'X';
				$booking['period_id'] = $uri['period'];
				$booking['room_id'] = $uri['room'];
				$booking['date']	= date("d/m/Y", strtotime($uri['date']));

				if($this->userauth->CheckAuthLevel(ADMINISTRATOR, $this->authlevel)){
					$booking['day_num'] = $uri['day'];
					$booking['week_id']	= $uri['week'];
				} else {
					$booking['user_id'] = $this->session->userdata('user_id');
				}

				$body['booking'] = $booking;
				$body['hidden'] = $booking;


			} else {
				$body['hidden'] = array();
			}

			// Lookups we need if an admin user
			if($this->userauth->CheckAuthLevel(ADMINISTRATOR, $this->authlevel)){
				$body['days'] = $this->M_periods->days;
				$body['rooms'] = $this->M_rooms->Get(NULL, $this->school_id);
				$body['periods'] = $this->M_periods->Get();
				$body['weeks'] = $this->M_weeks->Get();
				$body['users'] = $this->M_users->Get();
			}

			$layout['body'] = $this->load->view('bookings/bookings_book', $body, True);

			// Check that the date selected is not in the past
			$today = strtotime(date("Y-m-d"));
			$thedate = strtotime($uri['date']);

			if($this->userauth->CheckAuthLevel(TEACHER, $this->authlevel)){
				if($thedate < $today){
					$layout['body'] = $this->load->view('msgbox/error', 'You cannot make a booking in the past.', True);
				}
			}

			// Now see if user is allowed to book in advance
			if($this->userauth->CheckAuthLevel(TEACHER, $this->authlevel)){

				$bia = (int) $this->_booking_advance($this->school_id);
				if ($bia > 0) {
					$date_forward = strtotime("+$bia days", $today);
					if($thedate > $date_forward){
						$layout['body'] = $this->load->view('msgbox/error', 'You can only book '.$bia.' days in advance.', True);
					}

				}
			}

		}


		$this->load->view('layout', $layout);
		#print_r( $_SESSION );
	}





	function recurring(){
		foreach($this->input->post('recurring') as $booking){
			$arr = explode('/', $booking);
			$max = count($arr);
			#print_r($arr);
			$booking = array();
			for($i=0;$i<count($arr);$i=$i+2){
				$booking[$arr[$i]] = $arr[$i+1];
			}
			$bookings[] = $booking;
		}
		$errcount = 0;
		#echo "<hr>";
		#echo "<pre>".var_export($bookings,true)."</pre>";
		foreach($bookings as $booking){
			$data = array();
			$data['user_id'] = $this->input->post('user_id');
			$data['school_id'] = $this->school_id;
			$data['period_id'] = $booking['period'];
			$data['room_id'] = $booking['room'];
			$data['notes'] = $this->input->post('notes');
			$data['week_id'] = $booking['week'];
			$data['day_num'] = $booking['day'];
			if(!$this->M_bookings->Add($data)){
				$errcount++;
			}
		}
		if($errcount > 0){
			$flashmsg = $this->load->view('msgbox/error', 'One or more bookings could not be made.', True);
		} else {
			$flashmsg = $this->load->view('msgbox/info', 'The bookings were created successfully.', True);
		}

		$this->session->set_userdata('notes', $data['notes']);

		// Go back to index
		$this->session->set_flashdata('saved', $flashmsg);

		$uri = $this->session->userdata('uri');
		#if($data['date']){ $url = 'bookings/index/'.$data['date']; } else { $url = 'bookings'; }
		$uri = ($uri) ? $uri : 'bookings';
		redirect($uri, 'location');
		#echo anchor($uri, 'Go');
	}





	function cancel(){
		$uri = $this->session->userdata('uri');
		$booking_id = $this->uri->segment(3);
		if($this->M_bookings->Cancel($this->school_id, $booking_id)){
			$msg = $this->load->view('msgbox/info', 'The booking has been <strong>cancelled</strong>.', True);
		} else {
			$msg = $this->load->view('msgbox/error', 'An error occured cancelling the booking.', True);
		}
		$this->session->set_flashdata('saved', $msg);
		if($uri == NULL){ $uri = 'bookings'; }
		redirect($uri, 'redirect');
	}




	function edit(){
		$uri = $this->session->userdata('uri');
		$booking_id = $this->uri->segment(3);

		$booking = $this->M_bookings->Get();

		// Lookups we need if an admin user
		if($this->userauth->CheckAuthLevel(ADMINISTRATOR, $this->authlevel)){
			$body['days'] = $this->M_periods->days;
			$body['rooms'] = $this->M_rooms->Get($this->school_id, NULL);
			$body['periods'] = $this->M_periods->Get();
			$body['weeks'] = $this->M_weeks->Get();
			$body['users'] = $this->M_users->Get();
		}

		$layout['body'] = $this->load->view('bookings/bookings_book', $body, True);

		// Check that the date selected is not in the past
		/*$today = strtotime(date("Y-m-d"));
		$thedate = strtotime($uri['date']);
		if($thedate < $today){
			$layout['body'] = $this->load->view('msgbox/error', 'You cannot make a booking in the past.', True);
		}*/

		$this->load->view('layout', $layout);

	}





	function save(){

	 	// Get ID from form
		$booking_id = $this->input->post('booking_id');

		// Validation rules
		$vrules['booking_id']		= 'required';
		$vrules['date']					= 'max_length[10]|callback__is_valid_date';
		$vrules['use']					= 'max_length[100]';
		$this->validation->set_rules($vrules);

		// Pretty it up a bit for error validation message
		$vfields['booking_id']		= 'Booking ID';
		$vfields['date']					= 'Date';
		$vfields['period_id']			= 'Period';
		$vfields['user_id']				= 'User';
		$vfields['room_id']				= 'Room';
		$vfields['week_id']				= 'Week';
		$vfields['day_num']				= 'Day of week';
		$this->validation->set_fields($vfields);

		// Set the error delims to a nice styled red hint under the fields
		$this->validation->set_error_delimiters('<p class="hint error"><span>', '</span></p>');

    if ($this->validation->run() == FALSE){

      // Validation failed
			if($booking_id != "X"){
				return $this->Edit($booking_id);
			} else {
				return $this->book();
			}

		} else {

			// Validation succeeded

			// Data that goes into database regardless of booking type
			$data['user_id'] = $this->input->post('user_id');
			$data['school_id'] = $this->school_id;
			$data['period_id'] = $this->input->post('period_id');
			$data['room_id'] = $this->input->post('room_id');
			$data['notes'] = $this->input->post('notes');

			// Hmm.... now to see if it's a static booking or recurring or whatever... :-)
			if($this->input->post('date')){
				// Once-only booking

				$date_arr = explode('/', $this->input->post('date'));
				$data['date'] = date("Y-m-d", mktime(0,0,0,$date_arr[1], $date_arr[0], $date_arr[2] ) );
				$data['day_num'] = NULL;
				$data['week_id'] = NULL;
			}

			// If week_id and day_num are specified, its recurring
			if($this->input->post('recurring') && ($this->input->post('week_id') && $this->input->post('day_num'))){
				// Recurring
				$data['date'] = NULL;
				$data['day_num'] = $this->input->post('day_num');
				$data['week_id'] = $this->input->post('week_id');
			}


			#print '<pre>Going to database: '.var_export($data,true).'</pre>';


			// Now see if we are editing or adding
			if($booking_id == 'X'){
				// No ID, adding new record
				#echo 'adding';
				if(!$this->M_bookings->Add($data)){
					$flashmsg = $this->load->view('msgbox/error', sprintf($this->lang->line('dberror'), 'adding', 'booking'), True);
				} else {
					$flashmsg = $this->load->view('msgbox/info', 'The booking has been made.', True);
				}
			} else {
				// We have an ID, updating existing record
				#echo 'editing';
				if(!$this->M_bookings->Edit($booking_id, $data)){
					$flashmsg = $this->load->view('msgbox/error', sprintf($this->lang->line('dberror'), 'editing', 'booking'), True);
				} else {
					$flashmsg = $this->load->view('msgbox/info', 'The booking has been updated.', True);
				}
			} // End of booking_id=X

			#echo $flashmsg;

			// Go back to index
			$this->session->set_flashdata('saved', $flashmsg);

			$uri = $this->session->userdata('uri');
			#if($data['date']){ $url = 'bookings/index/'.$data['date']; } else { $url = 'bookings'; }
			$uri = ($uri) ? $uri : 'bookings';
			redirect($uri, 'location');
			#echo anchor($uri, 'OK');

		}

	}




	function callback__is_valid_date($date){
		$datearr = split('/', $date);
		if(count($datearr) == 3){
			$valid = checkdate($datearr[1], $datarr[0], $datearr[2]);
			if($valid){
				$ret = true;
			} else {
				$ret = false;
				$this->validation->set_message('_is_valid_date', 'Invalid date');
			}
		} else {
			$ret = false;
			$this->validation->set_message('_is_valid_date', 'Invalid date');
		}
		return $ret;
	}



	// Get booking in advance days
	function _booking_advance($school_id){
		$query_str = "SELECT bia FROM school WHERE school_id='$school_id' LIMIT 1";
		$query = $this->db->query($query_str);
		if($query->num_rows() == 1){
			$row = $query->row();
			return $row->bia;
		} else {
			return 'X';
		}
	}







}
?>
