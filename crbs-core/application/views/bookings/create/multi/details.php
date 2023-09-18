<?php

// Form
//
$attrs = [
	'id' => 'bookings_create_multi',
	'class' => 'cssform',
	'up-accept-location' => 'bookings',
	'up-layer' => 'any',
	'up-target' => '.bookings-create',
];

$hidden = [
	'mb_id' => $mb_id,
	'step' => 'details',
];

if ($message) {
	echo msgbox('error', $message);
}

echo validation_errors();

echo "<!-- <pre>" . json_encode($multibooking, JSON_PRETTY_PRINT) . "</pre> -->";

echo form_open(current_url(), $attrs, $hidden);

echo "<fieldset class='cssform-stacked' style='border:0;padding:0;margin-bottom:0;'>";

if ($allow_recurring) {

	// Allow selection of single/recurring
	//

	// Type
	//
	$week = week_dot($multibooking->week, 'sm') . ' ' . $multibooking->week->name;

	$options = [
		['value' => 'single', 'label' => 'Single bookings on the selected dates'],
		['value' => 'recurring', 'label' => sprintf('Recurring bookings every week <small>(%s)</small>', $week)],
	];
	$field = 'type';
	$label = form_label('Type');
	$value = set_value($field);

	$inputs_html = '';
	foreach ($options as $opt) {
		$id = sprintf('type_%s', $opt['value']);
		$input = form_radio([
			'name' => $field,
			'id' => $id,
			'value' => $opt['value'],
			'checked' => ($value == $opt['value']),
			'up-switch' => '.booking-type-content',
		]);
		$radio_label = "<label for='{$id}' class='ni'>{$input}{$opt['label']}</label>";
		$inputs_html .= $radio_label;
	}

	echo sprintf("<p class='input-group'>%s%s</p>%s", $label, $inputs_html, form_error($field));

	echo "</fieldset>";


	// Recurring info (different views depending on single or recurring)
	//

	echo "<div class='booking-type-content' up-show-for='single'>";
	$this->load->view('bookings/create/multi/details_single');
	echo "</div>";

	echo "<div class='booking-type-content' up-show-for='recurring'>";
	$this->load->view('bookings/create/multi/details_recurring');
	echo "</div>";

} else {

	echo form_hidden('type', 'single');
	$this->load->view('bookings/create/multi/details_single');

}


// Footer (submit or canceL)
//

$cancel = anchor($return_uri, 'Cancel', ['up-dismiss' => '']);

$submit_single = form_button([
	'type' => 'submit',
	'content' => 'Create selected bookings',
]);

$submit_recurring = form_button([
	'type' => 'submit',
	'content' => 'Next &rarr;',
]);

if ($allow_recurring) {
	echo "<div class='booking-type-content' style='border-top:0px;' up-show-for='single'>{$submit_single} &nbsp; {$cancel}</div>";
	echo "<div class='booking-type-content' style='border-top:0px;' up-show-for='recurring'>{$submit_recurring} &nbsp; {$cancel}</div>";
} else {
	echo "<div class='booking-type-content' style='border-top:0px;'>{$submit_single} &nbsp; {$cancel}</div>";
}

echo form_close();
