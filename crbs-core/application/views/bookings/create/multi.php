<?php

// Generate iconbar menu for choosing single/recurring booking type
//

$params = $this->input->get();
if ($this->input->post('original_params')) {
	$p = $this->input->post('original_params');
	parse_str((string) $p, $vars);
	$params = $vars;
}
$params['mb_id'] = $mb_id;
$params['step'] = 'details';

$items = [];
$items[] = [
	'link' => current_url().'?'.http_build_query(array_merge($params, ['booking_type' => 'single'])),
	'name' => lang('booking.type_single'),
	'icon' => 'cal_day.png',
	'attrs' => ['up-target' => '.bookings-create'],
];

$week_label = lang('booking.type_recurring');
if (isset($multibooking)) {
	$week_label = sprintf('%s: %s %s %s',
		lang('booking.type_recurring'),
		strtolower(lang('app.every')),
		week_dot($multibooking->week, 'sm'),
		$multibooking->week->name
	);
}
$items[] = [
	'link' => current_url().'?'.http_build_query(array_merge($params, ['booking_type' => 'recurring'])),
	'name' => $week_label,
	'escape' => false,
	'icon' => 'arrow_refresh.png',
	'attrs' => ['up-target' => '.bookings-create'],
];

echo iconbar($items, current_url().'?'.http_build_query(array_merge($params, ['booking_type' => $booking_type])));


echo "<br>";

//

if ($message) {
	echo msgbox('error', $message);
}

echo validation_errors();

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
	'step' => $step,
];

echo form_open(current_url(), $attrs, $hidden);

if (isset($subview) && ! empty($subview)) {
	$this->load->view($subview);
}

echo form_close();
