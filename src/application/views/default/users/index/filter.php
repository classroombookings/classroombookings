<form method="GET" action="<?php echo current_url() ?>">

	<div class="filter-wrapper">
	
		<table class="filter">
			<tr>
				<td><label for="u_username">Username</label></td>
				<td><label for="u_email">Email</label></td>
				<td><label for="u_g_id">Group</label></td>
				<td><label for="u_status">Status</label></td>
				<td></td>
			</tr>
			<tr>
				<td>
					<?php echo form_input(array(
						'name' => 'u_username',
						'value' => element('u_username', $filter),
						'class' => 'text-input',
					)) ?>
				</td>
				<td>
					<?php echo form_input(array(
						'name' => 'u_email',
						'value' => element('u_email', $filter),
						'class' => 'text-input',
					)) ?>
				</td>
				<td>
					<?php
					$groups_dropdown = array('' => '(Any)');
					$groups_dropdown += $groups;
					echo form_dropdown('u_g_id', $groups_dropdown, element('u_g_id', $filter, ''), 'class="text-input"');
					?>
				</td>
				<td>
					<?php
					$types = array(
						'' => '(Any)',
						'1' => 'Enabled',
						'0' => 'Disabled',
					);
					echo form_dropdown('u_enabled', $types, element('u_enabled', $filter, ''), 'class="text-input"');
					?>
				</td>
				
				<td class="text-right">
					<input type="submit" class="black button" value="Filter">
				</td>
				
			</tr>
			
		</table>
	
	</div>

</form>


