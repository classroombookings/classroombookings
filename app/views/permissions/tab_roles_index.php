<?php if (!empty($roles)): ?>

<table class="list2 middle" summary="Role list" id="roles">

	<thead>
		<tr>
			<th scope="col">Role name</th>
			<th scope="col">Description</th>
			<th scope="col">Actions</th>
		</tr>
	</thead>
	
	<tbody>
	
		<?php foreach ($roles as $role): ?>
		
		<td class="title">
			<?php
			echo anchor('permissions/edit_role/' . $role->role_id, $role->name . " ", 'rel="edit" class="' . $title . '"');
			?>
		</td>
		
		<td width="100" align="left">
			<?php echo $role->description; ?>&nbsp;
		</td>
		
		<td class="actions">
			<?php
				echo anchor(sprintf('permissions/delete_role/%d', $role->role_id),	
					'Delete', 'class="small red button"') . " ";
			?>
		</td>
		
	</tr>
	
	<?php endforeach; ?>
	
	</tbody>
	
</table>

<?php else: ?>

	<p>No roles defined!</p>
	
<?php endif; ?>