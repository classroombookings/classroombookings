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



	/**
	 * Process a form action from the bookings table
	 *
	 */
	public function action()
	{
		if ($this->input->post('cancel')) {
			return $this->process_cancel();
		}

		if ($this->input->post('recurring')) {
			return $this->process_recurring();
		}
	}




	private function process_recurring()
	{
		foreach ($this->input->post('recurring') as $booking) {
			$arr = explode('/', $booking);
			$max = count($arr);
			#print_r($arr);
			$booking = array();
			for ($i=0;$i<count($arr);$i=$i+2){
				$booking[$arr[$i]] = $arr[$i+1];
			}
			$bookings[] = $booking;
		}
		$errcount = 0;
		#echo "<hr>";
		#echo "<pre>".var_export($bookings,true)."</pre>";
		foreach ($bookings as $booking){
			$booking_data = array(
				'user_id' => $this->input->post('user_id'),
				'period_id' => $booking['period'],
				'room_id' => $booking['room'],
				'notes' => $this->input->post('notes'),
				'week_id' => $booking['week'],
				'day_num' => $booking['day'],
			);

			if ( ! $this->bookings_model->Add($booking_data)){
				$errcount++;
			}
		}

		if ($errcount > 0) {
			$flashmsg = msgbox('error', 'One or more bookings could not be made.');
		} else {
			$flashmsg = msgbox('info', 'The bookings were created successfully.');
		}

		$this->session->set_userdata('notes', $booking_data['notes']);

		// Go back to index
		$this->session->set_flashdata('saved', $flashmsg);

		$uri = $this->session->userdata('uri');
		if (empty($uri)) {
			$uri = 'bookings';
		}
		redirect($uri, 'location');
	}




	private function process_cancel()
	{
		$id = $this->input->post('cancel');
		$booking = $this->bookings_model->Get($id);
		$user_id = $this->session->userdata('user_id');
		$room = $this->rooms_model->Get($booking->room_id);

		$uri = $this->session->userdata('uri');
		if (empty($uri)){
			$uri = 'bookings';
		}

		$can_delete = ( ($this->userauth->CheckAuthLevel(ADMINISTRATOR))
						OR ($user_id == $booking->user_id)
						OR ( ($user_id == $room->user_id) && ($booking->date != NULL) )
					);

		if ( ! $can_delete) {
			$this->session->set_flashdata('saved', msgbox('error', "You do not have the correct privileges to cancel this booking."));
			return redirect($uri, 'redirect');
		}

		if ($this->bookings_model->Cancel($id)){
			$msg = msgbox('info', 'The booking has been cancelled.');
		} else {
			$msg = msgbox('error', 'An error occured cancelling the booking.');
		}

		$this->session->set_flashdata('saved', $msg);
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





	function save()
	{
		// Get ID from form
		$booking_id = $this->input->post('booking_id');

		$this->load->library('form_validation');
		$this->form_validation->set_rules('booking_id', 'Booking ID', 'integer');
		$this->form_validation->set_rules('date', 'Date', 'max_length[10]|callback_valid_date');
		$this->form_validation->set_rules('use', 'Notes', 'max_length[100]');
		$this->form_validation->set_rules('period_id', 'Period', 'integer');
		$this->form_validation->set_rules('user_id', 'User', 'integer');
		$this->form_validation->set_rules('room_id', 'Room', 'integer');
		$this->form_validation->set_rules('week_id', 'Week', 'integer');
		$this->form_validation->set_rules('day_num', 'Day of week', 'integer');

		if ($this->form_validation->run() == FALSE) {
			return (empty($booking_id) ? $this->book() : $this->edit($booking_id));
		}

		$booking_data = array(
			'user_id' => $this->input->post('user_id'),
			'period_id' => $this->input->post('period_id'),
			'room_id' => $this->input->post('room_id'),
			'notes' => $this->input->post('notes'),
		);

		// Determine if this booking is recurring or static.
		if ($this->input->post('date')) {
			$date_arr = explode('/', $this->input->post('date'));
			$booking_data['date'] = date("Y-m-d", mktime(0, 0, 0, $date_arr[1], $date_arr[0], $date_arr[2]));
			$booking_data['day_num'] = NULL;
			$booking_data['week_id'] = NULL;
		}

		if ($this->input->post('recurring') && $this->input->post('week_id') && $this->input->post('day_num')) {
			$booking_data['date'] = NULL;
			$booking_data['day_num'] = $this->input->post('day_num');
			$booking_data['week_id'] = $this->input->post('week_id');
		}

		if ($this->_check_unique_booking($booking_data)) {
			$this->_persist_booking($booking_id, $booking_data);
		} else {
			$flashmsg = msgbox('exclamation', "There is already a booking for that date, period and room.");
			$this->session->set_flashdata('saved', $flashmsg);
		}

		$uri = $this->session->userdata('uri');
		$uri = ($uri) ? $uri : 'bookings';
		redirect($uri, 'location');
	}




	public function valid_date($date)
	{
		if (strpos($date, '/') !== FALSE) {
			$datearr = explode('/', $date);
			$valid = checkdate($datearr[1], $datearr[0], $datearr[2]);
		} elseif (strpos($date, '-') !== FALSE) {
			$datearr = explode('-', $date);
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



	private function _check_unique_booking($data)
	{
		$bookings = $this->bookings_model->GetUnique(array(
			'date' => $data['date'],
			'period_id' => $data['period_id'],
			'room_id' => $data['room_id'],
		));

		return count($bookings) == 0;
	}



	private function _persist_booking($booking_id = NULL, $booking_data = array())
	{
		if (empty($booking_id)) {

			$booking_id = $this->bookings_model->Add($booking_data);

			if ($booking_id) {
				$flashmsg = msgbox('info', "The booking has been made.");
			} else {
				$line = sprintf($this->lang->line('crbs_action_dberror'), 'adding');
				$flashmsg = msgbox('error', $line);
			}

		} else {

			if ($this->bookings_model->Edit($booking_id, $booking_data)) {
				$flashmsg = msgbox('info', "The booking has been updated.");
			} else {
				$line = sprintf($this->lang->line('crbs_action_dberror'), 'editing');
				$flashmsg = msgbox('error', $line);
			}

		}

		$this->session->set_flashdata('saved', $flashmsg);
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
