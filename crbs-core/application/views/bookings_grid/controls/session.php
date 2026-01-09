<?php

$attrs = [
	'method' => 'post',
	'id' => 'bookings_controls_session',
];

$hidden = [
	'params' => http_build_query($query_params)
];

echo form_open($form_action, $attrs, $hidden);

$sess_lang = lang('session.session');
echo "<label for='session_id'>{$sess_lang}: </label>";

echo form_dropdown([
	'name' => 'session_id',
	'id' => 'session_id',
	'options' => html_escape($session_options),
	'selected' => $selected_session_id,
	'data-script' => 'on change requestSubmit() on the closest <form/>',
]);

echo form_close();

