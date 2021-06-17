<?php
defined('BASEPATH') OR exit('No direct script access allowed');

namespace app\components\bookings;

use \DateTime;
use \DateInterval;
use \DatePeriod;

use app\components\Calendar;
use app\components\bookings\grid\Controls;
use app\components\bookings\grid\Header;
use app\components\bookings\grid\Table;
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
		$this->table = new Table($context);
	}


	public function render()
	{
		$controls = $this->controls->render();
		$header = $this->header->render();

		$body = $this->render_body();
		$footer = $this->render_footer();

		// @TODO wrap body + footer in a <form> tag for admin recurring bookings.
		// footer will include the inputs for that.
		$form_attrs = [
			'up-history' => 'false',
			'up-modal' => '.bookings-create',
		];
		$form_hidden = [];
		$form_open = form_open($this->context->base_uri . '/create/multi', $form_attrs, $form_hidden);
		$form_close = form_close();

		if ($this->CI->userauth->is_level(ADMINISTRATOR)) {
			$body = $form_open . $body . $form_close;
		}

		return $controls . $header . $body . $footer;
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

		return $this->table->render();
	}


	/**
	 * Render the footer. This includes the legend/key, and recurring bookings controls.
	 *
	 */
	public function render_footer()
	{
		if ( ! $this->CI->userauth->is_level(ADMINISTRATOR)) {
			return '';
		}

		$input = form_checkbox([
			'name' => 'multi_select',
			'id' => 'multi_select',
			'value' => '1',
			'checked' => false,
			'up-switch' => '.multi-select-content',
		]);
		$input_label = form_label($input . 'Enable multiple selection', 'multi_select', ['class' => 'ni', 'style' => 'display: inline-block']);

		$submit_button = "<button type='submit' name='action' value='multi' style='margin-left:32px'>Create bookings...</button>";

		$out = $input_label . $submit_button;

		return "<div class='cssform' style='margin-top:32px; text-align: center'>{$out}</div>";
	}



}
