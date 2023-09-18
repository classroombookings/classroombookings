<?php

use app\components\TabPane;

// Date format of bookings
$date_format = setting('date_format_long', 'crbs');

// For period display
$time_fmt = setting('time_format_period');

$tabs = new TabPane();
$tabs->set_list_view('bookings/create/multi/preview/tab_item');
$tabs->set_detail_view('bookings/create/multi/preview/tab_detail');

foreach ($multibooking->slots as $key => $slot) {

	$tabs->add([
		'multibooking' => $multibooking,
		'key' => $key,
		'slot' => $slot,
	]);

}

// echo json_encode($multibooking, JSON_PRETTY_PRINT);


// Form
//

$attrs = [
	'id' => 'bookings_create_multi_recurring_preview',
	'class' => 'cssform',
	'up-accept-location' => 'bookings',
	'up-layer' => 'any',
	'up-target' => '.bookings-create',
];

$hidden = [
	'mb_id' => $mb_id,
	'step' => 'recurring_preview',
];

if ($message) {
	echo msgbox('error', $message);
}

echo validation_errors();

echo form_open(current_url(), $attrs, $hidden);

echo "<div style='margin-bottom:16px'>Use this page to check all the booking instances that will be created for each series.</div>";

echo $tabs->render();

// Footer (submit or canceL)
//

$cancel = anchor($return_uri, 'Cancel', ['up-dismiss' => '']);

$submit = form_button([
	'type' => 'submit',
	'content' => 'Create recurring bookings',
]);

echo "<div style='border-top:0px;'>{$submit} &nbsp; {$cancel}</div>";

echo form_close();
