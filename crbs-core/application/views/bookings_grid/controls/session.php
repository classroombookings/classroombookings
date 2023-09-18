<?php

$attrs = [
	'method' => 'post',
	'id' => 'bookings_controls_session',
];

$hidden = [
	'params' => http_build_query($query_params)
];

echo form_open($form_action, $attrs, $hidden);

echo "<label for='session_id'>Session: </label>";

echo form_dropdown([
	'name' => 'session_id',
	'id' => 'session_id',
	'options' => $session_options,
	'selected' => $selected_session_id,
]);

echo form_close();

