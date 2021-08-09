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


		$form_attrs = [
			'up-layer' => 'new modal',
			'up-size' => 'large',
			'up-target' => '.bookings-create',
			'up-dismissable' => 'button key',
		];
		$form_hidden = ['step' => 'selection'];
		$form_open = form_open($this->context->base_uri . '/create/multi', $form_attrs, $form_hidden);
		$form_close = form_close();

		if ($this->CI->userauth->is_level(ADMINISTRATOR)) {
			$out = "{$controls}\n{$header}\n{$form_open}\n{$body}\n{$footer}\n{$form_close}\n";
		} else {
			$out = "{$controls}\n{$header}\n{$body}\n{$footer}";
		}

		return "<div id='bookings_grid' up-hungry>{$out}</div>";
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

		if ($this->context->exception) {
			return '';
		}

		$input = form_checkbox([
			'name' => 'multi_select',
			'id' => 'multi_select',
			'value' => '1',
			'checked' => false,
			'up-switch' => '.multi-select-content',
		]);
		$input_label = form_label($input . 'Enable multiple selection', 'multi_select', ['class' => 'ni', 'style' => 'display: inline-block;margin-bottom:8px']);

		$check_block = "<div class='block b-50' style='text-align:right'>{$input_label}</div>";

		$submit_button = "<button type='submit' style='margin-left:32px' class='multi-select-content'>Create bookings...</button>";
		$button_block = "<div class='block b-50'>{$submit_button}</div>";

		return "<div class='cssform block-group' style='margin-top:32px;'>{$check_block}{$button_block}</div>";
	}



}
