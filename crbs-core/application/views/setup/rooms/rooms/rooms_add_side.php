<?php

if (isset($room)) {
	$url = site_url('setup/access_checker/room/'.$room->room_id);
	echo "<div hx-get='{$url}' hx-trigger='load' hx-swap='outerHTML'></div>";
}
