<table class="grid permissions" border="1">

<?php foreach ($available_perms as $section_name => $section_perms): ?>

	<tr class="section-header">
		
		<td class="section-name" width="280"><?php echo $section_name ?></td>
		
		<?php $c = 0; ?>
		<?php foreach ($roles as $role): ?>
			<td class="role-name zc<?php echo ($c & 1) ?>" width="80" title="<?php echo $role->name ?>">
				<?php echo character_limiter($role->name, 2) ?>
			</td>
		<?php $c++; ?>
		<?php endforeach; ?>
	
	</tr>
	
	<?php foreach ($section_perms as $permission_id => $permission_name): ?>
	
		<tr class="permission-row">
			
			<td><?php echo $permission_name ?></td>
			
			<?php $c = 0; ?>
			<?php foreach ($roles as $role): ?>
				<td class="check zc<?php echo ($c & 1) ?>" title="<?php echo $role->name ?>">
					<input type="checkbox">
				</td>
			<?php $c++; ?>
			<?php endforeach; ?>
			
		</tr>
	
	<?php endforeach; ?>
	
	<tr>
		<td colspan="<?php echo 1 + count($roles) ?>">&nbsp;</td>
	</tr>

<?php endforeach; ?>

</table>


<input type="submit" class="blue button" value="Save all permissions">