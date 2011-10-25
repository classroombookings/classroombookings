<?php echo form_open('permissions/save_permissions') ?>

<table class="grid permissions" border="1">

<?php foreach ($available_perms as $section_name => $section_perms): ?>

	<tr class="section-header">
		
		<td class="section-name" data-section="<?php echo $section_name ?>" width="280"><?php echo $section_name ?></td>
		
		<?php $c = 0; ?>
		<?php foreach ($roles as $role): ?>
			<td 
				class="role-name zc<?php echo ($c & 1) ?>" 
				width="80" 
				title="<?php echo $role->name ?>" 
				data-section="<?php echo $section_name ?>"
				data-roleid="<?php echo $role->role_id ?>">
				<?php echo character_limiter($role->name, 2) ?>
			</td>
		<?php $c++; ?>
		<?php endforeach; ?>
	
	</tr>
	
	<?php foreach ($section_perms as $permission_id => $permission_name): ?>
	
		<tr class="permission-row">
			
			<td class="permission-name section-<?php echo $section_name ?>"><?php echo $permission_name ?></td>
			
			<?php $c = 0; ?>
			<?php foreach ($roles as $role): ?>
				<td class="check zc<?php echo ($c & 1) ?>" title="<?php echo $role->name ?>">
					<?php $ref = sprintf('p_%d_%d', $role->role_id, $permission_id) ?>
					<input 
						class="tristate section-<?php echo $section_name ?> roleid-<?php echo $role->role_id ?> <?php echo $ref ?>"
						type="hidden" 
						name="permissions[<?php echo $role->role_id ?>][<?php echo $permission_id ?>]"
						data-ref="<?php echo $ref ?>"
						id="<?php echo $ref ?>"
						value="<?php echo $values[$role->role_id][$permission_id] ?>">
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

</form>



<script>
_jsQ.push(function(){
	
	$("input.tristate").cbtristate();
	
	$("td.check").click(function(){
		$(this).find("img.tristate-image").trigger("click");
	});
	
	// Permission name click: set whole row
	$("td.permission-name").click(function(){
		var row = $(this).closest("tr");
		var curval = row.data("curval");
		var nextval = get_next_value(curval);
		row.find("input[type=hidden]").val(nextval).trigger("change");
		row.data("curval", nextval);
	}).css("cursor", "pointer");
	
	// Section name clicking: set whole row for all sub permissions
	$("td.section-name").click(function(){
		var section_name = $(this).data("section");
		var curval = $(this).data("curval");
		var nextval = get_next_value(curval);
		var selector = "input.section-" + section_name;
		$(selector).val(nextval).trigger("change");
		$(this).data("curval", nextval);
	}).css("cursor", "pointer");
	
	$("td.role-name").click(function(e){
		var section_name = $(this).data("section");
		var role_id = $(this).data("roleid");
		var curval = $(this).data("curval");
		var nextval = get_next_value(curval);
		var selector = "input.section-" + section_name + ".roleid-" + role_id;
		$(selector).val(nextval).trigger("change");
		$(this).data("curval", nextval);
	}).css("cursor", "pointer");
	
	function get_next_value(curval){
		var nextval = null;
		if (curval == "1") {
			nextval = "0";
		} else if (curval == "0"){
			nextval = "";
		} else {
			nextval = "1";
		}
		return nextval;
	}
	
});
</script>