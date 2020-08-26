<?php
$url = site_url("rooms/info/{$room_id}");
$name = html_escape($name);
$link = "<a href='{$url}' up-drawer='.room-info' up-history='false' up-tooltip='View room details' up-preload>{$name}</a>";
// $link = "<a href='{$url}' up-position='right' up-align='center' up-popup='.room-info' up-history='false' up-preload>{$name}</a>";
?>

<td align="right" valign="middle" width="100" style="padding:15px 5px;">
	<strong><?php echo $link ?></strong>
	<?php
	$user = ($displayname == '') ? $username : $displayname;
	if ( ! empty($user)) {
		$user = html_escape($user);
		echo "<br>";
		echo "<span style='font-size:90%;'>{$user}</span>";
	}
	?>
</td>
