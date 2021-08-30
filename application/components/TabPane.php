<?php

namespace app\components;

defined('BASEPATH') OR exit('No direct script access allowed');


class TabPane
{

	public $id;
	public $tabs = [];

	public $list_view = '';
	public $detail_view = '';


	public function __construct($id = null)
	{
		$this->CI =& get_instance();
		$this->id = $id ? $id : uniqid();
	}


	public function set_list_view($view_name)
	{
		$this->list_view = $view_name;
		return $this;
	}


	public function set_detail_view($view_name)
	{
		$this->detail_view = $view_name;
	}


	public function add($data)
	{
		$this->tabs[] = $data;
		return $this;
	}


	public function set_tabs($data)
	{
		$this->tabs = $data;
		return $this;
	}


	public function render()
	{
		$list_html = [];
		$detail_html = [];

		foreach ($this->tabs as $idx => $tab_data) {

			$list_html[] = $this->render_list_item($idx, $tab_data);
			$detail_html[] = $this->render_detail_item($idx, $tab_data);

		}

		$vars = [
			'list_html' => implode("\n", $list_html),
			'detail_html' => implode("\n", $detail_html),
		];

		return $this->CI->load->view('tabpane/tabpane', $vars, TRUE);
	}


	public function render_list_item($idx, $view_data)
	{
		$html = $this->CI->load->view($this->list_view, $view_data, TRUE);

		$input_id = $this->id . '_tab' . $idx;

		$input = form_radio([
			'name' => $this->id,
			'id' => $input_id,
			'value' => $idx,
			'up-switch' => '.tab-group-' . $this->id,
			'checked' => ($idx === 0),
		]);

		return "<label
			class='tab-pane-list-item'
			for='{$input_id}'
		>{$input}<div class='tab-pane-list-item-body'>{$html}</div></label>";
	}


	public function render_detail_item($idx, $view_data)
	{
		$html = $this->CI->load->view($this->detail_view, $view_data, TRUE);

		$ref = $this->id . '_tab' . $idx;

		return "<div
			class='tab-pane-detail-item tab-group-{$this->id}'
			up-show-for='{$idx}'
		>{$html}</div>";
	}

}
