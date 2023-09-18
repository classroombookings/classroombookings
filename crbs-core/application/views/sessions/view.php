<?php

$messages = $this->session->flashdata('saved');
echo "<div class='messages'>{$messages}</div>";

$css = $calendar->get_css();
echo "<style type='text/css'>{$css}</style>";

$dateFormat = setting('date_format_long', 'crbs');
$start = $session->date_start ? $session->date_start->format($dateFormat) : '';
$end = $session->date_end ? $session->date_end->format($dateFormat) : '';
echo "<p><strong>Start date: </strong>{$start}</p>";
echo "<p><strong>End date:</strong> {$end}</p>";

if ( ! empty($weeks)) {
	$this->load->view('sessions/view_apply_week', [
		'weeks' => $weeks,
		'session' => $session,
	]);
}

echo "<br><p>Click on the dates in each calendar to toggle the Timetable Week for that week.</p><br>";

echo form_open(current_url(), [], ['session_id' => $session->session_id]);

echo $calendar->generate_full_session(['column_class' => 'b-50']);

$this->load->view('partials/submit', array(
	'submit' => array('Save', tab_index()),
));

echo form_close();
