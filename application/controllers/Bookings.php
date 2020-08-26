<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bookings extends MY_Controller
{


	public function __construct()
	{
		parent::__construct();

		$this->require_logged_in();

		$this->lang->load('bookings');

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
			'users' => $this->users_model->Get(NULL, NULL, NULL),
			'days_list' => $this->periods_model->days,
		);

		if ($this->userauth->is_level(TEACHER) && setting('maintenance_mode')) {
			$this->data['title'] = 'Bookings';
			$this->data['showtitle'] = '';
			$this->data['body'] = '';
			$this->render();
			$this->output->_display();
			exit();
		}

	}



	private function _store_query($data = array())
	{
		$_SESSION['query'] = $data;
	}


	private function _get_query()
	{
		if (array_key_exists('query', $_SESSION))
		{
			return $_SESSION['query'];
		}

		return array();
	}




	function index()
	{
		$query = $this->input->get();

		// $this->session->set_userdata('uri', $this->uri->uri_string());

		if ( ! isset($query['date']) ) {
			$query['date'] = date("Y-m-d");
			/*if( $this->session->userdata('chosen_date') ){
				#echo "session: {$this->session->userdata('chosen_date')}<br />";
				$this->school['chosen_date'] = $this->session->userdata('chosen_date');
			}*/
			// Day number of the chosen date
			$day_num = date('N', strtotime($query['date']));
			#$this->school['chosen_date'] = $chosen_date;
			#$this->session->set_userdata('chosen_date', $this->school['chosen_date']);
		}

		$room_of_user = $this->rooms_model->GetByUser($this->userauth->user->user_id);

		if ( ! isset($query['room'])) {
			if ( ! empty($room_of_user)) {
				$query['room'] = $room_of_user->room_id;
			} else {
				$query['room'] = NULL;
			}
		}

		if ( ! isset($query['direction'])) {
			$query['direction'] = 'forward';
		}

		$this->_store_query($query);

		#$this->school['room'] = $uri['room'];

		$body['html'] = $this->bookings_model->html(array(
			'school' => $this->school,
			'query' => $query,
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
			show_error("Sorry, the requested details cannot be loaded.");
		}

		// $query = $this->_get_query();
		$query = array(
			'direction' => $this->input->post('direction'),
			'date' => $this->input->post('chosen_date'),
		);

		switch ($style['display']) {

			case 'day':

				// Display type is one day at a time - all rooms/periods
				if ($this->input->post('chosen_date')) {
					$datearr = explode('/', $this->input->post('chosen_date'));
					if (count($datearr) != 3) {
						show_error('Invalid date chosen');
					}
					$query['date'] = date("Y-m-d", mktime(0, 0, 0, $datearr[1], $datearr[0], $datearr[2]));
				} else {
					show_error('No date chosen');
				}

			break;

			case 'room':

				if ($this->input->post('room_id')) {
					$query['room'] = $this->input->post('room_id');
				} else {
					show_error('No day selected');
				}

			break;

		}

		$uri = 'bookings/index?' . http_build_query($query);
		redirect($uri);
	}





	function book()
	{
		$query = $this->input->get();

		$this->data['title'] = 'Book a room';
		$this->data['showtitle'] = $this->data['title'];

		// Either no URI, or all URI info specified

		$this->data['hidden'] = new StdClass();

		if (isset($query['period']) && isset($query['room']) && isset($query['date'])) {

			// Create booking data
			$booking = new StdClass();
			$booking->booking_id = NULL;
			$booking->period_id = $query['period'];
			$booking->room_id = $query['room'];
			$booking->date = $query['date'];
			$booking->notes = '';
			$booking->user_id = $this->userauth->user->user_id;

			if ($this->userauth->is_level(ADMINISTRATOR)) {
				$booking->day_num = isset($query['day']) ? $query['day'] : NULL;
				$booking->week_id = isset($query['week']) ? $query['week'] : NULL;

				if (empty($booking->day_num)) {
					$booking->day_num = date('N', strtotime($query['date']));
				}
			}

			$this->data['booking'] = $booking;
			$this->data['hidden'] = (array) $booking;

		}

		// Lookups we need if an admin user
		if ($this->userauth->is_level(ADMINISTRATOR)) {
			$this->data['days'] = $this->periods_model->days;
			$this->data['rooms'] = $this->rooms_model->Get();
			$this->data['periods'] = $this->periods_model->Get();
			$this->data['weeks'] = $this->weeks_model->Get();
			$this->data['users'] = $this->school['users'];
		}

		$prev_query = $this->_get_query();
		$this->data['query_string'] = http_build_query($prev_query);
		$this->data['cancel_uri'] = 'bookings?' . http_build_query($prev_query);
		$this->data['body'] = $this->load->view('bookings/bookings_book', $this->data, TRUE);

		// If we have a date and the user is a teacher, do some extra checks
		//

		if (isset($query['date']) && $this->userauth->is_level(TEACHER)) {

			$booking_status = $this->userauth->can_create_booking($query['date']);

			if ($booking_status->result === FALSE) {

				$messages = [];

				if ( ! $booking_status->in_quota) {
					$msg = "You have reached the maximum number of active bookings (%d).";
					$msg = sprintf($msg, setting('num_max_bookings'));
					$messages[] = msgbox('error', $msg);
				}

				if ( ! $booking_status->is_future_date) {
					$msg = "The chosen date is in the past.";
					$messages[] = msgbox('error', $msg);
				}

				if ( ! $booking_status->date_in_range) {
					$msg = "The chosen date must be less than %d days in the future.";
					$msg = sprintf($msg, setting('bia'));
					$messages[] = msgbox('error', $msg);
				}

				$this->data['body'] = implode("\n", $messages);
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
		$bookings = array();

		foreach ($this->input->post('recurring') as $booking) {
			list($uri, $params) = explode('?', $booking);
			parse_str($params, $data);
			$bookings[] = $data;
		}

		$errcount = 0;

		foreach ($bookings as $booking) {
			$booking_data = array(
				'user_id' => $this->input->post('user_id'),
				'period_id' => $booking['period'],
				'room_id' => $booking['room'],
				'notes' => $this->input->post('notes'),
				'week_id' => $booking['week'],
				'day_num' => $booking['day_num'],
			);

			if ( ! $this->bookings_model->Add($booking_data)) {
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

		$query = $this->_get_query();
		$uri = 'bookings/index?' . http_build_query($query);
		redirect($uri);
	}




	private function process_cancel()
	{
		$id = $this->input->post('cancel');
		$booking = $this->bookings_model->Get($id);
		$user_id = $this->userauth->user->user_id;
		$room = $this->rooms_model->Get($booking->room_id);

		$query = $this->_get_query();
		$uri = 'bookings/index?' . http_build_query($query);

		$can_delete = ( ($this->userauth->is_level(ADMINISTRATOR))
						OR ($user_id == $booking->user_id)
						OR ( ($user_id == $room->user_id) && ($booking->date != NULL) )
					);

		if ( ! $can_delete) {
			$this->session->set_flashdata('saved', msgbox('error', "You do not have the correct privileges to cancel this booking."));
			return redirect($uri);
		}

		if ($this->bookings_model->Cancel($id)){
			$msg = msgbox('info', 'The booking has been cancelled.');
		} else {
			$msg = msgbox('error', 'An error occured cancelling the booking.');
		}

		$this->session->set_flashdata('saved', $msg);
		redirect($uri);
	}




	function edit($booking_id)
	{
		$booking = $this->bookings_model->Get($booking_id);

		$query = $this->_get_query();
		$uri = 'bookings?' . http_build_query($query);

		$can_edit = ( $this->userauth->is_level(ADMINISTRATOR) OR ($this->userauth->user->user_id == $booking->user_id));

		if ( ! $can_edit) {
			$this->session->set_flashdata('saved', msgbox('error', "You do not have the correct privileges to cancel this booking."));
			return redirect($uri);
		}

		$this->data['title'] = 'Edit booking';
		$this->data['showtitle'] = $this->data['title'];
		$this->data['cancel_uri'] = 'bookings?' . http_build_query($query);

		// Lookups we need if an admin user
		if ($this->userauth->is_level(ADMINISTRATOR)) {
			$this->data['days'] = $this->periods_model->days;
			$this->data['rooms'] = $this->rooms_model->Get();
			$this->data['periods'] = $this->periods_model->Get();
			$this->data['weeks'] = $this->weeks_model->Get();
			$this->data['users'] = $this->school['users'];
		}

		$this->data['booking'] = $booking;
		$this->data['hidden'] = (array) $booking;

		$this->data['body'] = $this->load->view('bookings/bookings_book', $this->data, TRUE);

		return $this->render();
	}





	function save()
	{
		// Get ID from form
		$booking_id = $this->input->post('booking_id');

		$this->load->library('form_validation');
		$this->form_validation->set_rules('booking_id', 'Booking ID', 'integer');
		$this->form_validation->set_rules('date', 'Date', 'max_length[10]');
		$this->form_validation->set_rules('use', 'Notes', 'max_length[100]');
		$this->form_validation->set_rules('period_id', 'Period', 'integer');
		$this->form_validation->set_rules('user_id', 'User', 'integer');
		$this->form_validation->set_rules('room_id', 'Room', 'integer');
		$this->form_validation->set_rules('week_id', 'Week', 'integer');
		$this->form_validation->set_rules('day_num', 'Day of week', 'integer');

		if ( ! $this->input->post('day_num')) {
			$this->form_validation->set_rules('date', 'Date', 'max_length[10]|callback_valid_date');
		}

		if ($this->form_validation->run() == FALSE) {
			return (empty($booking_id) ? $this->book() : $this->edit($booking_id));
		}

		$booking_data = array(
			'user_id' => $this->input->post('user_id'),
			'period_id' => $this->input->post('period_id'),
			'room_id' => $this->input->post('room_id'),
			'notes' => $this->input->post('notes'),
			'booking_id' => $this->input->post('booking_id'),
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
			$this->data['notice'] = $flashmsg;
			// $this->session->set_flashdata('saved', $flashmsg);
			return (empty($booking_id) ? $this->book() : $this->edit($booking_id));
		}

		$query = $this->_get_query();
		$uri = 'bookings/index?' . http_build_query($query);
		redirect($uri);
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
			'booking_id' => $data['booking_id'],
			'week_id' => $data['week_id'],
			'day_num' => $data['day_num'],
		));

		return count($bookings) == 0;
	}



	private function _persist_booking($booking_id = NULL, $booking_data = array())
	{
		if (empty($booking_id)) {

			unset($booking_data['booking_id']);

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


}
