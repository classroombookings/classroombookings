<?php

namespace app\components\bookings\grid;

defined('BASEPATH') OR exit('No direct script access allowed');

use app\components\bookings\Context;


class Header
{


	// CI instance
	private $CI;


	// Context instance
	private $context;


	public function __construct(Context $context)
	{
		$this->CI =& get_instance();

		$this->CI->load->helper('week');

		$this->context = $context;
	}


	/**
	 * Render the Date or Room selectors.
	 *
	 */
	public function render()
	{
		if ( ! $this->context->datetime) {
			return '';
		}

		$data = $this->get_data();

		if (empty($data)) {
			return '';
		}

		return $this->CI->load->view('bookings_grid/header', $data, TRUE);
	}


	private function get_data()
	{
		$data = [
			'prev' => FALSE,
			'next' => FALSE,
			'title' => '',
			'week' => $this->context->timetable_week,
		];

		switch ($this->context->display_type) {

			case 'day':

				$prev_label = '&larr; Back';
				$next_label = 'Next &rarr;';

				$long_date = $this->context->datetime->format(setting('date_format_long'));

				$data['title'] = $this->context->timetable_week
					? $long_date . ' - ' . html_escape($this->context->timetable_week->name)
					: $long_date;

				break;

			case 'room':

				$prev_label = '&larr; Previous week';
				$next_label = 'Next week &rarr;';

				$start_date = $this->context->week_start->format(setting('date_format_long'));
				$week_text = sprintf('Week commencing %s', $start_date);

				$data['title'] = $this->context->timetable_week
					? $week_text . ' - ' . html_escape($this->context->timetable_week->name)
					: $week_text;

				break;

			default:

				return $data;

		}

		// Links
		//

		$params = $this->context->get_query_params();

		if ($this->context->prev_date) {

			$params['date'] = $this->context->prev_date->format('Y-m-d');
			$params['dir'] = 'prev';
			$query = http_build_query($params);

			$data['prev'] = [
				'label' => $prev_label,
				'url' => site_url($this->context->base_uri) . '?' . $query,
			];
		}

		if ($this->context->next_date) {

			$params['date'] = $this->context->next_date->format('Y-m-d');
			$params['dir'] = 'next';
			$query = http_build_query($params);

			$data['next'] = [
				'label' => $next_label,
				'url' => site_url($this->context->base_uri) . '?' . $query,
			];
		}

		return $data;
	}


}
