<?php
$url = site_url('rooms/info/'.$school_id.'/'.$room_id);
$title = '<a onclick="window.open(\''.$url.'\',\'\',\'width='.$roomtitle['width'].',height=360,scrollbars\');return false;" href="'.$url.'" title="View More Information" '.$roomtitle['event'].'>'.$name.'</a>'."\n";
?>
<td align="center" valign="middle" width="100"><br />
<strong><?php echo $title ?></strong><br />
<span style="font-size:90%"><?php echo ($displayname == '') ? $username : $displayname; ?>&nbsp;</span>
</td>
