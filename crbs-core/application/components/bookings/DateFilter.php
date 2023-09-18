<?php

namespace app\components\bookings;

defined('BASEPATH') OR exit('No direct script access allowed');


// use \DateTime;
// use \DateInterval;
// use \DatePeriod;

use app\components\Calendar;


class DateFilter
{


	// CI instance
	private $CI;


	// Context instance
	private $context;


	public function __construct(Context $context)
	{
		$this->CI =& get_instance();

		$this->CI->load->model([
			'weeks_model',
			'dates_model',
		]);

		$this->context = $context;
	}


	public function render()
	{
		$params = $this->context->get_query_params();

		$data = [
			'params' => $params,
			'current_room' => $params['room'] ?? null,
			'current_date' => $params['date'] ?? null,
			'calendar' => $this->render_calendar(),
		];

		return $this->render_calendar();
	}


	private function render_calendar()
	{
		$calendar = new Calendar([
			'session' => $this->context->session,
			'weeks' => $this->CI->weeks_model->get_all(),
			'dates' => $this->CI->dates_model->get_by_session($this->context->session->session_id),
			'mode' => Calendar::MODE_NAVIGATE,
			'month_class' => 'session-calendar',
			'selected_datetime' => $this->context->datetime,
			'nav_url_format' => $this->get_url_format(),
		]);

		return $calendar->generate_full_session();
	}


	private function get_url_format()
	{
		$params = $this->context->get_query_params();
		$params['date'] = '_DATE_';
		$query_string = http_build_query($params);
		$query_string = str_replace('_DATE_', '%s', $query_string);
		$url = site_url('bookings') . '?' . $query_string;
		return $url;
	}

}
