<p><?= lang('period.hint') ?></p>

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
