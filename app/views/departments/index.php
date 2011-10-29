<table class="list2 middle" summary="Department list" id="departments">
	
	<thead>
		<tr>
			<th scope="col" width="20">&nbsp;</th>
			<th scope="col">Department name</th>
			<th scope="col">Description</th>
			<?php if ($this->settings->get('auth_ldap_enable') == 1): ?>
				<th scope="col">LDAP groups</th>
			<?php endif; ?>
			<th scope="col">Actions</th>
		</tr>
	</thead>
	
	<tbody>
		
		<?php foreach($departments as $department): ?>
				
		<tr>
		
			<td align="center" width="20">
				<div style="width: 14px; height: 14px; background-color: <?php echo $department->colour ?>">&nbsp;</div>
			</td>
			
			<td class="title">
				<?php echo anchor(
					'departments/edit/' . $department->department_id, 
					$department->name . " ", 'rel="edit"')
				?>
				<span>
					<?php 
					$word = ($department->user_count == 1) ? 'person' : 'people';
					if ($department->user_count > 0)
					{
						echo $department->user_count . " $word";
					}
					else
					{
						echo 'No members';
					}
					?>
				</span>
			</td>
			
			<td>
				<?php echo $department->description ?>
			</td>
			
			<?php if ($this->settings->get('auth_ldap_enable') == 1): ?>
			<td>
				<?php echo $department->ldap_groups ?>
			</td>
			<?php endif; ?>
			
			<td class="actions">
				<a href="<?php echo site_url(sprintf('departments/delete/%d', $department->department_id)) ?>" 
					class="button red small">Delete</a>
			</td>
			
		</tr>
		
		<?php endforeach; ?>
		
	</tbody>
	
</table>