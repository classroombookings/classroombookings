<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH . 'third_party/simple_bitmask.php');

class Bookings extends MY_Controller
{


	public function __construct()
	{
		parent::__construct();

		$this->require_logged_in();

		$this->load->model(array(
			'crud_model',
			'rooms_model',
			'periods_model',
			'weeks_model',
			'users_model',
			'holidays_model',
			'bookings_model',
		));

		$this->school = array(
			'users' => $this->users_model->Get(),
			'days_list' => $this->periods_model->days,
		);
	}




	function index()
	{
		$uri = $this->uri->uri_to_assoc(3);

		$this->session->set_userdata('uri', $this->uri->uri_string());

		if ( ! isset($uri['date']) ) {
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

		$room_of_user = $this->rooms_model->GetByUser($this->session->userdata('user_id'));

		if (isset($uri['room'])) {
			$uri['room'] = $uri['room'];
		} else {
			if ($room_of_user != False){
				$uri['room'] = $room_of_user->room_id;
			} else {
				$uri['room'] = false;
			}
		}

		#$this->school['room'] = $uri['room'];

		$body['html'] = $this->bookings_model->html(array(
			'date' => strtotime($uri['date']),
			'room_id' => $uri['room'],
			'school' => $this->school,
			'uri' => $uri,
		));

		/*$body['html'] = $this->M_bookings->htmltable(
			$this->school_id,
			'day',
			'periods',
			$chosen_date,
			$this->school
		);*/

		$this->data['title'] = 'Bookings';
		$this->data['showtitle'] = '';
		$this->data['body'] = $this->session->flashdata('saved');
		$this->data['body'] .= $body['html'];

		return $this->render();
	}




	/**
	* This function takes the date that was POSTed and loads the view()
	*/
	function load()
	{
		$style = $this->bookings_model->BookingStyle();

		#$chosen_date = $this->input->post('chosen_date');

		$this->load->library('form_validation');
		$this->form_validation->set_rules('chosen_date', 'Date', 'max_length[10]|callback_valid_date');
		$this->form_validation->set_rules('room_id', 'Room', 'integer');
		$this->form_validation->set_rules('direction', 'Diretion', '');

		if ($this->form_validation->run() == FALSE) {
			print_r($_POST);
			echo validation_errors();
			show_error("Sorry, the requested details cannot be loaded.");
		}

		switch ($style['display']) {

			case 'day':

				// Display type is one day at a time - all rooms/periods
				if ($this->input->post('chosen_date')) {
					$datearr = explode('/', $this->input->post('chosen_date'));
					if (count($datearr) == 3) {
						$chosen_date = sprintf("%s-%s-%s", $datearr[2], $datearr[1], $datearr[0]);
						$url = sprintf('bookings/index/date/%s/direction/%s', $chosen_date, $this->input->post('direction'));
						return redirect($url);
					} else {
						show_error('Invalid date chosen');
					}
				} else {
					show_error('No date chosen');
				}
			break;

			case 'room':
				if ($this->input->post('room_id')) {
					$url = sprintf(
						'bookings/index/date/%s/room/%s/direction/%s',
						$this->input->post('chosen_date'),
						$this->input->post('room_id'),
						$this->input->post('direction')
					);
					return redirect($url);
				} else {
					show_error('No day selected');
				}
			break;

		}
	}





	function book()
	{
		$uri = $this->uri->uri_to_assoc(3);

		$this->data['title'] = 'Book a room';
		$this->data['showtitle'] = $this->data['title'];

		$seg_count = $this->uri->total_segments();
		if ($seg_count != 2 && $seg_count != 12) {

			// Not all info in URI
			$this->data['body'] = msgbox('error', 'Not enough information specified to book a room.');

		} else {

			// Either no URI, or all URI info specified

			$this->data['hidden'] = array();

			// 12 segments means we have all info - adding a booking
			if ($seg_count == 12) {

				// Create array of data from the URI
				$booking = array(
					'booking_id' => NULL,
					'period_id' => $uri['period'],
					'room_id' => $uri['room'],
					'date'	=> date("d/m/Y", strtotime($uri['date'])),
				);

				if ($this->userauth->CheckAuthLevel(ADMINISTRATOR)) {
					$booking['day_num'] = $uri['day'];
					$booking['week_id']	= $uri['week'];
				} else {
					$booking['user_id'] = $this->session->userdata('user_id');
				}

				$this->data['booking'] = $booking;
				$this->data['hidden'] = $booking;

			}

			// Lookups we need if an admin user
			if ($this->userauth->CheckAuthLevel(ADMINISTRATOR)) {
				$this->data['days'] = $this->periods_model->days;
				$this->data['rooms'] = $this->rooms_model->Get();
				$this->data['periods'] = $this->periods_model->Get();
				$this->data['weeks'] = $this->weeks_model->Get();
				$this->data['users'] = $this->users_model->Get();
			}

			$this->data['body'] = $this->load->view('bookings/bookings_book', $this->data, TRUE);

			// Check that the date selected is not in the past
			$today = strtotime(date("Y-m-d"));
			$thedate = strtotime($uri['date']);

			if ($this->userauth->CheckAuthLevel(TEACHER)) {
				if ($thedate < $today) {
					$this->data['body'] = msgbox('error', 'You cannot make a booking in the past.');
				}
			}

			// Now see if user is allowed to book in advance
			if ($this->userauth->CheckAuthLevel(TEACHER, $this->authlevel)) {
				$bia = (int) $this->_booking_advance();
				if ($bia > 0) {
					$date_forward = strtotime("+$bia days", $today);
					if($thedate > $date_forward){
						$this->data['body'] =  msgbox('error', 'You can only book '.$bia.' days in advance.');
					}

				}
			}

		}

		return $this->render();
	}





	function recurring()
	{
		foreach ($this->input->post('recurring') as $booking) {
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




	public function valid_date($date)
	{
		if (strpos($date, '/') !== FALSE) {
			$datearr = explode('/', $date);
			$valid = checkdate($datearr[1], $datarr[0], $datearr[2]);
		} elseif (strpos($date, '-') !== FALSE) {
			$datearr = explode('-', $date);
			print_r($datearr);
			$valid = checkdate($datearr[1], $datearr[2], $datearr[0]);
		} else {
			$this->form_validation->set_message('valid_date', 'Invalid date');
			return FALSE;
		}

		if ($valid) {
			return TRUE;
		}

		$this->form_validation->set_message('valid_date', 'Invalid date');
		return FALSE;
	}




	// Get booking in advance days
	function _booking_advance()
	{
		$query_str = "SELECT bia FROM school LIMIT 1";
		$query = $this->db->query($query_str);
		if ($query->num_rows() == 1) {
			$row = $query->row();
			return $row->bia;
		} else {
			return 'X';
		}
	}




}
