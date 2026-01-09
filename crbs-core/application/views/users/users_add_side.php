<?php

if (isset($user)) {
	$url = site_url('setup/access_checker/user/'.$user->user_id);
	echo "<div hx-get='{$url}' hx-trigger='load' hx-swap='outerHTML'></div>";
}
