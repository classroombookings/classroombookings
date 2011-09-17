<?php
if (count($success) > 0 && count($fail) > 0)
{
	$message = $this->msg->err('Some users were successfully imported but some others could not be added.');
	$return = 'both';
}
elseif (count($success) > 0 && empty($fail))
{
	$message = $this->msg->notice('All users were successfully imported.');
	$return = 'success';
}
elseif (empty($success) && count($fail) > 0)
{
	$message = $this->msg->err('All of the users could not be imported.');
	$return = 'fail';
}

$message .= '<br>';

?>




<?php if ($return == 'both'): ?>

<?php echo $message ?>

	<table cellpadding="2" cellspacing="0" width="100%">
		<tr>
			<td width="50%" valign="top">
				<p><strong>The following users were successfully imported.</strong></p>
				<ul class="success">
					<?php
					foreach($success as $user){
						echo sprintf('<li>%s</li>', $user['username']);
					}
					?>
				</ul>
			</td>
			<td width="50%" valign="top">
				<p><strong>The following users could not be imported.</strong></p>
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

<?php endif; ?>




<?php if ($return == 'success'): ?>

<?php echo $message ?>

<ul class="success">
	<?php
	foreach($success as $user){
		echo sprintf('<li>%s</li>', $user['username']);
	}
	?>
</ul>

<?php endif; ?>




<?php if ($return == 'fail'): ?>

<?php echo $message ?>

<ul class="fail">
	<?php
	foreach($fail as $user){
		echo sprintf('<li>%s (Reason: %s)</li>', $user['username'], $user['fail']);
	}
	?>
</ul>

<?php endif; ?>

<p><a href="<?php echo site_url('users') ?>">Click here to back to the users page.</a>