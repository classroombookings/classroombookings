<p>Here is a list of the existing user groups. To edit the group name and description, click on the name. Use the <?php echo anchor('security/permissions', 'Permissions') ?> page to configure group permissions.</p>

<?php
if($groups != 0){
?>

<table class="list">
	<col /><col /><col /><col /><col />
	<thead>
	<tr class="heading">
		<td class="h" title="Name">Group name</td>
		<td class="h" title="Usercount">Users</td>
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
		
		<td class="x">
			<strong><?php echo anchor('security/groups/edit/'.$group->group_id, $group->name) ?></strong><br />
			<span title="<?php echo $group->description ?>"><?php echo word_limiter($group->description, 5) ?>&nbsp;</span>
		</td>
		
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
		
		<td class="x"><?php echo ($group->bookahead != 0) ? $group->bookahead . ' days' : 'No Limit';  ?></td>
		
		<td class="il"><?php
			unset($actiondata);
			$actiondata[] = array('security/users/ingroup/'.$group->group_id, ' ', 'user_orange-sm.gif' );
			$actiondata[] = array('security/permissions/forgroup/'.$group->group_id, ' ', 'key-sm.gif' );
			$actiondata[] = array('security/groups/delete/'.$group->group_id, ' ', 'cross_sm.gif' );
			$this->load->view('parts/linkbar', $actiondata);
		?></td>
		
	</tr>
	<?php $i++; } ?>
	</tbody>
</table>

<?php } else { ?>

<p>No users currently exist!</p>

<?php } ?>
