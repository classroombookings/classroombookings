<p style="text-align:left;">
<ul style="text-align:left;margin:0 0 0 1em">
	<?php
	foreach($user_permissions as $permission){
		echo sprintf('<li>%s</li>', $permission[1]);
	}
	?>
</ul>
</p>
