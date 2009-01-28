<?php
$errors = validation_errors();
if($errors){
	echo $this->msg->err('<ul>' . $errors . '</ul>', 'Please check the following invalid item(s) and try again.');
}

echo form_open('security/users/import/3', NULL, array('stage' => 3));

// Start tabindex
$t = 1;
?>

<p>Please check the details of the users you chose to import are correct.</p>

<?php

foreach($users as $user){
	
	?>
	<div style="width:400px;margin:1.2em 2.4em 1.2em 0;float:left;border:0px solid #ccc;background:#fff">
		<table width="100%" class="list" cellspacing="0">
			<tr>
				<td width="33%" style="background:#60AA37;color:#fff"><strong>Username</strong></td>
				<td style="background:#60AA37;color:#fff"><strong><?php echo $user['username'] ?></strong></td>
			</tr>
			<tr>
				<td><strong>Password</strong></td>
				<td><?php echo $user['password'] ?></td>
			</tr>
			<tr>
				<td><strong>Display name</strong></td>
				<td><?php echo $user['display'] ?></td>
			</tr>
			<tr>
				<td><strong>Email address</strong></td>
				<td><?php echo $user['email'] ?></td>
			</tr>
			<tr>
				<td><strong>Group</strong></td>
				<td><?php echo $groups[$user['group_id']] ?></td>
			</tr>
		</table>
	</div>
	
	<?php
	
}

?>

<?php
unset($buttons);
$buttons[] = array('submit', 'positive', 'Import users', 'database-arr2.gif', $t);
$buttons[] = array('cancel', 'negative', 'Cancel', 'arr-left.gif', $t+1, site_url('security/users/import'));
$this->load->view('parts/buttons', array('buttons' => $buttons));
?>

</form>