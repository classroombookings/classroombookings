<p>All times should be entered in the <span>24 hour</span> format, between 00:00 and 23:59. Periods times should not overlap.</p>

<div>
	<?php
	if ( ! empty($periods)) {
		foreach ($periods as $period) {
			$this->load->view('periods/item_view', ['period' => $period]);
		}
	}
	echo $this->load->view('periods/item_add_edit', ['period' => NULL, 'focus' => FALSE]);
	?>
</div>

<style>
.box.box-period .block {
	padding: 12px;
}
</style>
