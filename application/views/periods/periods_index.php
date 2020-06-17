<?php

echo $this->session->flashdata('saved');

$iconbar = iconbar(array(
	array('periods/add', 'Add Period', 'add.png'),
));

echo $iconbar;

$sort_cols = ["None", "Name", "TimeStart", "TimeEnd", "Duration", "Days", "None"];

?>

<table width="100%" cellpadding="2" cellspacing="2" border="0" class="zebra-table sort-table" id="jsst-periods" up-data='<?= json_encode($sort_cols) ?>'>
	<col /><col /><col /><col />
	<thead>
	<tr class="heading">
		<td class="h" title="N">&nbsp;</td>
		<td class="h" title="Name">Name</td>
		<td class="h" title="TimeStart">Start time</td>
		<td class="h" title="TimeEnd">End Time</td>
		<td class="h" title="Duration">Duration</td>
		<td class="h" title="Days">Days of week</td>
		<td class="n" title="X"></td>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=0;
	if ($periods) {
	foreach ($periods as $period) {
		// Get UNIX timestamp of times to do calculations on
		$time_start = strtotime($period->time_start);
		$time_end = strtotime($period->time_end);
	?>
	<tr>
		<?php
		// $now = timestamp to do calculations with for "current" period
		$now = now();
		// $dayofweek = numeric day of week (1=monday) to get "current" period for periods on this day of the week
		$dayofweek = date('N', $now);
		$key = "day_{$dayofweek}";

		if ( ($now >= $time_start) && ($now < $time_end) && ($period->{$key} == '1') ) {
			$now_img = img('assets/images/ui/school_manage_times.png', 'width="16" height="16" alt="Now"');
		} else {
			$now_img = '';
		}
		?>
		<td width="20" align="center"><?php echo $now_img ?></td>
		<td><?php echo html_escape($period->name) ?></td>
		<td><?php echo strftime('%H:%M', $time_start) ?></td>
		<td><?php echo strftime('%H:%M', $time_end) ?></td>
		<td><?php echo timespan($time_start, $time_end) ?></td>
		<td><?php
		foreach ($days_list as $day_num => $day_name) {
			$key = "day_{$day_num}";
			$letter = "{$day_name[0]}{$day_name[1]}";
			if ($period->{$key} == '1') {
				echo "$letter ";
			} else {
				echo "<span style='color:#ccc'>{$letter}</span> ";
			}
		}
		?></td>
		<td width="45" class="n"><?php
			$actions['edit'] = 'periods/edit/'.$period->period_id;
			$actions['delete'] = 'periods/delete/'.$period->period_id;
			$this->load->view('partials/editdelete', $actions);
			?>
		</td>
	</tr>
	<?php $i++; }
	} else {
		echo '<td colspan="7" align="center" style="padding:16px 0">No periods exist!</td>';
	}
	?>
	</tbody>
</table>

<?php

echo $iconbar;
