<?php

/**
 * Return markup for a button of type submit/button/link
 */
function form_button($data = array())
{
	$type = element('type', $data, 'submit');
	$class = element('class', $data, '');
	$text = element('text', $data, '');
	$tab_index = (isset($data['tab_index'])) ? 'tabindex="' . $data['tab_index'] . '"' : '';
	$url = site_url(element('url', $data, ''));
	$id = (isset($data['id'])) ? 'id="' . $data['id'] . '"' : '';
	
	$data_attr_str = '';
	
	// Data attrs
	if (array_key_exists('data', $data))
	{
		$data_attrs = array();
		foreach ($data['data'] as $attr => $value)
		{
			$data_attrs[] = 'data-' . $attr . '="' . $value . '"';
		}
		$data_attr_str = implode(' ', $data_attrs);
	}
	
	$find = array('{class}', '{text}', '{tab_index}', '{url}', '{id}', '{data}');
	$replace = array($class, $text, $tab_index, $url, $id, $data_attr_str);
	
	$html = '';

	switch($type)
	{
		case 'submit':
			$format = '<input type="submit" name="submit" class="button {class}" value="{text}" {tab_index} {id} {data}>';
			$html = str_replace($find, $replace, $format);
		break;
		
		case 'button':
			$format = '<button type="button" name="{text}" value="{text}" class="button {class}" {tab_index} {id} {data}>{text}</button>';
			$html = str_replace($find, $replace, $format);
		break;

		case 'link':
			$format = '<a href="{url}" class="button {class}" {tab_index} {id} {data}>{text}</a>';
			$html = str_replace($find, $replace, $format);
		break;
	}
	
	return $html;
}

