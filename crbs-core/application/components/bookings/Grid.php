<?php

namespace app\components\bookings;

defined('BASEPATH') OR exit('No direct script access allowed');

use app\components\bookings\grid\Controls;
use app\components\bookings\grid\Header;
use app\components\bookings\grid\Table;


class Grid
{

	private \MY_Controller $CI;

	private Context $context;
	private Controls $controls;
	private Header $header;
	private Table $table;

	public function __construct(Context $context)
	{
		$this->CI =& get_instance();

		$this->CI->load->model([
			'sessions_model',
			'dates_model',
			'users_model',
			'rooms_model',
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

		$group_buttons = $this->render_group_buttons();
		$body = $this->render_body();
		$footer = $this->render_footer();
		$legend = $this->render_legend();

		$params = $this->context->get_query_params();
		$query_str = http_build_query($params);

		// Multi-select form: create bookings
		//
		$form_attrs = [
			'up-layer' => 'new modal',
			'up-size' => 'large',
			'up-target' => '.bookings-create',
			'up-dismissable' => 'button key',
			'id' => 'form_create_multi',
		];
		$form_hidden = ['step' => 'selection', 'params' => $query_str];
		$form_open = form_open($this->context->base_uri . '/create/multi', $form_attrs, $form_hidden);
		$form_close = form_close();
		$create_form = $form_open . $form_close;

		// Multi-select form: cancel existing bookings
		//
		$form_attrs = [
			'up-layer' => 'new modal',
			'up-size' => 'large',
			'up-target' => '.bookings-cancel-multi',
			'up-dismissable' => 'button key',
			'id' => 'form_cancel_multi',
		];
		$form_hidden = ['step' => 'selection', 'params' => $query_str];
		$form_open = form_open($this->context->base_uri . '/cancel_multi', $form_attrs, $form_hidden);
		$form_close = form_close();
		$cancel_form = $form_open . $form_close;

		$style = $this->render_style();

		$out = "{$controls}\n{$header}\n{$group_buttons}\n{$body}\n{$footer}\n{$legend}\n{$create_form}\n{$cancel_form}\n";

		return "{$style}<div id='bookings_grid' up-hungry>{$out}</div>";
	}


	private function render_group_buttons()
	{
		if ($this->context->display_type == 'room') return '<br>';

		$items = [];
		$has_group = $this->context->room_group !== FALSE;
		$params = $this->context->get_query_params();

		foreach ($this->context->room_groups as $group) {

			$is_open = $has_group && $this->context->room_group->room_group_id == $group->room_group_id;

			$query = $params;
			$query['room_group'] = $group->room_group_id;
			$query_str = http_build_query($query);

			$url = site_url($this->context->base_uri) . '?' . $query_str;

			$items[] = [
				'url' => $url,
				'title' => sprintf('%s <span>(%d)</span>', html_escape($group->name), $group->room_count),
				'active' => $is_open,
				'attrs' => 'up-follow up-preload'
			];
		}
		return buttonlist($items);
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

		$ms = $this->render_multiselect_controls('header');

		$table = $this->table->render();

		return $ms . $table;
	}


	/**
	 * Render the footer. This includes the legend/key, and recurring bookings controls.
	 *
	 */
	public function render_footer()
	{
		if ($this->context->exception) {
			return '';
		}

		return $this->render_multiselect_controls('footer');
	}


	private function render_multiselect_controls($position = '')
	{

		switch ($position) {
			case 'header':
				$css = 'margin-bottom:8px;';
				break;
			case 'footer':
				$css = 'margin-top:8px;margin-bottom:32px;';
				break;
		}

		$toggle_true = '<span class="multi-select-content" style="display:none;font-size:110%" data-multi="true">&#9745;</span>';
		$toggle_false = '<span class="multi-select-content" style="font-size:110%" data-multi="false">&#9745;</span>';

		$toggle_btn = form_button([
			'type' => 'button',
			'data-script' => 'on click trigger toggle_ms on .bookings-grid',
			'content' => $toggle_false . $toggle_true . ' ' . lang('booking.toggle_multi_select'),
			'style' => 'padding:2px 6px;line-height:18x;margin-left:32px',
		]);

		$create_btn = form_button([
			'type' => 'submit',
			'style' => 'padding:4px 6px;line-height:18x;border:1px solid #2ECC40;background:#EBFAEC;display:none',
			'class' => 'multi-select-content',
			'data-multi' => 'true',
			'form' => 'form_create_multi',
			'content' => '&#10004; ' . lang('booking.create_bookings') . '...',
		]);

		$cancel_btn = form_button([
			'type' => 'submit',
			'style' => 'padding:4px 6px;line-height:18x;margin-left:16px;border:1px solid #FF4136;background:#FFEDEB;display:none',
			'class' => 'multi-select-content',
			'data-multi' => 'true',
			'form' => 'form_cancel_multi',
			'content' => '&#10008; ' . lang('booking.action.cancel_bookings') . '...',
		]);

		$button_block = "<div class='block b-100' style='text-align:right;'>{$create_btn}{$cancel_btn}{$toggle_btn}</div>";

		return "<div class='cssform block-group multi-select-controller' style='padding:8px;{$css}'>{$button_block}</div>";
	}


	/**
	 * Render table legend.
	 *
	 */
	public function render_legend()
	{
		$cells = [];

		$legend = html_escape(lang('booking.legend.legend'));
		$cells[] = "<td width='25%' align='right' valign='middle'><strong>{$legend}:</strong></td>";

		$label = html_escape(lang('booking.legend.free'));
		$class = 'bookings-grid-slot booking-status-available';
		$cells[] = "<td class='{$class}' width='25%' align='center'><span class='bookings-grid-button'>{$label}</div></td>";

		$label = html_escape(lang('booking.legend.static'));
		$class = 'bookings-grid-slot booking-status-booked booking-status-booked-recurring';
		$cells[] = "<td class='{$class}' width='25%' align='center'><span class='bookings-grid-button'>{$label}</div></td>";

		$label = html_escape(lang('booking.legend.staff'));
		$class = 'bookings-grid-slot booking-status-booked booking-status-booked-single';
		$cells[] = "<td class='{$class}' width='25%' align='center'><span class='bookings-grid-button'>{$label}</div></td>";

		$cells_str = implode("\n", $cells);

		$table = "<table
			border='0'
			cellpadding='4'
			cellspacing='4'
			class='bookings-grid is-legend'
			align='center'
			style='border:1px solid #000;border-width:0px 0px;margin:16px auto;'
		><tr>{$cells_str}</tr></table>";

		return $table;
	}


	private function render_style()
	{
		if ( ! is_array($this->context->weeks)) return '';

		$css = '';
		foreach ($this->context->weeks as $week) {
			$css .= week_calendar_css($week);
		}

		return "<style type='text/css'>{$css}</style>";
	}



}
