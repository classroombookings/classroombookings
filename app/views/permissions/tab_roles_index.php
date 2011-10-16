<table class="list2 middle" summary="Role list" id="roles">

	<thead>
		<tr>
			<th scope="col" colspan="2">Move</th>
			<th scope="col">Role name</th>
			<th scope="col">Actions</th>
		</tr>
	</thead>
	
	<tbody>
	
		<?php if (!empty($roles)): ?>
	
		<?php foreach ($roles as $role): ?>
		
		<td class="one icon">
			<?php if ($role->weight > $weights['min']): ?>
				<img src="img/ico/arrup.png">
			<?php endif; ?>
		</td>
		<td class="one icon">
			<?php if ($role->weight < $weights['max']): ?>
				<img src="img/ico/arrdown.png">
			<?php endif; ?>
		</td>
		
		<td class="title">
			<?php
			echo $role->name;
			?>
		</td>
		
		<td class="actions">
			<?php
				echo anchor(sprintf('permissions/delete_role/%d', $role->role_id),	
					'Delete', 'class="small red button"') . " ";
			?>
		</td>
		
	</tr>
	
	<?php endforeach; ?>
	
	<?php endif; ?>
	
	<?php echo form_open('permissions/save_role') ?>
	<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>
			<?php
			unset($input);
			$input['accesskey'] = 'N';
			$input['name'] = 'name';
			$input['id'] = 'name';
			$input['size'] = '30';
			$input['maxlength'] = '20';
			$input['autocomplete'] = 'off';
			$input['class'] = 'remove-bottom';
			$input['value'] = @set_value($input['name'], $group->name);
			echo form_input($input);
			?>
		</td>
		<td class="actions">
			<input type="submit" value="Add role" class="small blue button">
		</td>
	</tr>
	</form>
	
	
	</tbody>
	
</table>