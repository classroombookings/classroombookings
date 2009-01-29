<?php
if(count($success) > 0 && count($fail) > 0){
	$message = $this->msg->note('Some users were successfully imported but some others could not be added.');
	$return = 'both';
} elseif( count($success) > 0 && empty($fail) ){
	$message = $this->msg->info('All users were successfully imported.');
	$return = 'success';
} elseif( empty($success) && count($fail) > 0){
	$message = $this->msg->err('All of the users could not be imported.');
	$return = 'fail';
}
?>


<?php if($return == 'both'){ ?>

	<table cellpadding="2" cellspacing="0" width="100%">
		<tr>
			<td width="50%" valign="top">
				<p>The following users were successfully imported.</p>
				<ul class="success">
					<?php
					foreach($success as $user){
						echo sprintf('<li>%s</li>', $user['username']);
					}
					?>
				</ul>
			</td>
			<td width="50%" valign="top">
				<p>The following users could not be imported.</p>
				<ul class="fail">
					<?php
					foreach($fail as $user){
						echo sprintf('<li>%s (Reason: %s)</li>', $user['username'], $user['fail']);
					}
					?>
				</ul>
			</td>
		</tr>
	</table>

<?php } ?>


<?php if($return == 'success'){ ?>

<p>The following users were successfully imported.</p>

<ul class="success">
	<?php
	foreach($success as $user){
		echo sprintf('<li>%s</li>', $user['username']);
	}
	?>
</ul>

<?php } else if($return == 'fail'){ ?>

<p>The following users were not imported.</p>

<ul class="fail">
	<?php
	foreach($fail as $user){
		echo sprintf('<li>%s (Reason: %s)</li>', $user['username'], $user['fail']);
	}
	?>
</ul>

<?php } ?>

<p><a href="<?php echo site_url('security/users') ?>">Click here to back to the users page.</a>