<?php
if($periods != 0){
?>

<table class="list" width="100%" cellpadding="0" cellspacing="0" border="0">
	<col /><col /><col />
	<thead>
	<tr class="heading">
		<td class="h" title="Current"><img src="img/ico/clock1.gif" width="16" height="16" alt="Now" title="Current period" /></td>
		<td class="h" title="Bookable"><img src="img/ico/f_yes.gif" width="16" height="16" alt="Active" title="Can be booked" /></td>
		<td class="h" title="Name">Name</td>
		<td class="h" title="TimeStart">Start Time</td>
		<td class="h" title="TimeEnd">End Time</td>
		<td class="h" title="Duration">Duration</td>
		<td class="h" title="DoW">Days of the week</td>
		<td class="h" title="X">&nbsp;</td>
	</tr>
	</thead>
	<tbody>
	<?php
	$i = 0;
	foreach($periods as $period){
		
		// Get UNIX timestamp of times to do calculations on
		$time_start = strtotime($period->time_start);
		$time_end = strtotime($period->time_end);
		$now = now();
		$dayofweek = date('w', $now);
		
		if( ($now >= $time_start) && ($now < $time_end) && (in_array($dayofweek, $period->days) ) ){
			$now_img = '<img src="img/ico/clock1.gif" width="16" height="16" alt="Now" title="This is the current period"/>';
		} else {
			$now_img = '';
		}
		
	?>
	<tr>
		<td align="center" width="20"><?php echo $now_img; ?></td>
		<td align="center" width="20"><?php if($period->bookable == 1){ ?><img src="img/ico/f_yes.gif" width="16" height="16" alt="Active" title="This period can be booked" /><?php } ?></td>
		<td><?php echo anchor('academic/periods/edit/'.$period->period_id, $period->name) ?></td>
		<td><?php echo strftime("%H:%M", $time_start) ?></td>
		<td><?php echo strftime("%H:%M", $time_end) ?></td>
		<td><?php echo timespan($time_start, $time_end) ?></td>
		<td><?php
		foreach($days as $num => $name){
			$day = (in_array($num, $period->days)) ? '%s' : '<span style="color:#ccc">%s</span>';
			echo sprintf($day. ' ', $name{0});
		}
		?></td>
		<td class="il">
		<?php
		$actiondata[] = array('academic/periods/delete/'.$period->period_id, 'Delete', 'cross_sm.gif');
		$this->load->view('parts/listactions', $actiondata);
		unset($actiondata);
		?></td>
	</tr>
	<?php $i++; } ?>
	</tbody>
</table>

<?php } else { ?>

<p>No periods currently exist!</p>

<?php } ?>