<!-- <?php print_r($vars) ?> -->

<form action="<?php echo site_url('bookings/load') ?>" method="POST">
<table>
	<tr>
		<td valign="middle"><label for="chosen_date"><strong>Date:</strong></label></td>
		<td valign="middle">
			<input type="text" name="chosen_date" id="chosen_date" size="10" maxlength="10" value="<?php echo date("d/m/Y", $chosen_date) ?>" onchange="this.form.submit()" onblur="this.form.submit()" />
		</td>
		<td valign="middle">
			<img style="cursor:pointer" align="top" src="webroot/images/ui/cal_day.gif" width="16" height="16" title="Choose date" onclick="displayDatePicker('chosen_date', false);" />
		</td>
		
		<td> &nbsp; <input type="submit" value=" Load " /></td>
	</tr>
</table>
</form>

<br /> 

<?php
$today_weeknum = date('4');

/*$minusval = $today_weeknum-1;

$monday = mktime(0, 0, 0, 11, 1-$minusval,  date("Y"));
echo date("Y-m-d", $monday);*/

#echo date("Y-m-d", strtotime("last Monday", mktime(0,0,0,11, 1, 2006)));


#$chosen_date = mktime(0,0,0,date('n'),date('d'),date('Y'));

if( date("w", $chosen_date) == 1 ){
	$nextdate = date("Y-m-d", $chosen_date);
} else {
	$nextdate = date("Y-m-d", strtotime("last Monday", $chosen_date));
}
#echo $nextdate;


foreach($weeks as $week){
	$bweeks[$week->week_id] = $week;
}

$thisweek = $bweeks[$weekdateids[$nextdate]];
$bgcol = $thisweek->bgcol;
$fgcol = $thisweek->fgcol;
$name = $thisweek->name;


/*
$ay_start = mktime(0,0,0,9,4,2006);
$ay_end = mktime(0,0,0,7,20,2007);

echo date("w", $ay_start);

echo '<br><br>';

while($ay_start <= $ay_end){
	if( date("w", $ay_start) == 1 ){
		$nextdate = date("Y-m-d", $ay_start);
	} else {
		$nextdate = date("Y-m-d", strtotime("last Monday", $ay_start));
	}
	$ay_start = strtotime("next Monday", $ay_start);
	echo $nextdate."<br/>";
}*/



$style = sprintf('padding:3px;font-weight:bold;background:#%s;color:#%s', $bgcol, $fgcol);
?>

<div class="bookings_week" style="<?php echo $style ?>;">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
<td width="20">
<a href="bookings/view/<?php echo date("Y-m-d", strtotime("yesterday", $chosen_date)); ?>/back"><img src="webroot/images/ui/resultset_previous.gif" width="16" height="16" /></a>
</td>
<td align="center"><?php echo date("l jS F Y", $chosen_date) . ' - ' .$thisweek->name ?></td>
<td width="20">
<a href="bookings/view/<?php echo date("Y-m-d", strtotime("tomorrow", $chosen_date)) ?>/forward"><img src="webroot/images/ui/resultset_next.gif" width="16" height="16" /></a>
</td>
</tr>
</table>
</div>

<br />

<table border="0" bordercolor="#ffffff" cellpadding="2" cellspacing="2" class="bookings" width="100%">

<tr>

	<td colspan="2">&nbsp;</td>
	
	<?php
	$today = date('w', $chosen_date);
	foreach($periods as $period){
		if($period->bookable == 1){
			$days_bitmask->reverse_mask($period->days);
			if($days_bitmask->bit_isset($today)){
				echo '<td align="center">';
				echo "<strong>{$period->name}</strong><br />";
				echo '<span style="font-size:90%">'.date('g:i', strtotime($period->time_start)).'-'.date('g:i', strtotime($period->time_end)).'</span>';
				echo '</td>';
			}
		}
	}
	?>
		
</tr>
	
	<?php
	$jscript = '';
	foreach($rooms as $room){
	
		if($bookings){
			foreach($bookings as $booking){
				$bookings_rooms[$booking['room_id']] = $booking;
				#echo $booking['room_id'];
			}
		}
	
		echo '<tr>';
		echo '<td align="center" valign="middle" width="16" height="40">';
		if($room->photo != NULL){
			$photo = 'webroot/images/roomphotos/640/'.$room->photo;
			$photo_sm = 'webroot/images/roomphotos/160/'.$room->photo;
			$jscript .= "messages[{$room->room_id}] = new Array('$photo_sm','{$room->location}');\n";
			if( file_exists($photo) ){
				echo '<a href="'.$photo.'" title="View Photo" onmouseover="doTooltip(event,'.$room->room_id.')" onmouseout="hideTip()"><img src="webroot/images/ui/picture.gif" width="16" height="16" alt="View Photo" /></a>'."\n";
			}
		} else {
			echo '&nbsp;';
		}
		echo '</td>';
		echo '<td align="center" valign="middle" width="100">';
		echo "<br /><strong>{$room->name}</strong><br />";
		echo '<span style="font-size:90%">'.$room->username.'&nbsp;</span>';
		echo '</td>';
		
		foreach($periods as $period){
			if($period->bookable == 1){
				$days_bitmask->reverse_mask($period->days);
				if($days_bitmask->bit_isset($today)){
					$class = 'free';
					echo '<td align="center" class="'.$class.'">';
					#echo $period->period_id;
					if(isset($bookings_rooms[$room->room_id])){
						if($bookings_rooms[$room->room_id]['period_id'] == $period->period_id){
							echo $bookings_rooms[$room->room_id]['booking_id'];
						}
					}
					#echo $room->room_id;
					echo '</td>';
					#echo '<td class="free" align="center"><a href="#" onclick="book">Book</a></td>';
				}
			}
		}
	
		echo '</tr>';	
	}
	
?>		

</table>

<script type="text/javascript"><?php echo $jscript ?></script>
