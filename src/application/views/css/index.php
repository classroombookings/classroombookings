<?php
foreach ($weeks as $week)
{
	$w = array();
	$name = $week->name;
	$id = $week->week_id;
	$colour = $week->colour;
	$contrast = (is_dark($week->colour)) ? '#fff' : '#000';
	?>
	/* <?php echo $name ?> */
	table tr.week_<?php echo $id ?> td { background: <?php echo $colour ?>; color: <?php echo $contrast ?> }
	.week_<?php echo $id ?> { background: <?php echo $colour ?>; color: <?php echo $contrast ?> }
	.week_<?php echo $id ?> a{ color: <?php echo $contrast ?> !important }
	.week_<?php echo $id ?>_fg { color: <?php echo $colour ?> }
	<?php
}
?>