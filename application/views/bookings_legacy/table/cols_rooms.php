<?php
$url = site_url("rooms/info/{$room_id}");
$name = html_escape($name);
$link = "<a href='{$url}' up-position='left' up-drawer='.room-info' up-history='false' up-preload>{$name}</a>";
?>

<td align="center" width="<?php echo $width ?>">
	<strong><?php echo $link ?></strong><br />
	<span style="font-size:90%">
		<?php echo html_escape(($displayname == '') ? $username : $displayname); ?> &nbsp;
	</span>
</td>
