<p>Here is a list of the existing users, including those that authenticate via LDAP. To edit a user's details or properties, click on their username. Use the links on the right to view an audit trail of their actions or delete them.</p>

<?php
if($users != 0){
?>

<table class="list" width="99%" cellpadding="0" cellspacing="0" border="0">
	<col /><col /><col />
	<thead>
	<tr class="heading">
		<td class="h" title="Username">Username</td>
		<td class="h" title="Name">Display name</td>
		<td class="h" title="Name">Group</td>
		<td class="h" title="Lastlogin">Last login</td>
		<td class="h" title="Type">Type</td>
		<td class="h" title="X">&nbsp;</td>
	</tr>
	</thead>
	<tbody>
	<?php
	$i = 0;
	foreach ($users as $user) {
	?>
	<!-- <tr class="tr<?php echo ($i & 1) ?>">-->
	<tr class="<?php echo ($i & 1); echo ($user->enabled == 0) ? ' disabled' : NULL; ?>">
		<td class="t"><?php echo anchor('security/users/edit/'.$user->user_id, $user->username) ?></td>
		<td class="m"><?php echo $user->displayname ?>&nbsp;</td>
		<td class="m"><?php echo $user->groupname ?>&nbsp;</td>
		<td class="m"><?php echo mysqlhuman($user->lastlogin, "d/m/Y H:i") ?>&nbsp;</td>
		<td class="m"><?php echo ($user->ldap == 1) ? 'LDAP' : 'Local'; ?></td>
		<td class="il" width="270">
		<?php
		$actiondata[0] = array('security/users/view/'.$user->user_id, 'Report', 'magnifier_sm.gif' );
		$actiondata[1] = array('security/permissions/effective/'.$user->user_id, 'View effective permissions', 'key-sm.gif' );
		$actiondata[2] = array('security/users/delete/'.$user->user_id, 'Delete', 'cross_sm.gif' );
		$this->load->view('parts/listactions', $actiondata);
		#$this->load->view('parts/delete', array('url' => 'security/users/delete/'.$user->user_id));
		?></td>
	</tr>
	<?php $i++; } ?>
	</tbody>
</table>

<?php
} else {
?>

<p>No users currently exist!</p>

<?php
}
?>
