<div class="grid_12">

	<?php echo form_open('roles/permissions') ?>

		<table class="grid permissions">

			<?php foreach ($available_perms as $section_name => $section_perms): ?>
			
			<tr class="section-header">
				
				<td class="section-name" data-section="<?php echo $section_name ?>" width="280">
					<h3 class="sub-heading remove-bottom"><?php echo lang('permissions_section_' . $section_name) ?></h3>
				</td>
				
				<?php foreach ($roles as $role): ?>
				
				<td class="role-name" title="<?php echo $role['r_name'] ?>">
					<label class="check">
						<?php echo form_checkbox(array(
							'name' => 'role_section',
							'class' => 'role-section',
							'data-section' => $section_name,
							'data-r_id' => $role['r_id'],
						));
						echo ' ' . $role['r_name'];
						?>
					</label>
				</td>
				
				<?php endforeach; ?>
			
			</tr>
			
			<?php foreach ($section_perms as $p_id => $p_name): ?>
			
			<tr class="permission-row">
				
				<td class="permission-name section-<?php echo $section_name ?>">
					<?php echo lang($p_name) ?>
				</td>
				
				<?php foreach ($roles as $role): ?>
				
				<?php
				$ref = sprintf('p_%d_%d', $role['r_id'], $p_id);
				$name = sprintf('permissions[%d][%d]', $role['r_id'], $p_id);
				$checked = (array_key_exists($p_id, $role_permissions[$role['r_id']]));
				?>
				
				<td class="check <?php echo ($checked ? 'checked' : 'unchecked') ?>" title="<?php echo $role['r_name'] ?>">
				
					<label class="check"><?php echo form_checkbox(array(
						'type' => 'checkbox',
						'class' => 'role-permission',
						'name' => $name,
						'id' => $ref,
						'value' => $p_id,
						'checked' => $checked,
						'data-section' => $section_name,
						'data-ref' => $ref,
						'data-r_id' => $role['r_id'],
						'data-p_id' => $p_id,
					)); ?>
					</label>
				</td>
				
				<?php endforeach; ?>
			
			</tr>
			
			<?php endforeach; ?>
			
			<tr>
				<td colspan="<?php echo 1 + count($roles) ?>">&nbsp;</td>
			</tr>

		<?php endforeach; ?>

		</table>

		<?php echo form_button(array(
			'type' => 'submit',
			'class' => 'primary',
			'text' => lang('save'),
			'tab_index' => tab_index(),
		)) ?>

	</form>

</div>


<script>
Q.push(function(){
	
	// Click anywhere in a TD to activate the checkbox
	$("table.permissions").on("click", "td.check", function(e) {
		var $input = $(e.currentTarget).find("input[type=checkbox]");
		$input.attr("checked", !$input.attr("checked")).trigger("change");
	});
	
	// Update the TD class based on checkbox's state
	$("table.permissions").on("change", "input", function(e) {
		var $td = $(this).closest("td");
		$td.removeClass("unchecked checked");
		var add_class = $(this).is(":checked") ? "checked" : "unchecked";
		$td.addClass(add_class);
	});
	
	// Checkbox at role level checks all boxes on the section for the role
	$("table.permissions").on("change", "input.role-section", function(e) {
		var checked = $(this).attr("checked");
		var section = $(this).data("section");
		var r_id = $(this).data("r_id");
		
		var $group_checks = $("input.role-permission[data-section='" + section + "'][data-r_id='" + r_id + "']");
		$group_checks.attr("checked", !!checked).trigger("change");
	});
	
});
</script>