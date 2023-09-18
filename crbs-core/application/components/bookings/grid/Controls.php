<?php

namespace app\components\bookings\grid;

defined('BASEPATH') OR exit('No direct script access allowed');

use app\components\bookings\Context;


class Controls
{


	// CI instance
	private $CI;


	// Context instance
	private $context;


	public function __construct(Context $context)
	{
		$this->CI =& get_instance();
		$this->context = $context;
	}


	/**
	 * Render the Date or Room selectors.
	 *
	 */
	public function render()
	{
		$display_view = $this->render_display_view();
		$session_view = $this->render_session_view();

		$row = "<div class='block b-70'>{$display_view}</div><div class='block b-30' style='text-align:right'>{$session_view}</div>";
		$group = "<div class='block-group bookings-grid-controls'>{$row}</div>";
		return $group;
	}


	private function render_display_view()
	{
		if ( ! $this->context->session) return '&nbsp;';

		$view = FALSE;

		switch ($this->context->display_type) {

			case 'day':

				$query_params = $this->context->get_query_params();
				unset($query_params['date']);

				$data = [
					'query_params' => $query_params,
					'form_action' => $this->context->base_uri,
					'datetime' => $this->context->datetime,
				];

				$view = 'bookings_grid/controls/day';

				break;

			case 'room':

				$rooms = [];
				foreach ($this->context->rooms as $room) {
					$rooms[ $room->room_id ] = html_escape($room->name);
				}

				$query_params = $this->context->get_query_params();
				unset($query_params['room']);

				$data = [
					'room' => $this->context->room,
					'rooms' => $rooms,
					'query_params' => $query_params,
					'form_action' => $this->context->base_uri,
					'datetime' => $this->context->datetime,
					'week_start' => $this->context->week_start,
				];

				$view = 'bookings_grid/controls/room';

				break;
		}

		if ($view) {
			return $this->CI->load->view($view, $data, TRUE);
		}

		return '';
	}


	private function render_session_view()
	{
		$show_all = ($this->context->user && $this->context->user->authlevel == ADMINISTRATOR)
			? TRUE
			: FALSE;


		if ($show_all) {

			$session_options = [];

			if ($this->context->active_sessions) {
				foreach ($this->context->active_sessions as $session) {
					$session_options['Current and future'][$session->session_id] = $session->name;
				}
			}

			if ($this->context->past_sessions) {
				foreach ($this->context->past_sessions as $session) {
					$session_options['Past'][$session->session_id] = $session->name;
				}
			}

		} else {

			// No available sessions: skip.
			if ( ! is_array($this->context->available_sessions)) {
				return '';
			}

			// Only 1 session *and* is current: skip.
			$num_sessions = count($this->context->available_sessions);
			$selected_is_current = ($this->context->session && $this->context->session->is_current == '1');

			if ($selected_is_current && $num_sessions == 1) {
				return '';
			}

			$session_options = ['' => ''];
			foreach ($this->context->available_sessions as $session) {
				$session_options[$session->session_id] = $session->name;
			}

		}

		$query_params = $this->context->get_query_params();

		$data = [
			'available_sessions' => $this->context->available_sessions,
			'selected_session_id' => $this->context->session_id,
			'form_action' => site_url('bookings/change_session'),
			'query_params' => $query_params,
			'session_options' => $session_options,
		];

		return $this->CI->load->view('bookings_grid/controls/session', $data, TRUE);
	}


}
