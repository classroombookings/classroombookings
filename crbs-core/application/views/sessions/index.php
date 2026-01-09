<?php
$messages = $this->session->flashdata('saved');
echo "<div class='messages'>{$messages}</div>";


echo iconbar([
	['sessions/add', lang('session.add.action'), 'add.png'],
]);

echo "<h3>" . lang('session.list.current_and_future') . "</h3>";
$this->load->view('sessions/table', ['items' => $active, 'id' => 'sessions_active']);

if ( ! empty($past)) {
	echo "<br><br><h3>" . lang('session.list.past') . "</h3>";
	$this->load->view('sessions/table', ['items' => $past, 'id' => 'sessions_past']);
}
