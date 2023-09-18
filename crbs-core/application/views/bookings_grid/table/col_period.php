<th class="bookings-grid-header-cell bookings-grid-header-cell-period" width="<?= $width ?>">
	<strong><?php echo html_escape($period->name) ?></strong>
	<br />
	<span style="font-size: 90%">
		<?php
		$time_fmt = setting('time_format_period');
		if (!empty($time_fmt)) {
			$start = date($time_fmt, strtotime($period->time_start));
			$end = date($time_fmt, strtotime($period->time_end));
			echo sprintf('%s - %s', $start, $end);
		}
		?>
	</span>
</th>
