<?php

defined('BASEPATH') OR exit('No direct script access allowed');


function field($validation, $database = NULL, $last = ''){
	$value = (isset($validation)) ? $validation : ( (isset($database)) ? $database : $last);
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
		$attrs = isset($item['attrs']) ? $item['attrs'] : null;
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
			list($link, $name, $icon) = $item;
		}

		if (is_array($attrs)) {
			$attrs = _stringify_attributes($attrs);
		}

		$class = ($link == $active)
			? 'active'
			: '';

		$img = img("assets/images/ui/{$icon}", FALSE, "alt='{$name}' align='top' hspace='0' border='0'");

		$label = anchor($link, "{$img} {$name}", "class='{$class}' {$attrs}");
		$label = str_replace(site_url('#'), '#', $label);

		$html .= $label;

		if ($i < $max) {
			$html .= img("assets/images/sep.gif", FALSE, "alt='|' align='top' hspace='0' border='0' style='margin:0px 3px;'");
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
		$content = html_escape($content);
	}

	$html = "<p class='msgbox {$type}'>{$content}</p>";
	return $html;
}


/*
function icon($name, $attributes = array())
{
	$CI =& get_instance();
	return $CI->feather->get($name, $attributes, FALSE);
}
*/
