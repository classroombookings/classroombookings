<?php
// Lookups
$icocls['U'] = 'configure-users';
$icocls['G'] = 'configure-groups';
$icocls['D'] = 'configure-departments';
$fullname['U'] = 'user';
$fullname['G'] = 'group';
$fullname['D'] = 'department';
?>

<?php foreach($assignments as $role_id => $entities): ?>

	<div class="alpha three columns"><h6><?php echo $roles[$role_id] ?></h6></div>

	<div class="omega six columns">
		<ul class="add-bottom">
		<?php foreach($entities as $e): ?>
			<?php
			$confirmtext = "Are you sure you want to unassign the '%s' role from the %s '%s'?";
			$confirmtext = sprintf($confirmtext, 
				$roles[$role_id], $fullname[$e->entity_type], $e->name);
			?>
			<li class="add-half role-assignment i <?php echo $icocls[$e->entity_type] ?>">
				<strong><?php echo $e->name ?></strong> 
				<span class="hint" style="margin: 0 5px;"><?php echo $fullname[$e->entity_type] ?></span>
				<button rel="#unassign-role" 
					class="small red button unassign" style="float:right"
					data-roleid="<?php echo $role_id ?>" 
					data-rolename="<?php echo $roles[$role_id] ?>"
					data-entityid="<?php echo $e->entity_id ?>"
					data-confirm="<?php echo $confirmtext ?>"
					data-entitytype="<?php echo $e->entity_type ?>">Unassign</button>
			</li>
		<?php endforeach; ?>
		</ul>
	</div>
	
	<hr class="add-bottom">

<?php endforeach; ?>



<!-- Dialog box for unassigning a role -->

<div class="hidden dialog" id="unassign-role">

	<h2>Unassign role</h2>

	<?php
	echo form_open('permissions/unassign_role');
	?>

	<input type="hidden" name="role_id" value="">
	<input type="hidden" name="entity_id" value="">
	<input type="hidden" name="entity_type" value="">

	<p class="add-bottom"></p>
	
	<input type="submit" class="blue button remove-bottom" value="Yes">
	<button class="grey button close remove-bottom" type="reset">No</button>

	</form>

</div>



<script>
_jsQ.push(function(){
	
	var unassign_triggers = $("button.unassign").overlay({
		closeOnClick: true,
		closeOnEsc: true,
		top: '20%',
		oneInstance: true,
		speed: 'fast',
		onBeforeLoad: function(e){
			var o = this.getOverlay();
			var trigel = this.getTrigger();
			var role_id = trigel.data("roleid");
			var role_name = trigel.data("rolename");
			var entity_id = trigel.data("entityid");
			var entity_type = trigel.data("entitytype");
			var confirm_text = trigel.data("confirm");
			o.find("h2").text("Unassign role: " + role_name);
			o.find("p").text(confirm_text);
			o.find("input[name='role_id']").val(role_id);
			o.find("input[name='entity_id']").val(entity_id);
			o.find("input[name='entity_type']").val(entity_type);
		}
	});
	
});
</script>