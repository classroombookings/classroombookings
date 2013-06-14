<ul class="role-list sortable handles grid_9">

<?php foreach ($roles as $role): ?>

	<li class="role-row row" data-r_id="<?php echo $role['r_id'] ?>">
		
		<div class="grid_3 role-title">
			<h6 class="heading remove-bottom"><img src="img/ico/arrow_ns.png" class="handle"><?php echo $role['r_name'] ?></h6>
		</div>
		
		<div class="grid_6 role-members">
			
			<input type="text" placeholder="Search for a user, group or department..." class="autocomplete" data-r_id="<?php echo $role['r_id'] ?>">
			
			<ul class="role-assigned" data-r_id="<?php echo $role['r_id'] ?>">
				<?php
				if ( ! empty($role['assigned']['all']))
				{
					foreach ($role['assigned']['all'] as $entity)
					{
						echo '<li data-e_type="' . $entity['e_type'] . '" data-e_id="' . $entity['e_id'] . '">';
						echo '<span class="role-entity-name">' . $entity['name'] . '</span> ';
						echo '<span class="role-entity-type">' . lang('roles_entity_type_' . $entity['e_type']) . '</span> ';
						echo '<span class="role-entity-remove">' . anchor('#', '&times;') . '</span> ';
						echo '</li>';
					}
				}
				?>
			</ul>
			
			<div class="clear"></div>
			
		</div>
		
	</li>

<?php endforeach; ?>

</ul>

<div class="grid_3">
<div class="hint">
	<h6>Roles</h6>
	<p>Each user's permissions are added to them through the roles they are assigned to.</p>
	<p>Other settings attached to roles are set explicity for that role only. Roles higher up in the list over-ride ones further down.</p> 
</div>
</div>


	
<!-- Javascript template for new entities that get added -->
<script id="role_entity" type="text/html">
<li data-e_id="{{e_id}}" data-e_type="{{e_type}}">
	<span class="role-entity-name">{{e_name}}</span>
	<span class="role-entity-type">{{e_type_lang}}</span>
	<span class="role-entity-remove"><a href="#">&times;</a></span>
</li>
</script>


<?php /*
<div class="grid_12">

	<table class="grid list roles">
		
		<thead>
			<th style="width: 20px;"></th>
			<th>&nbsp;</th>
			<th style="width: 22%">Users</th>
			<th style="width: 22%">Groups</th>
			<th style="width: 22%">Departments</th>
		</thead>
		
		<tbody>
			
			<?php foreach ($roles as $role): ?>
			
			<tr class="vat <?php echo alternator('odd', 'even') ?>">
				
				<td></td>
				
				<td class="title"><?php echo anchor('roles/set/' . $role['r_id'], $role['r_name']) ?></td>
				
				<!-- Users -->
				<td>
					
					<ul class="role-assigned">
						<?php
						if (isset($role['assigned']['U']))
						{
							foreach ($role['assigned']['U'] as $user)
							{
								echo '<li>' . anchor('users/set/' . $user['e_id'], $user['name']) . '</li>';
							}
						}
						else
						{
							echo '<li>&nbsp;</li>';
						}
						?>
					</ul>
					<?php echo role_assign_button($role, 'U'); ?>
				</td>
				
				<!-- Groups -->
				<td>
					
					<ul class="role-assigned">
						<?php
						if (isset($role['assigned']['G']))
						{
							foreach ($role['assigned']['G'] as $group)
							{
								echo '<li>' . anchor('groups/set/' . $group['e_id'], $group['name']) . '</li>';
							}
						}
						else
						{
							echo '<li>&nbsp;</li>';
						}
						?>
					</ul>
					<?php echo role_assign_button($role, 'G'); ?>
				</td>
				
				<!-- Departments -->
				<td>
					
					<ul class="role-assigned">
						<?php
						if (isset($role['assigned']['D']))
						{
							foreach ($role['assigned']['D'] as $department)
							{
								echo '<li>' . anchor('departments/set/' . $department['e_id'], $department['name']) . '</li>';
							}
						}
						else
						{
							echo '<li>&nbsp;</li>';
						}
						?>
					</ul>
					<?php echo role_assign_button($role, 'D'); ?>
				</td>
			
			</tr>
			
			<?php endforeach; ?>
			
		</tbody>
		
	</table>
	
</div>
*/ ?>
