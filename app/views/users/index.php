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


<table class="list2 middle" summary="User list" id="users">
	
	<thead>
		<tr>
			<th scope="col" width="20">&nbsp;</th>
			<th scope="col">User</th>
			<th scope="col"></th>
			<th scope="col">Quota</th>
			<th scope="col">Last login</th>
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
		
			<td align="center" width="20">
			<?php
			$src = ($user->ldap == 1) ? 'user-ldap.png' : 'database.png';
			$alt = ($user->ldap == 1) ? 'LDAP' : 'Local';
			echo '<img src="img/ico/' . $src . '" style="margin: 0;">';
			echo '<span style="display:none">' . $alt . '</span>';
			?>
			</td>
			
			<?php
			$classes = array();
			if ($user->online == 1) $classes[] = 'status online';
			if ($user->enabled == 0) $classes[] = 'status disabled';
			?>
			<td class="title <?php echo implode(' ', $classes) ?>">
				<?php echo anchor('users/edit/' . $user->user_id, $user->displayname . " ", 'rel="edit"') ?>
				<span><?php echo $user->groupname ?></span>
			</td>
			
			<td width="32" align="right">
				<?php
				$quota = rand(0, 10);
				$class = ($quota > 0) ? 'green' : 'red';
				echo '<span class="digit ' . $class . '">' . $quota . '</span>';
				?>
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
				echo "$text";
				?>
			</td>
			
			<td>
				<?php
				$date = mysqlhuman($user->lastlogin, "d/m/Y H:i");
				echo str_replace(date("d/m/Y"), "Today at", $date);
				?>&nbsp;
			</td>
			
			<td class="actions">
				<a href="<?php echo site_url(sprintf('users/delete/%d', $user->user_id)) ?>" class="button red small">Delete</a>
				<a href="#" class="button small">View events</a>
			</td>
			
		</tr>
		
		<?php endforeach; ?>
		
	</tbody>
	
</table>