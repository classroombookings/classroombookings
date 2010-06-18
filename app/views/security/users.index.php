<p>Here is a list of the existing users, including those that authenticate via LDAP. To edit a user's details or properties, click on their username. Use the links on the right to view an audit trail of their actions or delete them.</p>

<?php
if($users != 0){
?>

<table class="list" width="99%" cellpadding="0" cellspacing="0" border="0">
	<col /><col /><col />
	<thead>
	<tr class="heading">
		<td class="h" title="User">User</td>
		<td class="h" title="Name">Group</td>
		<td class="h" title="Quota">Quota</td>
		<td class="h" title="Lastlogin">Last login</td>
		<td class="h" title="Lastactivity">Last activity</td>
		<td class="h" title="Type">Type</td>
		<td class="h" title="X">&nbsp;</td>
	</tr>
	</thead>
	<tbody>
	<?php
	$i = 0;
	foreach ($users as $user) {
	?>
	<tr class="tr<?php echo ($i & 1); echo ($user->enabled == 0) ? ' disabled' : NULL; ?>">
		<td class="t"><?php echo anchor('security/users/edit/'.$user->user_id, $user->displayname) ?></td>
		<!-- <td class="m"><?php echo $user->displayname ?>&nbsp;</td> -->
		<td class="m"><?php echo $user->groupname ?>&nbsp;</td>
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
		echo $text;
		?>
		</td>
		<td class="m"><?php echo mysqlhuman($user->lastlogin, "d/m/Y H:i") ?>&nbsp;</td>
		<td class="m"><?php echo mysqlhuman($user->lastactivity, "d/m/Y H:i") ?>&nbsp;</td>
		<td class="m"><?php echo ($user->ldap == 1) ? 'LDAP' : 'Local'; ?></td>
		<td class="il">
		<?php
		$actiondata[0] = array('security/users/view/'.$user->user_id, 'Report', 'magnifier_sm.gif');
		$actiondata[1] = array('security/permissions/effective/'.$user->user_id, 'Effective permissions', 'key-sm.gif', 'facebox');
		$actiondata[2] = array('security/users/delete/'.$user->user_id, 'Delete', 'cross_sm.gif');
		$this->load->view('parts/listactions', $actiondata);
		#$this->load->view('parts/delete', array('url' => 'security/users/delete/'.$user->user_id));
		?></td>
	</tr>
	<?php $i++; } ?>
	</tbody>
</table>

<?php echo $this->pagination->create_links() ?>


<script type='text/javascript'>
/*$(function(){
	$('a[rel*=facebox]').click(function(){
		jQuery.facebox(function($){
			$.get($(this).attr("href") + "/ajax", function(data){ $.facebox(data); return false; });
		});
		return false;
	});
	return false;
});*/

/* jQuery.facebox(function($){
	$.get('blah.html', function(data) { $.facebox(data) })
}) */


$(document).ready(function($){
	//$('.facebox').boxy({title:'Effective Permissions'});
	$('.facebox').facebox();
});


//$('a[class*=facebox]').attr("href", $(this).attr("href") + "/ajax");
/*jQuery(document).ready(function($){
	$('a[class*=facebox]').facebox();
});*/ /* WHEN JS LIB WAS JQUERY, THIS ONE WORKED */






/* $(function(){
	$('.boxy').click(function(){
		Boxy.load($(this).attr("href") + "/ajax", {cache:'false', title: 'Effective Permissions'});
		return false;
	});
}); */

/* 
$$('a.win').each(function(el){
	el.href = el.href + "/ajax"
	var ajax = new Control.Window(el,{
		className: 'simple_window',
		closeOnClick: true,
	});
}); */

</script> 

<?php
} else {
?>

<!-- This shouldn't happen... -->
<p>No users currently exist!</p>

<?php
}
?>
