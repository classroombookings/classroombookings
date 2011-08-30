<div class="filter add-bottom"><div class="row">
		<div class="lfloat">
			<input id="search" size="30" class="search" autocomplete="off">
		</div>
		<div class="rfloat">
			<?php
			$groups[-1] = 'In group...';
			ksort($groups);
			echo form_dropdown(
				'auth_ldap_groupid', 
				$groups,
				-1,
				'id="group"'
			);
			?>
		</div>
</div></div>


<table class="list2" summary="User list" id="users">
	
	<thead>
		<tr>
			<th scope="col">User</th>
			<th scope="col">Quota</th>
			<th scope="col">Last login</th>
			<th scope="col">Type</th>
			<th scope="col">Actions</th>
		</tr>
	</thead>
	
	<tbody>
		
		<?php foreach($users as $user): ?>
		
		<?php
		$classes = array();
		if ($user->enabled == 0) $classes[] = 'disabled';
		$classes[] = 'groupid-' . $user->group_id;
		?>
		
		<tr class="<?php echo implode(' ', $classes) ?>">
			
			<td class="title">
				<?php echo anchor('security/users/edit/' . $user->user_id, $user->displayname . " ", 'rel="edit"') ?>
				<!-- <br><span><?php echo $user->groupname ?></span> -->
			</td>
			
			<td>
				<?php
				if ($user->quota_type == NULL)
				{
					$text = '(Unlimited)';
				}
				else
				{
					switch ($user->quota_type)
					{
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
			
			<td><?php echo mysqlhuman($user->lastlogin, "d/m/Y H:i") ?>&nbsp;</td>
			
			<td><?php echo ($user->ldap == 1) ? 'LDAP' : 'Local'; ?></td>
			
			<td>&nbsp;</td>
			
		</tr>
		
		<?php endforeach; ?>
		
	</tbody>
	
</table>