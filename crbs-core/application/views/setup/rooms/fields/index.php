<?php

echo $this->session->flashdata('saved');

echo iconbar([
	['setup/rooms/fields/add', lang('custom_field.add.action'), 'add.png'],
]);


$this->table->set_template([
	'table_open' => '<table
		class="border-table"
		style="line-height:1.3;margin-top:16px;margin-bottom:16px"
		width="100%"
		cellspacing="2"
		border="0"
		>',
	'heading_row_start' => '<tr class="heading">',
]);

$this->table->set_heading([
	['data' => 'Name', 'width' => '25%'],
	['data' => 'Type', 'width' => '15%'],
	['data' => 'Options', 'width' => '50%'],
	['data' => 'Actions', 'width' => '10%'],
]);

if (is_array($fields) && ! empty($fields)) {

	foreach ($fields as $field) {

		$name_html = anchor('setup/rooms/fields/edit/'.$field->field_id, html_escape($field->name));
		$type_html = $options_list[$field->type];

		$options_html = '';
		if (isset($field->options) && is_array($field->options)) {
			$values = array();
			foreach ($field->options as $option) {
				$label = trim((string) $option->value);
				if (empty($label)) continue;
				$values[] = html_escape($label);
			}
			$options_html = implode(", ", $values);
		}

		$actions = [];
		// $actions['edit'] = 'rooms/edit_field/'.$field->field_id;
		$actions['delete'] = 'setup/rooms/fields/delete/'.$field->field_id;
		$actions_html = $this->load->view('partials/editdelete', $actions, true);

		$this->table->add_row([
			$name_html,
			$type_html,
			$options_html,
			$actions_html,
		]);
	}

	echo $this->table->generate();

} else {

	echo msgbox('info', lang('custom_field.no_items'));

}
