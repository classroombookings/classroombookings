<p>Here is a list of the existing users, including those that authenticate via LDAP. To edit a user's details or properties, click on their username. Use the links on the right to view an audit trail of their actions or delete them.</p>

<?php
if($users != 0){
?>

<table class="list">
	<col /><col /><col /><col /><col />
	<thead>
	<tr class="heading">
		<td class="h" title="User">User</td>
		<td class="h" title="Quota">Quota</td>
		<td class="h" title="Lastlogin">Last login</td>
		<td class="h" title="Type">Type</td>
		<td class="h" title="Options">&nbsp;</td>
	</tr>
	</thead>
	<tbody>
	<?php
	$i = 0;
	foreach ($users as $user) {
	?>
	<tr class="tr<?php echo ($i & 1); echo ($user->enabled == 0) ? ' disabled' : NULL; ?>">
		
		<td class="t">
			<?php echo anchor('security/users/edit/'.$user->user_id, $user->displayname) ?>
			<span><?php echo $user->groupname ?></span>
		</td>
		
		<td class="m"><?php
		if($user->quota_type == NULL){
			$text = '(Unlimited)';
		} else {
			switch($user->quota_type){
				case 'current': $q = '%d concurrent'; break;
				case 'day': $q = '%d per day'; break;
				case 'week': $q = '%d per week'; break;
				case 'month': $q = '%d per month'; break;
			}
			$text = sprintf($q, $user->quota_num);
		}
		echo "0 / $text";
		?>
		</td>
		
		<td class="m"><?php echo mysqlhuman($user->lastlogin, "d/m/Y H:i") ?>&nbsp;</td>
		
		<td class="m"><?php echo ($user->ldap == 1) ? 'LDAP' : 'Local'; ?></td>
		
		<td class="il"><?php
			unset($actiondata);
			$actiondata[] = array('security/users/view/'.$user->user_id, ' ', 'magnifier_sm.gif', 'View report');
			$actiondata[] = array('security/permissions/effective/'.$user->user_id, ' ', 'key-sm.gif', 'Effective permissions', FALSE, 'rel="boxy"');
			$actiondata[] = array('security/users/delete/'.$user->user_id, ' ', 'cross_sm.gif', 'Delete user');
			$this->load->view('parts/linkbar', $actiondata);
		?></td>
		
	</tr>
	<?php $i++; } ?>
	</tbody>
</table>

<?php echo $this->pagination->create_links() ?>


<script type="text/javascript">
_jsQ.push(function(){
	$('a[rel=boxy]').click(function(){
		Boxy.load($(this).attr("href"), {cache:'false', title: 'Effective Permissions'});
		return false;
	});
});
</script> 

<?php
} else {
?>

<!-- This shouldn't happen... -->
<p>No users currently exist!</p>

<?php
}
?>
