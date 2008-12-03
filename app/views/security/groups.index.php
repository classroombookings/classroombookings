<p>Here is a list of the existing user groups. To edit the group name and description, click on the name. Use the <?php echo anchor('security/permissions', 'Permissions') ?> page to configure group permissions.</p>

<?php
if($groups != 0){
?>

<table class="list" width="100%" cellpadding="0" cellspacing="0" border="0">
	<col /><col /><col />
	<thead>
	<tr class="heading">
		<td class="h" title="Name">Group name</td>
		<td class="h" title="Description">Description</td>
		<td class="h" title="Usercount">Number of users</td>
		<td class="h" title="Quota">Quota</td>
		<td class="h" title="Ahead">Booking ahead</td>
		<td class="h" title="X">&nbsp;</td>
	</tr>
	</thead>
	<tbody>
	<?php
	$i = 0;
	foreach ($groups as $group) {
	?>
	<tr>
		<td class="t"><?php echo anchor('security/groups/edit/'.$group->group_id, $group->name) ?></td>
		<td class="m"><span title="<?php echo $group->description ?>"><?php echo word_limiter($group->description, 5) ?>&nbsp;</span></td>
		<td class="x"><?php echo $group->usercount ?>&nbsp;</td>
		<td class="x"><?php
		if($group->quota_type == NULL){
			$q = 'Unlimited';
		} else {
			switch($group->quota_type){
				case 'current': $q = '%d concurrent'; break;
				case 'day': $q = '%d per day'; break;
				case 'week': $q = '%d per week'; break;
				case 'month': $q = '%d per month'; break;
			}
			$q = sprintf($q, $group->quota_num);
		}
		echo $q;
		?></td>
		<td class="x"><?php echo ($group->bookahead != 0) ? $group->bookahead . ' days' : '-';  ?></td>
		<td class="il">
		<?php
		$actiondata[0] = array('security/users/ingroup/'.$group->group_id, 'View users', 'user_orange-sm.gif' );
		$actiondata[1] = array('security/permissions/forgroup/'.$group->group_id, 'Edit permissions', 'key-sm.gif' );
		$actiondata[2] = array('security/groups/delete/'.$group->group_id, 'Delete', 'cross_sm.gif' );
		$this->load->view('parts/listactions', $actiondata);
		#$this->load->view('parts/delete', array('url' => 'security/users/delete/'.$user->user_id));
		?></td>
	</tr>
	<?php $i++; } ?>
	</tbody>
</table>

<?php } else { ?>

<p>No users currently exist!</p>

<?php } ?>
