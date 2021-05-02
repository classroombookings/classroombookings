<?php
defined('BASEPATH') OR exit('No direct script access allowed');

namespace app\components\bookings\grid;

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
				];

				$view = 'bookings_grid/controls/room';

				break;
		}

		if ($view) {
			return $this->CI->load->view($view, $data, TRUE);
		}

		return '';
	}


}
