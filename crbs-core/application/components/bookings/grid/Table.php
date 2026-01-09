<?php

namespace app\components\bookings\grid;

defined('BASEPATH') OR exit('No direct script access allowed');

use app\components\Calendar;
use app\components\bookings\Context;
use app\components\bookings\Slot;


class Table
{


	// CI instance
	private $CI;


	// Context instance
	private $context;

	// Column width
	private $col_width = FALSE;


	public function __construct(Context $context)
	{
		$this->CI =& get_instance();
		$this->context = $context;

		// Determine width of columns based on number of items
		if ( ! $this->context->exception) {

			$col_count = match ($this->context->columns) {
				'periods' => is_array($this->context->periods) ? count($this->context->periods) + 1 : 1,
				'days' => is_array($this->context->dates) ? count($this->context->dates) + 1 : 1,
				'rooms' => is_array($this->context->rooms) ? count($this->context->rooms) + 1 : 1,
				default => 0,
			};

			$this->col_width = ($col_count > 0)
				? sprintf('%d%%', round(100 / $col_count))
				: '5%';

		}
	}


	public function get_columns()
	{
		$cols = [
			'periods' => ['name' => 'period', 'items' => $this->context->periods],
			'days' => ['name' => 'date', 'items' => $this->context->dates],
			'rooms' => ['name' => 'room', 'items' => $this->context->rooms],
		];

		return array_key_exists($this->context->columns, $cols)
			? $cols[$this->context->columns]
			: [];
	}


	public function get_rows()
	{
		$rows = [
			'periods' => ['name' => 'period', 'items' => $this->context->periods],
			'days' => ['name' => 'date', 'items' => $this->context->dates],
			'rooms' => ['name' => 'room', 'items' => $this->context->rooms],
		];

		return array_key_exists($this->context->rows, $rows)
			? $rows[$this->context->rows]
			: [];
	}


	/**
	 * Render the main bookings table.
	 *
	 */
	public function render()
	{
		$header_row = $this->render_header_row();
		$content_rows = $this->render_content_rows();

		// Run the content rows through the parser to populate the slots
		$content_rows = $this->parse_slots($content_rows);

		$tbody = '<tbody>' . $header_row . $content_rows . '</tbody>';

		$classes = 'bookings-grid';
		if (setting('grid_highlight') == 1) {
			$classes .= ' has-highlight';
		}

		$hs = <<<EOH
init
	on toggle_ms
		toggle [@data-multi=true]
		call updateUi()
	end
	def updateUi
		if my [@data-multi]
			set showElts to <.multi-select-content[data-multi='true']/>
			set hideElts to <.multi-select-content[data-multi='false']/>
			add .highlight to .multi-select-controller
		else
			set showElts to <.multi-select-content[data-multi='false']/>
			set hideElts to <.multi-select-content[data-multi='true']/>
			remove .highlight from .multi-select-controller
		end
		show showElts with display
		hide hideElts with display
	end
EOH;

		$table_open = '<table border="0" bordercolor="#ffffff" cellpadding="2" cellspacing="2" class="%s" data-script="%s">';
		$table_open = sprintf($table_open, $classes, trim(html_escape($hs)));

		$table_close = '</table>';

		return $table_open . $tbody . $table_close;
	}


	private function render_header_row()
	{
		$day_names = Calendar::get_day_names();

		$cells = [];

		// Top corner
		//
		$cells[] = "<td>&nbsp;</td>";

		// Get columns
		//

		$column_config = $this->get_columns();
		$name = $column_config['name'];

		if (is_array($column_config['items'])) {

			foreach ($column_config['items'] as $col_item) {

				$data = [
					$name => $col_item,
					'width' => $this->col_width,
					'day_names' => $day_names,
					'today' => $this->context->today,
				];

				$view_name = sprintf('bookings_grid/table/col_%s', $name);

				$cells[] = $this->CI->load->view($view_name, $data, TRUE);

			}

		}


		$classes = [
			'bookings-grid-row',
			'bookings-grid-header-row',
			'bookings-grid-header-row-' . $this->context->columns,
		];

		$classes_str = implode(' ', $classes);

		return "<tr class='{$classes_str}'>" . implode("\n", $cells) . "</tr>";
	}


	private function render_content_rows()
	{
		$content_rows = [];

		// Get rows
		//

		$row_config = $this->get_rows();
		$name = $row_config['name'];

		if (is_array($row_config['items'])) {
			foreach ($row_config['items'] as $row_item) {
				$content_rows[] = $this->render_content_row([
					'name' => $name,
					$name => $row_item,
				]);
			}
		}

		return implode("\n", $content_rows);
	}


	private function render_content_row($params)
	{
		$cells = [];

		$day_names = Calendar::get_day_names();

		// Render header cell
		//
		$header_view_data = $params;
		$header_view_data['day_names'] = $day_names;
		$header_view_data['today'] = $this->context->today;
		$header_view_name = sprintf('bookings_grid/table/row_%s', $params['name']);
		$cells[] = $this->CI->load->view($header_view_name, $header_view_data, TRUE);

		// Render slots
		//
		$column_config = $this->get_columns();
		$col_name = $column_config['name'];

		if (is_array($column_config['items'])) {

			foreach ($column_config['items'] as $col_item) {

				$cell_data = [
					'row' => $params,
					'column' => ['name' => $col_name, $col_name => $col_item ],
				];

				$cells[] = $this->render_cell($cell_data);

			}
		}


		$classes = [
			'bookings-grid-row',
			'bookings-grid-content-row',
			'bookings-grid-content-row-' . $this->context->rows,
		];

		$classes_str = implode(' ', $classes);

		return "<tr class='{$classes_str}'>" . implode("\n", $cells) . "</tr>";
	}


	private function render_cell($params)
	{
		$data = [];

		switch ($this->context->display_type) {
			case 'day': $data['date'] = $this->context->date_info; break;
			case 'room': $data['room'] = $this->context->room; break;
		}

		$row_key = $params['row']['name'];
		$column_key = $params['column']['name'];

		$data[$row_key] = $params['row'][$row_key];
		$data[$column_key] = $params['column'][$column_key];

		extract($data);

		$slot_key = Slot::generate_key($date->date, $period->period_id, $room->room_id);

		return '{' . $slot_key . '}';
	}


	private function parse_slots($template)
	{
		$vars = [];

		$classes = [
			'bookings-grid-slot',
		];

		$highlight = null;
		$params = $this->context->get_query_params();
		if (isset($params['highlight'])) {
			$highlight = (int) $params['highlight'];
		}

		foreach ($this->context->slots as $slot) {

			$slot_classes = [
				sprintf('booking-status-%s', $slot->status),
				sprintf('booking-status-%s-%s', $slot->status, $slot->reason),
			];

			if ($slot->booking && $slot->booking->booking_id == $highlight) {
				$slot_classes[] = 'highlight';
			}

			$class_str = implode(' ', array_merge($classes, $slot_classes));

			$view_name = sprintf('bookings_grid/table/slot/%s_%s', $slot->status, $slot->reason);
			$view_name = rtrim($view_name, '_');

			$view_data = [
				'class' => $class_str,
				'slot' => $slot,
				'context' => $slot->context,
				'extended' => FALSE,
			];

			$view_data = array_merge($view_data, $slot->view_data);

			$view = $this->CI->load->view($view_name, $view_data, TRUE);

			$vars[$slot->key] = $view;
		}

		$this->CI->load->library('parser');
		$html = $this->CI->parser->parse_string($template, $vars, TRUE);

		return $html;
	}


}
