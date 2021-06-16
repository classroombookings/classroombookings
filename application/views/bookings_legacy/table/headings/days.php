<td align="center" width="<?php echo $width ?>">
	<strong><?php echo html_escape($name) ?></strong>
	<?php
	$date_fmt = setting('date_format_weekday');
	if (strlen($date_fmt)) {
		echo "<br>";
		echo sprintf("<span style='font-size:90%%'>%s</span>", date($date_fmt, strtotime($date)));
	}
	?>
</td>
