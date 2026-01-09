<?php

$messages = $this->session->flashdata('saved');
echo "<div class='messages'>{$messages}</div>";

$css = $calendar->get_css();
echo "<style type='text/css'>{$css}</style>";

$start = $session->date_start ? date_output_long($session->date_start) : '';
$end = $session->date_end ? date_output_long($session->date_end) : '';
echo "<p><strong>Start date: </strong>{$start}</p>";
echo "<p><strong>End date:</strong> {$end}</p>";

if ( ! empty($weeks)) {
	$this->load->view('sessions/view_apply_week', [
		'weeks' => $weeks,
		'session' => $session,
	]);
}

echo "<br><p>" . lang('session.weeks.intro') . "</p><br>";

echo form_open(current_url(), [], ['session_id' => $session->session_id]);

echo $calendar->generate_full_session(['column_class' => 'b-50']);

$this->load->view('partials/submit', array(
	'submit' => array('Save', tab_index()),
));

echo form_close();
