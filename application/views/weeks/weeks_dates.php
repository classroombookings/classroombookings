<fieldset><legend accesskey="D" tabindex="6">Week Dates</legend>

<div>Please select the week-commencing (Monday) dates within the current academic year that this week applies to.</div>

<?php
/*
<p>
  <label for="try_automatic">Fill in automatically</label>
  <?php
	#$photo = @field($this->validation->name, $room->name);
	echo form_checkbox( array(
		'name' => 'try_automatic',
		'id' => 'try_automatic',
		'value' => 'true',
		'tabindex' => '7',
		'checked' => false,
	) );
	?>
	<p class="hint">Allow classroombookings to attempt to fill in the rest of the week dates automatically. This requires you tick <span>atleast <strong>two</strong></span> dates.</p>
</p>

<br />
*/
?>

<?php

echo '<table width="100%" cellpadding="0" cellspacing="10">';
echo '<tbody>';
$row = 0;

if($weeks){
	foreach($weeks as $oneweek){
		$weekdata[$oneweek->week_id]['fgcol'] = $oneweek->fgcol;
		$weekdata[$oneweek->week_id]['bgcol'] = $oneweek->bgcol;
	}
}

#print_r($weekdata);

foreach($mondays as $monday){
	$checked = '';
	if(isset($monday['holiday']) && $monday['holiday'] == true){
		$checkbox_disabled = '';	//' disabled="disabled" ';
		$cell_style = 'border:1px solid #888;';
	} else {
		$checkbox_disabled = '';
		$cell_style = '';
	}
	$fgcol = '#000';
	if(isset($monday['week_id']) && $monday['week_id'] != NULL){
		$cell_style = "background:#{$weekdata[$monday['week_id']]['bgcol']};";
		$fgcol = '#'.$weekdata[$monday['week_id']]['fgcol'];
	}
	if($row == 0){ echo '<tr>'; }

	if(isset($monday['week_id']) && ($monday['week_id'] == @field($week->week_id)) && isset($week->week_id)){
		$checked = 'checked="checked"';
	} else {
		$checked = '';
	}

	echo '<td style="'.$cell_style.'padding:4px;" width="'.round(100/$weekscount).'%">';
	$input = '<input type="checkbox" name="dates[]" value="'.$monday['date'].'" id="'.$monday['date'].'" '.$checkbox_disabled.' '.$checked.' /> ';
	echo '<label class="ni" for="'.$monday['date'].'" style="color:'.$fgcol.'">';
	echo $input;
	echo date("d M Y", strtotime($monday['date']));
	echo '</label>';
	echo '</td>';
	echo "\n";
	if($row == $weekscount-1){ echo "</tr>\n\n"; $row = -1; }
	$row++;
}

echo '</tbody>';
echo '</table>';


/* $start_date = strtotime($academicyear->date_start);
$end_date = strtotime($academicyear->date_end);	//mktime(0,0,0,$_POST['end_month'],$_POST['end_day'],$_POST['end_year']);
$time_between = $end_date - $start_date;
//find the days
$day_count = ceil($time_between/24/60/60);
//find the names/dates of the days
for($i=0;$i<=$day_count;$i++){
    if($i==0 && date("l",$newtime) != "Monday"){
    	//we're starting in the middle of a week.... show 1 earlier week than the code that follows
    	for($s=1;$s<=6;$s++){
    		$newtime = $start_date-($s*60*60*24);
    		if(date("l",$newtime) == "Monday"){
    			$end_of_week = $newtime+(6*60*60*24);
    			echo date("d.m.Y",$newtime)."<br/><br/>";	// through ".date("F jS, Y",$end_of_week)." is a week.<br />";
    		}
    	}
    } else {
    	$newtime = $start_date+($i*60*60*24);
    	if(date("l",$newtime) == "Monday"){
    		//Beginning of a week... show it
    		$end_of_week = $newtime+(6*60*60*24);
    		echo date("d.m.Y",$newtime)."<br/><br/>";	// through ".date("F jS, Y",$end_of_week)." is a week.<br /><br />";
    	}
    }
} */
?>


</fieldset>
