<?php

namespace app\components\bookings\agent;

defined('BASEPATH') OR exit('No direct script access allowed');


use app\components\bookings\exceptions\AgentException;
use app\components\bookings\Slot;
use \Bookings_model;


/**
 * Agent handles the editing of a booking.
 *
 */
class UpdateAgent extends BaseAgent
{


	// Agent type
	const TYPE = 'update';

	// Features that can be changed
	const FEATURE_DATE = 'date';
	const FEATURE_PERIOD = 'period';
	const FEATURE_ROOM = 'room';
	const FEATURE_DEPARTMENT = 'department';
	const FEATURE_USER = 'user';
	const FEATURE_NOTES = 'notes';

	// Edit modes
	const EDIT_ONE = '1';
	const EDIT_FUTURE = 'future';
	const EDIT_ALL = 'all';


	// Booking being edited
	private $booking;

	// Edit mode
	private $edit_mode;

	// Features
	private $features = [];


	/**
	 * Initialise the Agent with some values.
	 *
	 * Depending on the type of booking, these will be retrieved from different places.
	 *
	 */
	public function load()
	{
		// Load booking that is being edited
		$booking_id = $this->CI->input->post_get('booking_id');
		$includes = [
			'week',
			'period',
			'room',
			'department',
			'user',
		];
		if (!empty($booking_id)) $this->booking = $this->CI->bookings_model->include($includes)->get($booking_id);
		if ( ! $this->booking) throw AgentException::forNoBooking();

		// Get session of booking
		$this->session = $this->CI->sessions_model->get($this->booking->session_id);
		if ( ! $this->session) throw AgentException::forNoSession();

		// Load rooms & periods lists
		//
		$schedule = $this->CI->schedules_model->get_applied_schedule($this->session->session_id, $this->booking->room->room_group_id);

		// Load the list of available periods and rooms (for admins), now we have more required context.
		//
		if ($this->is_admin && !empty($schedule)) {
			$this->all_periods = $this->CI->periods_model->filtered([
				'schedule_id' => $schedule->schedule_id,
				'bookable' => 1,
			]);
		}

		if ($this->is_admin && ! empty($this->booking->room->room_group_id)) {
			$this->all_rooms = $this->CI->rooms_model->get_bookable_rooms([
				'user_id' => $this->user->user_id,
				'room_group_id' => $this->booking->room->room_group_id,
			]);
		}

		//

		// Get edit mode.
		// This flag helps determine what fields can be edited (important for recurring bookings selection)
		// Options are single, future, or all.
		$this->edit_mode = $this->CI->input->post_get('edit')
			? $this->CI->input->post_get('edit')
			: self::EDIT_ONE;

		// Determine what aspects can be changed.
		$default_feature = ($this->is_admin) ? TRUE : FALSE;

		$this->features = [
			self::FEATURE_DATE => $default_feature,
			self::FEATURE_PERIOD => $default_feature,
			self::FEATURE_ROOM => $default_feature,
			self::FEATURE_DEPARTMENT => $default_feature,
			self::FEATURE_USER => $default_feature,
			self::FEATURE_NOTES => $default_feature,
		];

		// Booking owners can change the notes
		if ($this->booking->user_id == $this->user->user_id) {
			$this->features[self::FEATURE_NOTES] = TRUE;
		}

		// If a recurring booking future or all is being edited, then it can't be moved.
		if ($this->booking->repeat_id) {
			if (in_array($this->edit_mode, [self::EDIT_FUTURE, self::EDIT_ALL])) {
				$this->features[self::FEATURE_DATE] = FALSE;
				$this->features[self::FEATURE_PERIOD] = FALSE;
				$this->features[self::FEATURE_ROOM] = FALSE;
			}
		}

		$this->handle_edit();
	}


	private function handle_edit()
	{
		$this->view = 'bookings/edit/form';
		$this->title = 'Edit booking';

		if ($this->CI->input->post()) {
			$this->process_edit_booking();
		}
	}


	/**
	 * Main vars to ensure are in the view.
	 *
	 */
	public function get_view_data()
	{
		$vars = [

			'booking' => $this->booking,
			'features' => $this->features,
			'edit_mode' => $this->edit_mode,

		];

		return $vars;
	}


	/**
	 * Edit a booking
	 *
	 */
	private function process_edit_booking()
	{
		$rules = $this->get_validation_rules($this->booking->booking_id);
		$this->CI->load->library('form_validation');
		$this->CI->form_validation->set_rules($rules);

		if ($this->CI->form_validation->run() == FALSE) {
			$this->message = 'The form contained some invalid values. Please check and try again.';
			return FALSE;
		}

		// Build data array with values that can be updated.
		// These are passed directly to db->update(), so it should only include
		// the fields that are permitted according to the edit mode.

		$booking_data = [];

		if ($this->features[self::FEATURE_DATE]) {
			$booking_data['date'] = $this->CI->input->post('booking_date');
		}

		if ($this->features[self::FEATURE_PERIOD]) {
			$booking_data['period_id'] = $this->CI->input->post('period_id');
		}

		if ($this->features[self::FEATURE_ROOM]) {
			$booking_data['room_id'] = $this->CI->input->post('room_id');
		}

		if ($this->features[self::FEATURE_DEPARTMENT]) {
			$booking_data['department_id'] = $this->CI->input->post('department_id');
		}

		if ($this->features[self::FEATURE_USER]) {
			$booking_data['user_id'] = $this->CI->input->post('user_id');
		}

		if ($this->features[self::FEATURE_NOTES]) {
			$booking_data['notes'] = $this->CI->input->post('notes');
		}

		$update = $this->CI->bookings_model->update($this->booking->booking_id, $booking_data, $this->edit_mode);

		if ($update) {

			$msgs = [
				self::EDIT_ONE => 'The booking has been updated successfully.',
				self::EDIT_FUTURE => 'The booking and all future bookings in the series have been updated.',
				self::EDIT_ALL => 'All bookings in the series have been updated successfully.',
			];

			$this->message = $msgs[$this->edit_mode];
			$this->success = TRUE;

			return TRUE;
		}

		$err = $this->CI->bookings_model->get_error();

		$this->message = ($err)
			? $err
			: 'Could not create booking.';

		return FALSE;
	}


	private function get_validation_rules($booking_id)
	{
		$rules = [];

		if ($this->features[self::FEATURE_DATE]) {
			$rules[] = ['field' => 'booking_date', 'label' => 'Date', 'rules' => sprintf('required|valid_date|no_conflict[%d]', $booking_id)];
		}

		if ($this->features[self::FEATURE_PERIOD]) {
			$rules[] = ['field' => 'period_id', 'label' => 'Period', 'rules' => 'required|integer'];
		}

		if ($this->features[self::FEATURE_ROOM]) {
			$rules[] = ['field' => 'room_id', 'label' => 'Room', 'rules' => 'required|integer'];
		}

		if ($this->features[self::FEATURE_DEPARTMENT]) {
			$rules[] = ['field' => 'department_id', 'label' => 'Department', 'rules' => 'integer'];
		}

		if ($this->features[self::FEATURE_USER]) {
			$rules[] = ['field' => 'user_id', 'label' => 'User', 'rules' => 'integer'];
		}

		if ($this->features[self::FEATURE_NOTES]) {
			$rules[] = ['field' => 'notes', 'label' => 'Notes', 'rules' => 'max_length[255]'];
		}

		return $rules;
	}


}
