<?php echo form_open(current_url(), array('id' => 'user_import_3_form')) ?>

	<div class="grid_12 form">
	
		<h3 class="sub-heading"><?php echo lang('users_import_preview') ?></h3>
		
		<table class="list users-preview">
			
			<thead>
				<tr>
					<?php
					foreach ($destination_fields as $field => $label)
					{
						if ($field === '')
						{
							echo '<th><input type="checkbox" id="chk_all"></th>';
							continue;
						}
						
						echo '<th>' . $label . '</th>';
					}
					?>
				</tr>
			</thead>
			
			<tbody>
				
				<?php $i = 0; ?>
				
				<?php foreach ($users as $user): ?>
				
				<tr>
					
					<?php
					foreach ($destination_fields as $field => $label)
					{
						echo '<td>';
						
						if ($field === '')
						{
							echo form_hidden('users[' . $i . '][import]', 0);
							echo '<input type="checkbox" id="chk_' . $user['u_username'] . '" name="users[' . $i . '][import]" value="1" class="user">';
							continue;
						}
						
						$value = element($field, $user);
						$display = $value;
						
						if ($field === 'u_g_id')
						{
							$display = element($value, $groups);
						}
						
						if ($field === 'd_id')
						{
							$display = element($value, $departments);
						}
						
						if ($field === 'u_enabled')
						{
							$display = ($value == 1) ? 'Y' : 'N';
						}
						
						echo '<label class="check" for="chk_' . $user['u_username'] . '">' . $display . '</label>';
						
						echo form_hidden('users[' . $i . '][' . $field . ']', $value);
						
						echo '</td>';
					}
					?>
					
				</tr>
				
				<?php $i++; ?>
				
				<?php endforeach; ?>
				
			</tbody>
			
		</table>
		
		<div class="row submit">
			<div class="grid_9 offset_3 text-right">
				<?php echo form_button(array(
					'type' => 'submit',
					'class' => 'blue right',
					'text' => lang('next'),
					'tab_index' => tab_index(),
				)) ?>
				<?php echo form_button(array(
					'type' => 'link',
					'url' => 'users/import/cancel',
					'class' => 'grey right',
					'text' => lang('cancel'),
					'tab_index' => tab_index(),
				)) ?>
			</div>
		</div>

	</div>

</form>


<script>
Q.push(function() {
	$("input#chk_all").on("change", function() {
		$("input.user").prop("checked", $(this).prop("checked"));
	});
});
</script>