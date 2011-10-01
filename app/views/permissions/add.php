<?php
$errors = validation_errors();
if ($errors)
{
	echo '<div class="row">';
	echo $this->msg->err('<ul class="square">' . $errors . '</ul>', 'Please check the following invalid item(s) and try again.');
	echo '</div>';
}


echo form_open('permissions/save', null, array('permission_id' => $permission_id));

array_unshift($users, "Choose...");
array_unshift($groups, "Choose...");
array_unshift($departments, "Choose...");


// Start tabindex
$t = 1;
?>



<div class="alpha three columns"><h6>Entity type</h6></div>

<div class="omega nine columns">

	<fieldset>
	<?php
	$options = array(
		'e' => 'Everyone',
		'd' => 'Department',
		'g' => 'Group',
		'u' => 'User',
	);
	foreach($options as $k => $v)
	{
		echo '<label for="entity_type_' . $k . '">';
		unset($input);
		$input['name'] = 'entity_type';
		$input['id'] = 'entity_type_' . $k;
		$input['value'] = $k;
		$input['tabindex'] = $t;
		echo form_radio($input);
		echo '<span>' . $v . '</span>';
		echo '</label>';
	}
	?>
	</fieldset>
	
</div>

<br class="clear">



<!-- Department choice -->

<div id="entity_type_d" class="entity-type hidden">

	<div class="alpha three columns"><h6>Department</h6></div>

	<div class="omega nine columns">
		
		<?php echo form_dropdown('department_id', $departments) ?>
		
	</div>
	
</div>




<!-- Group choice -->

<div id="entity_type_g" class="entity-type hidden">

	<div class="alpha three columns"><h6>Group</h6></div>

	<div class="omega nine columns">

		<?php echo form_dropdown('group_id', $groups) ?>

	</div>

</div>




<!-- User choice -->

<div id="entity_type_u" class="entity-type hidden">

	<div class="alpha three columns"><h6>User</h6></div>

	<div class="omega nine columns">

		<?php echo form_dropdown('user_id', $users) ?>

	</div>

</div>





<hr class="remove-bottom">

<br>


<div id="permissions" class="hidden">

<?php // $this->load->view('permissions/list', null); ?>

perms

</div>





<!-- end of page -->

</form>




<script>
_jsQ.push(function(){
	
	$("input[name='entity_type']").change(function(){
		var type = $(this).val();
		var options = $("div#entity_type_" + type);
		var others = $("div.entity-type");
		others.hide();
		options.show();
	});
	
	$("select").change(function(){
		$("div#permissions:hidden").show();
	});
	
});
</script>