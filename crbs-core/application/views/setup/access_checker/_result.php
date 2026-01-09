<?php

if (isset($result)) {
	foreach ($result as $group => $permissions) {
		$group_label = lang(sprintf('permission.group.%s', $group));
		$group_label = html_escape($group_label);
		echo "<h6 style='margin-bottom:4px;margin-top:0;'>{$group_label}</h6>";
		echo "<div style='padding:0 0 24px 12px'>";
		foreach ($permissions as $permission => $has_access) {

			$title = lang(sprintf('permission.%s', $permission));
			$title = empty($title) ? $permission : html_escape($title);

			if ($has_access) {
				$char = '&#x2611;';
				$cls = 'flex align-items-center';
				$style = '';
			} else {
				$char = '&#x2610;';
				$cls = 'flex align-items-center';
				$style = 'color:#777';
			}

			$char = "<div style='font-size:150%;line-height:1;display:inline-block;margin-right:4px'>{$char}</div>";
			echo "<div style='margin:0;padding:0;padding:4px 0;{$style}' class='{$cls}'>{$char} {$title}</div>";

		}
		echo "</div>";
	}
}
