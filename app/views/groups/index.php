<table class="list2 middle" summary="Group list" id="groups">

	<thead>
		<tr>
			<th scope="col">Group name</th>
			<th scope="col"># Users</th>
			<th scope="col">Actions</th>
		</tr>
	</thead>
	
	<tbody>
	
		<?php foreach ($groups as $group): ?>
		
		<td class="title">
			<?php
			$title = $group->description;
			echo anchor('groups/edit/' . $group->group_id, $group->name . " ", 'rel="edit" class="' . $title . '"');
			?>
		</td>
		
		<td width="100" align="left">
			<?php echo $group->usercount ?>&nbsp;
		</td>
		
		<td class="actions">
			<?php
				echo anchor(sprintf('groups/delete/%d', $group->group_id),	
					'Delete', 'class="small red button"') . " ";
				echo anchor(sprintf('users/ingroup/%d', $group->group_id),
					'View users', 'class="small button"') . " ";
				echo anchor(sprintf('permissions/view/%d', $group->group_id),
					'View permissions', 'class="small button"') . " ";
			?>
		</td>
		
	</tr>
	
	<?php endforeach; ?>
	
	</tbody>
	
</table>