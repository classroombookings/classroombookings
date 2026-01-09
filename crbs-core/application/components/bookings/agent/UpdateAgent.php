<?php

namespace app\components\bookings\agent;

defined('BASEPATH') OR exit('No direct script access allowed');


use app\components\bookings\exceptions\AgentException;
use Permission;


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
	const FEATURE_VIEW_USER = 'view_user';
	const FEATURE_EDIT_USER = 'edit_user';
	const FEATURE_VIEW_NOTES = 'view_notes';
	const FEATURE_EDIT_NOTES = 'edit_notes';

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

		if ( ! booking_editable($this->booking)) throw AgentException::forAccessDenied();

		// Get session of booking
		$this->session = $this->CI->sessions_model->get($this->booking->session_id);
		if ( ! $this->session) throw AgentException::forNoSession();

		$schedule = $this->CI->schedules_model->get_applied_schedule($this->session->session_id, $this->booking->room->room_group_id);

		if (!empty($schedule)) {

			// Load the list of available periods and rooms
			$this->all_periods = $this->CI->periods_model->filtered([
				'schedule_id' => $schedule->schedule_id,
				'bookable' => 1,
			]);

			if (!empty($this->booking->room->room_group_id)) {
				$this->all_rooms = $this->CI->rooms_model->get_bookable_rooms([
					'user_id' => $this->user->user_id,
					'room_group_id' => $this->booking->room->room_group_id,
				]);
			}
		}


		// Get edit mode.
		// This flag helps determine what fields can be edited (important for recurring bookings selection)
		// Options are single, future, or all.
		$this->edit_mode = $this->CI->input->post_get('edit') ?: self::EDIT_ONE;

		$is_booking_owner = ($this->booking->user_id == $this->user->user_id);
		if ($this->booking->repeat_id) {
			$can_view_notes = has_permission(Permission::BK_RECUR_VIEW_OTHER_NOTES, $this->booking->room_id);
			$can_view_user = has_permission(Permission::BK_RECUR_VIEW_OTHER_USERS, $this->booking->room_id);
			$can_set_user = has_permission(Permission::BK_RECUR_SET_USER, $this->booking->room_id);
			$can_edit_other = has_permission(Permission::BK_RECUR_EDIT_OTHER, $this->booking->room_id);
			$can_set_departent = has_permission(Permission::BK_RECUR_SET_DEPT, $this->booking->room_id);
		} else {
			$can_view_notes = has_permission(Permission::BK_SGL_VIEW_OTHER_NOTES, $this->booking->room_id);
			$can_view_user = has_permission(Permission::BK_SGL_VIEW_OTHER_USERS, $this->booking->room_id);
			$can_set_user = has_permission(Permission::BK_SGL_SET_USER, $this->booking->room_id);
			$can_edit_other = has_permission(Permission::BK_SGL_EDIT_OTHER, $this->booking->room_id);
			$can_set_departent = has_permission(Permission::BK_SGL_SET_DEPT, $this->booking->room_id);
		}

		if ($can_set_user) {
			$this->populate_users();
		}

		if ($can_set_departent) {
			$this->populate_departments();
		}

		$this->features = [
			self::FEATURE_DATE => $is_booking_owner || $can_edit_other,
			self::FEATURE_PERIOD => $is_booking_owner || $can_edit_other,
			self::FEATURE_ROOM => $is_booking_owner || $can_edit_other,
			self::FEATURE_VIEW_NOTES => $is_booking_owner || $can_view_notes,
			self::FEATURE_EDIT_NOTES => $is_booking_owner || ($can_view_notes && $can_edit_other),
			self::FEATURE_DEPARTMENT => $can_set_departent,
			self::FEATURE_VIEW_USER => $is_booking_owner || $can_view_user,
			self::FEATURE_EDIT_USER => ($can_view_user && $can_set_user),
		];

		// If a recurring booking future or all is being edited, then it can't be moved in time or space.
		if ($this->booking->repeat_id) {
			if (in_array($this->edit_mode, [self::EDIT_FUTURE, self::EDIT_ALL])) {
				$this->features[self::FEATURE_DATE] = false;
				$this->features[self::FEATURE_PERIOD] = false;
				$this->features[self::FEATURE_ROOM] = false;
			}
		}

		$this->handle_edit();
	}


	private function handle_edit()
	{
		$this->view = 'bookings/edit/form';
		$this->title = lang('booking.edit.title');

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
			$this->message = lang('app.form_error');
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

		if ($this->features[self::FEATURE_EDIT_USER]) {
			$booking_data['user_id'] = $this->CI->input->post('user_id');
		}

		if ($this->features[self::FEATURE_EDIT_NOTES]) {
			$booking_data['notes'] = $this->CI->input->post('notes');
		}

		$update = $this->CI->bookings_model->update($this->booking->booking_id, $booking_data, $this->edit_mode);

		if ($update) {

			$msgs = [
				self::EDIT_ONE => lang('booking.edit.one.success'),
				self::EDIT_FUTURE => lang('booking.edit.future.success'),
				self::EDIT_ALL => lang('booking.edit.all.success'),
			];

			$this->message = $msgs[$this->edit_mode];
			$this->success = TRUE;

			return TRUE;
		}

		$err = $this->CI->bookings_model->get_error();

		$this->message = $err ?: lang('booking.edit.error')
			;

		return FALSE;
	}


	private function get_validation_rules($booking_id)
	{
		$rules = [];

		if ($this->features[self::FEATURE_DATE]) {
			$rules[] = ['field' => 'booking_date', 'label' => 'lang:app.date', 'rules' => sprintf('required|valid_date|no_conflict[%d]', $booking_id)];
		}

		if ($this->features[self::FEATURE_PERIOD]) {
			$rules[] = ['field' => 'period_id', 'label' => 'lang:period.period', 'rules' => 'required|integer'];
		}

		if ($this->features[self::FEATURE_ROOM]) {
			$rules[] = ['field' => 'room_id', 'label' => 'lang:room.room', 'rules' => 'required|integer'];
		}

		if ($this->features[self::FEATURE_DEPARTMENT]) {
			$rules[] = ['field' => 'department_id', 'label' => 'lang:department.department', 'rules' => 'integer'];
		}

		if ($this->features[self::FEATURE_EDIT_USER]) {
			$rules[] = ['field' => 'user_id', 'label' => 'lang:user.user', 'rules' => 'integer'];
		}

		if ($this->features[self::FEATURE_EDIT_NOTES]) {
			$rules[] = ['field' => 'notes', 'label' => 'lang:booking.notes', 'rules' => 'max_length[255]'];
		}

		return $rules;
	}


}
