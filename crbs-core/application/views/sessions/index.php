<?php
$messages = $this->session->flashdata('saved');
echo "<div class='messages'>{$messages}</div>";


echo iconbar([
	['sessions/add', 'Add Session', 'add.png'],
]);


$sort_cols = ["Name", "Start date", "End date", "Current?", "Selectable?"];

echo "<h3>Current and future sessions</h3>";
$this->load->view('sessions/table', ['items' => $active, 'id' => 'sessions_active', 'sort_cols' => $sort_cols]);

if ( ! empty($past)) {
	echo "<br><br><h3>Past sessions</h3>";
	$this->load->view('sessions/table', ['items' => $past, 'id' => 'sessions_past', 'sort_cols' => $sort_cols]);
}
