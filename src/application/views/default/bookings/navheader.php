<?php
if(!$week){
	$week->week_id = 0;
	$week->name = '(No week configured)';
	$week->colour = 'ffffff';
}
switch($mode){
	
	case 'week':
		
		if($week){
			$title = 'Week beginning %s - %s';
			$nicedate = date('l jS F Y', strtotime($week_start));
			$title = sprintf($title, $nicedate, $week->name);
			$class = 'week_' . $week->week_id;
			$col_contrast = (isdark($week->colour)) ? 'fff' : '000';
		} else {
			$title = '(No week configured)';
			$class = 'bg-red';
		}
		// Get just the day from the target URLs
		$prev_arr = explode('/', $prev['href']);
		$prev_day = $prev_arr[count($prev_arr)-1];
		$next_arr = explode('/', $next['href']);
		$next_day = $next_arr[count($next_arr)-1];
		
	break;
	
	case 'day':
		
		if($date){
			$title = 'Date: %s - %s';
			$nicedate = date('l jS F Y', strtotime($date));
			$title = sprintf($title, $nicedate, $week->name);
			$class = 'week_' . $week->week_id;
			$col_contrast = (isdark($week->colour)) ? 'fff' : '000';
		} else {
			$title = '(No week configured)';
			$class = 'bg-red';
		}
		
		// Get just the day from the target URLs
		$prev_arr = explode('/', $prev['href']);
		$prev_day = $prev_arr[count($prev_arr)-1];
		$next_arr = explode('/', $next['href']);
		$next_day = $next_arr[count($next_arr)-1];
	
	break;

}

?>

<div id="navheader" class="<?php echo $class ?>">

	<table>
		<tr>
			<td class="text"><?php echo $title ?></td>
			<td class="nav">
				<?php
				if($prev['href'] != NULL){
					$prev['text'] = '<img src="img/nav-arr-left-' . $col_contrast . '.png" />';
					echo anchor($prev['href'], $prev['text'], 'rel="navheader" id="'.$prev_day.'"');
				}
				?>
			</td>
			<td class="nav">
				<?php
				if($next['href'] != NULL){
					$next['text'] = '<img src="img/nav-arr-right-' . $col_contrast . '.png" />';
					echo anchor($next['href'], $next['text'], 'rel="navheader" id="'.$next_day.'"');
				}
				?>
			</td>
			<td style="width:10px;">&nbsp;</td>
		</tr>
	</table>
	
</div>