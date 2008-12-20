<p style="text-align:left;">
<?php if($user_permissions != NULL){ ?>
<ul style="text-align:left;margin:0 0 0 1em">
	<?php
	foreach($user_permissions as $permission){
		echo sprintf('<li>%s</li>', $permission[1]);
	}
	?>
</ul>
<?php } else { ?>
No permissions configured for this group.
<?php } ?>
</p>
