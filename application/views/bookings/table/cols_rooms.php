<?php
$event = '';
$width = 400;
if($photo != NULL){
	$photo_lg = 'webroot/images/roomphotos/640/'.$photo;
	$photo_sm = 'webroot/images/roomphotos/160/'.$photo;
	$jscript = "<script>messages[$room_id] = new Array('$photo_sm','{$location}');</script>\n";
	echo $jscript;
	$event = 'onmouseover="doTooltip(event,'.$room_id.')" onmouseout="hideTip()"';
	$width = 760;
}
$url = site_url('rooms/info/'.$this->session->userdata('school_id').'/'.$room_id);
$title = '<a onclick="window.open(\''.$url.'\',\'\',\'width='.$width.',height=360,scrollbars\');return false;" href="'.$url.'" title="View More Information" '.$event.'>'.$name.'</a>'."\n";
?>

<td align="center" width="<?php echo $width ?>">
	<strong><?php echo $title ?></strong><br />
	<span style="font-size:90%">
		<?php echo ($displayname == '') ? $username : $displayname; ?> &nbsp;
	</span>
</td>