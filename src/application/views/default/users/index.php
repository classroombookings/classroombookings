<div class="grid_12">

	<?php $this->load->view('default/users/index/filter') ?>

	<?php if ($users): ?>

	<table class="list users" id="users">
		
		<thead>
			<th style="width: 20px"></th>
			<th>User</th>
			<th>Last login</th>
			<th></th>
		</thead>
		
		<tbody>
			
			<?php foreach ($users as $u): ?>
			
			<tr class="<?php echo user_classes($u) ?>">
				
				<td><?php echo user_auth_icon($u, 'tag') ?></td>
				
				<td class="title">
					<?php echo anchor('users/set/' . $u['u_id'], $u['u_username']) ?>
					<span class="user-group"><?php echo $u['g_name'] ?></span>
					<?php if ($u['u_enabled'] == 0) echo '<span class="orange label">disabled</span>'; ?>
				</td>
				
				<td><?php echo user_last_login($u, 'D j M Y H:i') ?></td>
				
				<td class="text-right"><?php echo user_delete($u) ?></td>
				
			</tr>
			
			<?php endforeach; ?>
			
		</tbody>
		
	</table>

	<?php else: ?>

	<p class="no-results">No users found.</p>
		
	<?php endif; ?>

</div>