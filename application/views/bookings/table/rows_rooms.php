<?php
if (isset($roomphoto)){
	$width = 760;
} else {
	$width = 400;
}

$url = site_url('rooms/info/'.$room_id);
$title = '<a onclick="window.open(\''.$url.'\',\'\',\'width='.$width.',height=360,scrollbars\');return false;" href="'.$url.'" title="View More Information">'.$name.'</a>'."\n";
?>

<td align="right" valign="middle" width="100" style="padding:15px 5px;">
	<strong><?php echo $title ?></strong>
	<?php
	$user = ($displayname == '') ? $username : $displayname;
	if ( ! empty($user)) {
		echo "<br>";
		echo "<span style='font-size:90%;'>{$user}</span>";
	}
	?>
</td>
