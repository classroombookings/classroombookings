<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
 * Colour picker input
 *
 */
function form_colour_picker($options = [])
{
	$defaults = [
		'name' => '',
		'value' => '',
		'attrs' => '',
		'custom' => TRUE,		// allow custom values?
		'random' => TRUE,	// if value empty, pick random colour
		'palette' => 'material',
		'item_template' => "<label class='colour-picker-item' for='{id}'>{input}</label>",
		'input_template' => "<input type='radio' name='{name}' value='{value}' id='{id}' {checked}><div class='colour-picker-icon' style='background-color:{colour};'><span>&#10004;</span></div>",
		'template' => "<div class='colour-picker' {attrs}>{items}</div>",
	];

	$data = array_merge($defaults, $options);

	// Format value so we can match it reliably
	$data['value'] = str_replace('#', '', $data['value']);
	$data['value'] = strtolower($data['value']);

	$palettes = [];

	$palettes['clrs'] = [
		'001f3f',
		'0074d9',
		'7fdbff',
		'39cccc',
		'3d9970',
		'2ecc40',
		'01ff70',
		'ffdc00',
		'ff851b',
		'ff4136',
		'f012be',
		'b10dc9',
		'85144b',
		'aaaaaa',
	];

	$palettes['material'] = [
		'e51c23',
		'e91e63',
		'9c27b0',
		'673ab7',
		'3f51b5',
		'5677fc',
		'03a9f4',
		'00bcd4',
		'009688',
		'259b24',
		'8bc34a',
		'cddc39',
		'ffeb3b',
		'ffc107',
		'ff9800',
		'ff5722',
		'795548',
		'9e9e9e',
		'607d8b',
	];

	if (is_array($data['palette'])) {
		$colours = $data['palette'];
	} else {
		$colours = $palettes[$data['palette']];
	}

	if ( ! strlen($data['value'])) {
		$data['value'] = $colours[ array_rand($colours) ];
	}

	$CI =& get_instance();
	$CI->load->library('parser');

	// Build items
	$items = [];
	foreach ($colours as $colour) {

		$hex_colour = "#{$colour}";
		$checked = $colour == $data['value'] ? 'checked="checked"' : '';

		$vars = [
			'id' => "{$data['name']}_colour_{$colour}",
			'name' => $data['name'],
			'value' => $colour,
			'colour' => $hex_colour,
			'checked' => $checked,
		];

		$vars['input'] = $CI->parser->parse_string($data['input_template'], $vars, TRUE);

		$items[] = $CI->parser->parse_string($data['item_template'], $vars, TRUE) . "\n";
	}

	// Build final output

	$items_html = implode("\n", $items);

	$vars = [
		'attrs' => $data['attrs'],
		'items' => $items_html,
	];

	return $CI->parser->parse_string($data['template'], $vars, TRUE);
}
