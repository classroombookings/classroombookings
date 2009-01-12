<?php
$errors = validation_errors();
if($errors){
	echo $this->msg->err('<ul>' . $errors . '</ul>', 'Please check the following invalid item(s) and try again.');
}

echo form_open('departments/save', NULL, array('department_id' => $department_id));

// Start tabindex
$t = 1;
?>

<table class="form" cellpadding="6" cellspacing="0" border="0" width="100%">
	
	<!-- <tr class="h"><td colspan="2">Department details</td></tr> -->
	
	<tr>
		<td class="caption">
			<label for="name" class="r" accesskey="N"><u>N</u>ame</label>
		</td>
		<td class="field">
			<?php
			unset($input);
			$input['accesskey'] = 'N';
			$input['name'] = 'name';
			$input['id'] = 'name';
			$input['size'] = '30';
			$input['maxlength'] = '64';
			$input['tabindex'] = $t;
			$input['value'] = @set_value('name', $department->name);
			echo form_input($input);
			$t++;
			?>
		</td>
	</tr>
	
	<tr>
		<td class="caption">
			<label for="description" class="r" accesskey="D"><u>D</u>escription</label>
		</td>
		<td class="field">
			<?php
			unset($input);
			$input['accesskey'] = 'D';
			$input['name'] = 'description';
			$input['id'] = 'description';
			$input['cols'] = '50';
			$input['rows'] = '4';
			$input['maxlength'] = '255';
			$input['tabindex'] = $t;
			$input['autocomplete'] = 'off';
			$input['value'] = @set_value($input['name'], $department->description);
			echo form_textarea($input);
			$t++;
			?>
		</td>
	</tr>
	
	<tr>
		<td class="caption">
			<label for="colour" accesskey="C"><u>C</u>olour</label>
		</td>
		<td class="field">
			<?php
			unset($input);
			$input['accesskey'] = 'C';
			$input['name'] = 'colour';
			$input['id'] = 'colour';
			$input['size'] = '10';
			$input['maxlength'] = '7';
			$input['tabindex'] = $t;
			$input['value'] = @set_value('colour', $department->colour);
			echo form_input($input);
			$t++;
			?>
			<div id="cp"></div>
			<script type="text/javascript"><!--
			$(document).ready(function(){
				$('#cp').colorPicker({
					activeColour: '<?php echo $input['value'] ?>',
					click: function(c){$('#colour').val(c);}
				});
				$('#colour').css("display", "none");
			});
			// --></script>
		</td>
	</tr>
	
	
	<?php if($this->settings->ldap() == TRUE){ ?>
	<tr>
		<td class="caption">
			<label for="ldapgroups" accesskey="L" title="Users who belong to the selected LDAP group(s) will be put in this department. "><u>L</u>DAP Groups</label>
		</td>
		<td class="field">
			<select name="ldapgroups[]" id="ldapgroups" size="20" tabindex="<?php echo $t ?>" multiple="multiple">
			<option value="-1">(None)</option>
			<?php
			foreach($ldapgroups as $id => $name){
				$selected = (@in_array($id, $department->ldapgroups)) ? ' selected="selected"' : '';
				echo sprintf('<option value="%1$d"%3$s>%2$s</option>', $id, $name, $selected);
			}
			$t++;
			?>
			</select>
		</td>
	</tr>
	<?php } ?>
	

	
	<?php
	if($department_id == NULL){
		$submittext = 'Add department';
	} else {
		$submittext = 'Save department';
	}
	unset($buttons);
	$buttons[] = array('submit', 'positive', $submittext, 'disk1.gif', $t);
	#$buttons[] = array('submit', '', 'Save and add another', 'add.gif', $t+1);
	$buttons[] = array('cancel', 'negative', 'Cancel', 'arr-left.gif', $t+2, site_url('departments'));
	$this->load->view('parts/buttons', array('buttons' => $buttons));
	?>

</table>
</form>




