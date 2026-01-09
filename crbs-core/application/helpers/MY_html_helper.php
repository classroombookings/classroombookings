<?php

defined('BASEPATH') OR exit('No direct script access allowed');


function field($validation, $database = NULL, $last = ''){
	$value = $validation ?? $database ?? $last;
	return $value;
}


function buttonlist($items = array()) {
	$out = '';
	$links = [];

	foreach ($items as $item) {
		if (is_null($item)) continue;
		$is_active = isset($item['active']) && $item['active'] == true;
		$url = $item['url'];
		$title = $item['title'];
		$attrs = $item['attrs'] ?? null;
		if (is_array($attrs)) {
			$attrs = _stringify_attributes($attrs);
		}
		$class = ($is_active) ? 'is-active' : '';
		$link = anchor($url, $title, "class='{$class}' {$attrs}");
		$link = str_replace(site_url('#'), '#', $link);
		$links[] = $link;
	}

	$items_html = implode("", $links);

	$out = "<nav class='buttonlist'>{$items_html}</nav>";
	return $out;
}



function iconbar($items = array(), $active = false) {

	$html = "<div class='iconbar'>";
	$i = 1;
	$max = count($items);

	foreach ($items as $item) {

		if (is_null($item)) {
			$max--;
			continue;
		}

		$attrs = '';

		if (isset($item['link'])) {
			extract($item);
		} else {
			[$link, $name, $icon] = $item;
		}

		$escape = true;
		if (isset($item['escape']) && $item['escape'] === false) {
			$escape = false;
		}

		$name = $escape ? html_escape($name) : $name;

		if (is_array($attrs)) {
			$attrs = _stringify_attributes($attrs);
		}

		if (isset($title)) {
			$title = html_escape($title);
			$attrs .= " title='$title'";
		}

		$class = ($link == $active)
			? 'active'
			: '';

		$img = img([
			'src' => asset_url("assets/images/ui/{$icon}"),
			'alt' => html_escape(strip_tags($name)),
			'align' => 'top',
			'hspace' => 0,
			'border' => 0,
		]);

		$count = '';
		if (isset($item['count'])) {
			$count_val = html_escape($item['count']);
			$count = "<span class='count'>({$count_val})</span>";
		}

		$meta = '';
		if (isset($item['meta'])) {
			$meta = "<span class='count'>{$item['meta']}</span>";
		}

		$label = anchor($link, "{$img} {$name}{$count}{$meta}", "class='{$class}' {$attrs}");
		$label = str_replace(site_url('#'), '#', $label);

		$html .= $label;

		if ($i < $max) {
			$html .= img(asset_url("assets/images/sep.gif"), FALSE, "alt='|' class='iconbar-sep' align='top' hspace='0' border='0' style='margin:0px 3px;'");
		}

		$i++;
	}

	$html .= "</div>";

	return $html;
}


function tab_index($reset = false)
{
	static $_tab_index;

	if (empty($_tab_index) || $reset === true) {
		$_tab_index = 0;
	} else {
		$_tab_index++;
	}

	return $_tab_index;
}




function msgbox($type = 'error', $content = '', $escape = TRUE)
{
	if ($escape)
	{
		$content = nl2br(html_escape($content));
	}

	$html = "<div class='msgbox {$type}'>{$content}</div>";
	return $html;
}


function sort_link($base_uri, $param, $label)
{
	$CI =& get_instance();
	$get_data = $CI->input->get();
	$get_sort = $CI->input->get('sort') ?? '';
	$get_sort_field = ltrim($get_sort, '-');

	$get_data['sort'] = $param;

	if ($get_sort == $param) {
		$get_data['sort'] = "-{$param}";
	}

	$suffix = '';
	if ($get_sort == $param) {
		$suffix = '<span class="sort-arr">&#11205;</span>';
	} elseif ($get_sort == "-{$param}") {
		$suffix = '<span class="sort-arr">&#11206;</span>';
	}

	$query = http_build_query($get_data);
	$uri = $base_uri . '?' . $query;

	return anchor(site_url($uri), $label . $suffix, ['class' => 'sort-link']);
}


/**
 * script_src
 *
 * Render a <script> tag
 *
 * @access	public
 * @param	type	name
 * @return	type
 */
if (! function_exists('script_src')) {
	function script_src($src = '', $attributes = [])
	{
		$out = '<script';
		$attributes['src'] = $src;
		$out .= _stringify_attributes($attributes);
		$out .= "></script>\n";
		return $out;
	}
}


function date_picker_img($input_name)
{
	$hs = <<<EOS
on click call displayDatePicker('{$input_name}', false)
EOS;

	$img = img([
		'src' => asset_url('assets/images/ui/cal_day.png'),
		'style' => 'cursor:pointer',
		'align' => 'top',
		'width' => 16,
		'height' => 16,
		'title' => lang('app.choose_date'),
		'script' => $hs,
	]);

	return $img;
}



function render_list_builder(array $params = [])
{
	$CI =& get_instance();
	$available_options = [];
	$selected_options = [];
	$value = $params['value'] ?? [];

	foreach (($params['options'] ?? []) as $id => $label) {
		// if (array_key_exists($id, $))
		if (in_array($id, $params['value'])) {
			$selected_options[$id] = $label;
		} else {
			$available_options[$id] = $label;
		}
	}

	$params['available_options'] = $available_options;
	$params['selected_options'] = $selected_options;

	return $CI->load->view('partials/list_builder', $params, true);
}

/*
function icon($name, $attributes = array())
{
	$CI =& get_instance();
	return $CI->feather->get($name, $attributes, FALSE);
}
*/
