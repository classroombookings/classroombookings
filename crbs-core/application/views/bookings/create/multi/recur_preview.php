<?php

use app\components\TabPane;

$tabs = new TabPane();
$tabs->set_list_view('bookings/create/multi/recur/preview/tab_item');
$tabs->set_detail_view('bookings/create/multi/recur/preview/tab_detail');

foreach ($multibooking->slots as $key => $slot) {

	if ($slot->ignore) continue;

	$tabs->add([
		'multibooking' => $multibooking,
		'key' => $key,
		'slot' => $slot,
	]);

}

if ($conflict_count > 0) {
	$line = lang('booking.conflict.multiple');
	$msg = sprintf($line, $conflict_count);
	echo msgbox('notice large', $msg);
}

echo $tabs->render();

// Actions
//
$submit = form_button([
	'type' => 'submit',
	'name' => 'action',
	'value' => 'create',
	'content' => '&check; ' . lang('booking.add.multi.recurring.action'),
]);

$cancel = anchor($return_uri, lang('app.action.cancel'), ['up-dismiss' => '']);

echo "<div class='booking-type-content' style='border-top:0px;'>{$submit} &nbsp; {$cancel}</div>";

