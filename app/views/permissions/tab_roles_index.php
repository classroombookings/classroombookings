<?php
$errors = validation_errors();
if ($errors)
{
	echo '<div class="row">';
	echo $this->msg->err('<ul class="square">' . $errors . '</ul>', 'Please check the following invalid item(s) and try again.');
	echo '</div>';
}
?>



<!-- Main listing of roles -->

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
		
	<tr class="role" data-roleid="<?php echo $role->role_id ?>">
		
		<td class="one icon up" style="padding:0;">
			<img src="img/ico/arrup.png" class="move" data-direction="up">
		</td>
		<td class="one icon down" style="padding:0;">
			<img src="img/ico/arrdown.png" class="move" data-direction="down">
		</td>
		
		<td class="title">
			<?php
			echo $role->name;
			?>
		</td>
		
		<td class="actions">
			<button class="small green button assign" rel="#assign-role" 
				data-id="<?php echo $role->role_id ?>" 
				data-name="<?php echo $role->name ?>">Assign</button>
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



<?php
echo form_open('permissions/save_role_order', array('name' => 'role_order'));
foreach ($roles as $role){
	echo form_hidden("role[{$role->role_id}]", $role->weight);
}
echo form_close();
?>




<!-- Dialog box for assigning a role -->

<div class="hidden dialog" id="assign-role">
	
	<h2>Assign role</h2>
	
	<?php
	echo form_open('permissions/assign_role');
	?>
	
	<input type="hidden" name="role_id" value="">
	
	<div class="alpha two columns"><h6>To a ...</h6></div>

	<div class="omega four columns">
	
		<fieldset>
		<?php
		$options = array(
			'D' => 'Department',
			'G' => 'Group',
			'U' => 'User',
		);
		foreach($options as $k => $v)
		{
			echo '<label for="entity_type_' . $k . '">';
			unset($input);
			$input['name'] = 'entity_type';
			$input['id'] = 'entity_type_' . $k;
			$input['value'] = $k;
			echo form_radio($input);
			echo '<span>' . $v . '</span>';
			echo '</label>';
		}
		?>
		</fieldset>
		
	</div>
	
	<br class="clear">
	
	<!-- Department choice -->
	<div id="entity_type_D" class="entity-type hidden">
		<div class="alpha two columns"><h6>Department</h6></div>
		<div class="omega four columns">
			<?php echo form_dropdown('department_id', $departments) ?>
		</div>
	</div>

	<!-- Group choice -->
	<div id="entity_type_G" class="entity-type hidden">
		<div class="alpha two columns"><h6>Group</h6></div>
		<div class="omega four columns">
			<?php echo form_dropdown('group_id', $groups) ?>
		</div>
	</div>

	<!-- User choice -->
	<div id="entity_type_U" class="entity-type hidden">
		<div class="alpha two columns"><h6>User</h6></div>
		<div class="omega four columns">
			<?php echo form_dropdown('user_id', $users) ?>
		</div>
	</div>
	
	<hr class="remove-bottom"><br>
	
	<div class="alpha two columns">&nbsp;</div>
	
	<div class="omega four columns">
		<input type="submit" class="blue button" value="Assign">
		<button class="grey button close" type="reset">Cancel</button>
	</div>
	
</div>




<script>
_jsQ.push(function(){
	
	$("table#roles tr.role:first td.up img").hide();
	$("table#roles tr.role:last td.down img").hide();
	
	// Attach overlay trigger to assign buttons
	var triggers = $("button.assign").overlay({
		closeOnClick: true,
		closeOnEsc: true,
		top: '20%',
		oneInstance: true,
		speed: 'fast',
		onBeforeLoad: function(e){
			var o = this.getOverlay();
			var trigel = this.getTrigger();
			var id = trigel.data("id");
			var name = trigel.data("name");
			o.find("h2").text("Assign role: " + name);
			o.find("input[name='role_id']").val(id);
			o.find("div.entity-type").hide();
		}
	});
	
	// Change dropdown box when selecting type
	$("input[name='entity_type']").change(function(){
		var type = $(this).val();
		var options = $("div#entity_type_" + type);
		var others = $("div.entity-type");
		others.hide();
		options.css("display", "block").show();
	});
	
	$("img.move").click(function(){
		// Get direction to move in
		var direction = $(this).data("direction");
		// Get the row that is being moved
		var row = $(this).closest("tr.role");
		// Move it appropriately
		if (direction == "up"){
			row.insertBefore(row.prev());
		} else {
			row.insertAfter(row.next());
		}
		// Show all move up/down arrow images
		var tablebody = $(this).closest("tbody");
		tablebody.find("tr.role img.move").show();
		// But hide the relevant up/down arrow at the top/bottom of the table
		tablebody.find("tr.role:first td.up img").hide();
		tablebody.find("tr.role:last td.down img").hide();
		// Update the hidden form values
		var data = {};
		tablebody.find("tr.role").each(function(x){
			var weight = x+1;
			var role_id = $(this).data("roleid");
			data["role[" + role_id + "]"] = weight;
			console.log(role_id + ": " + weight);
			/* var form = $("form[name=role_order]");
			form.find("input[name=role\\[" + role_id + "\\]]").val(weight);
			form.ajaxSubmit(); */
		});
		$.post(siteurl + "permissions/save_role_order", data);
		
	}).css("cursor", "pointer");
	
});
</script>