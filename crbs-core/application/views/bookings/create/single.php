<?php

// Generate iconbar menu for choosing single/recurring booking type
//

$params = $this->input->get();
if ($this->input->post('original_params')) {
	$p = $this->input->post('original_params');
	parse_str((string) $p, $vars);
	$params = $vars;
}
$params['step'] = 'details';

$items = [];
if ($can_book_single) {
	$items[] = [
		'link' => current_url().'?'.http_build_query(array_merge($params, ['booking_type' => 'single'])),
		'name' => lang('booking.type_single'),
		'icon' => 'cal_day.png',
		'attrs' => ['up-target' => '.bookings-create'],
	];
}
if ($can_book_recur) {
	$items[] = [
			'link' => current_url().'?'.http_build_query(array_merge($params, ['booking_type' => 'recurring'])),
			'name' => lang('booking.type_recurring'),
			'icon' => 'arrow_refresh.png',
			'attrs' => ['up-target' => '.bookings-create'],
	];
}

if (count($items) > 1) {
	echo iconbar($items, current_url().'?'.http_build_query(array_merge($params, ['booking_type' => $booking_type])));
}


echo "<br>";

//

if ($message) {
	echo msgbox('error large', $message);
}

//

if ($permit_booking) {

	// Ecnlose subview in form
	//

	$attrs = [
		'id' => 'bookings_create_single',
		'class' => 'cssform',
		'up-accept-location' => 'bookings',
		'up-layer' => 'any',
		'up-target' => '.bookings-create',
	];

	echo form_open(current_url(), $attrs, ['original_params' => http_build_query($this->input->get())]);
	$this->load->view($subview);

	echo form_close();

} else {

	$this->load->view($subview);

}
