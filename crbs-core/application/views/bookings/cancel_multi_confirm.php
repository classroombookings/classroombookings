<?php

// Generate table of bookings
//
$this->table->set_template([
	'table_open' => '<table class="zebra-table form-table multibooking-table" style="line-height:1.3;margin-bottom:16px" width="100%" cellpadding="8" cellspacing="0" border="0">',
]);


// Get columns for table
//

$cols = [];

$cols[] = ['data' => '', 'width' => '5%'];

$cols[] = ['data' => lang('app.date'), 'width' => '25%'];
$cols[] = ['data' => lang('period.period'), 'width' => '10%'];
$cols[] = ['data' => lang('room.room'), 'width' => '15%'];
$cols[] = ['data' => lang('department.department')];
$cols[] = ['data' => lang('user.user')];
$cols[] = ['data' => lang('booking.notes'), 'width' => '25%'];

$this->table->set_heading($cols);

// Generate rows
//

$all_ids = array_column($bookings, 'booking_id');

foreach ($bookings as $booking) {

	if ( ! booking_cancelable($booking)) continue;

	// 'Cancel' checkbox col
	//
	$cancel_field = sprintf('bookings[%d]', $booking->booking_id);
	$cancel_check = form_checkbox([
		'id' => $cancel_field,
		'name' => $cancel_field,
		'value' => 'cancel',
		'checked' => set_value($cancel_field, 'cancel') == 'cancel',
	]);
	$check_col = $cancel_check;

	// Date column
	//
	$date = date_output_long($booking->date);
	$date_col = form_label($date, $cancel_field, ['class' => 'ni']);

	// Period col
	//
	$period_col = form_label(html_escape($booking->period->name), $cancel_field, ['class' => 'ni']);

	// Room col
	//
	$room_col = html_escape($booking->room->name);

	// Department column
	//
	$department_col = html_escape($booking->department->name ?? '');

	// User column
	//
	$user_col = '';
	if (booking_user_viewable($booking)) {
		$user = $booking->user ?? null;
		if ( ! is_null($user)) {
			$user = !empty($user->displayname)
				? $user->displayname
				: $user->username;
			$user_col = html_escape($user);
		}
	}

	$notes_col = '';
	if (booking_notes_viewable($booking)) {
		// Notes
		//
		$notes_col = html_escape($booking->notes ?? '');
	}


	// Add row
	//
	$row = [];
	$row[] = $check_col;
	$row[] = $date_col;
	$row[] = $period_col;
	$row[] = $room_col;
	$row[] = $department_col;
	$row[] = $user_col;
	$row[] = $notes_col;

	$this->table->add_row($row);

}


// Main output
//


// Form
//

$attrs = [
	'id' => 'bookings_cancel_multi_confirm',
	'class' => 'cssform',
	'up-accept-location' => 'bookings',
	'up-layer' => 'any',
	'up-target' => '.bookings-cancel-multi',
];
$hidden = ['step' => 'confirm'];
echo form_open(current_url(), $attrs, $hidden);

// Table
//
echo "<fieldset style='border:0; padding:0;'>";
echo $this->table->generate();
echo "</fieldset>";

// Footer (submit or canceL)
//
$cancel = anchor($return_uri ?? '', lang('app.action.cancel'), ['up-dismiss' => '']);
$submit_single = form_button([
	'type' => 'submit',
	'content' => lang('booking.cancel_multi.action'),
	'style' => 'border: 1px solid red',
]);
echo "<div style='border-top:0px;'>{$submit_single} &nbsp; {$cancel}</div>";
echo form_close();

