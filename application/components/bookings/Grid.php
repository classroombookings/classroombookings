<?php
defined('BASEPATH') OR exit('No direct script access allowed');

namespace app\components\bookings;

use \DateTime;
use \DateInterval;
use \DatePeriod;

use app\components\Calendar;
use app\components\bookings\grid\Controls;
use app\components\bookings\grid\Header;
use app\components\bookings\exceptions\DateException;


class Grid
{


	// CI instance
	private $CI;


	// Context instance
	private $context;



	public function __construct(Context $context)
	{
		$this->CI =& get_instance();

		$this->CI->load->model([
			'sessions_model',
			'dates_model',
			'users_model',
			'rooms_model',
			'access_control_model',
			'periods_model',
			'weeks_model',
		]);

		$this->context = $context;

		$this->controls = new Controls($context);
		$this->header = new Header($context);
	}


	public function render()
	{
		$sections = [
			$this->controls->render(),
			$this->header->render(),
			$this->render_body(),
			$this->render_footer(),
		];

		return implode("\n\n", $sections);
	}


	/**
	 * Render the main content area.
	 *
	 * If an exception is present in the context, the error is displayed instead of the table.
	 *
	 */
	public function render_body()
	{
		// Check for any errors and render it instead of the table.
		if ($this->context->exception) {
			return msgbox('error', $this->context->exception->getMessage());
		}

		return '';
	}


	/**
	 * Render the footer. This includes the legend/key, and recurring bookings controls.
	 *
	 */
	public function render_footer()
	{
		return '';
	}



}
