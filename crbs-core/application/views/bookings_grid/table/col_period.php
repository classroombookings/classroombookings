<th class="bookings-grid-header-cell bookings-grid-header-cell-period" width="<?= $width ?>">
	<strong><?php echo html_escape($period->name) ?></strong>
	<br />
	<span>
		<?php
		$time_start_fmt = date_output_time($period->time_start);
		$time_end_fmt = date_output_time($period->time_end);
		echo sprintf('%s - %s', $time_start_fmt, $time_end_fmt);
		?>
	</span>
</th>
