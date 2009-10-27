<?php if($user_permissions != NULL){ ?>
<ul>
	<?php
	foreach($user_permissions as $permission){
		echo sprintf('<li>%s</li>', $permission[1]);
	}
	?>
</ul>
<?php } else { ?>
No permissions configured for this group.
<?php } ?>