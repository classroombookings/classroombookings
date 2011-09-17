<?php
$errors = validation_errors();
if($errors){
	echo $this->msg->err('<ul>' . $errors . '</ul>', 'Please check the following invalid item(s) and try again.');
}

echo form_open('users/import/3', NULL, array('step' => 3));

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
				<td width="33%" style="background:#E0E4ED;color:#000"><strong>Username</strong></td>
				<td style="background:#E0E4ED;color:#000"><strong><?php echo $user['username'] ?></strong></td>
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
			<tr>
				<td><strong>Department(s)</strong></td>
				<td><?php
				if (!empty($user['departments']))
				{
					$dnames = array();
					foreach($user['departments'] as $did)
					{
						$dnames[] = $departments[$did];
					}
					echo implode(", ", $dnames);
				}
				?></td>
			</tr>
		</table>
	</div>
	
	<?php
	
}

?>

<br class="add-bottom clear">

<?php
unset($buttons);
$buttons[] = array('submit', 'green', "Import users &rarr;", $t + 1);
$buttons[] = array('link', '', 'Cancel', $t + 2, site_url('users/import/cancel'));
$this->load->view('parts/buttons', array('buttons' => $buttons));
?>

</form>