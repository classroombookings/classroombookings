<?php
if($mode == 'week'){
	if($week){
		$title = 'Week beginning %s - %s';
		$nicedate = date('l jS F Y', strtotime($week_start));
		$title = sprintf($title, $nicedate, $week->name);
		$class = 'week_' . $week->week_id;
	} else {
		$title = '(No week configured)';
		$class = 'bg-red';
	}
	// Get just the day from the target URLs
	$prev_arr = explode('/', $prev['href']);
	$prev_day = $prev_arr[count($prev_arr)-1];
	$next_arr = explode('/', $next['href']);
	$next_day = $next_arr[count($next_arr)-1];
}

?>

<div id="navheader" class="<?php echo $class ?>">
	<table width="100%">
		<tr>
			
			<td align="left" width="150">
				<?php if($prev['href'] != NULL){ echo anchor($prev['href'], $prev['text'], 'rel="navheader" id="'.$prev_day.'"'); } ?>
			</td>
			
			<td align="center"><?php echo $title ?></td>
			
			<td align="right" width="150">
				<?php if($next['href'] != NULL){ echo anchor($next['href'], $next['text'], 'rel="navheader" id="'.$next_day.'"'); } ?>
			</td>
			
		</tr>
	</table>
</div>