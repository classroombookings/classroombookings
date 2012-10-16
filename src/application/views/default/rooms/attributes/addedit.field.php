<?php
$errors = validation_errors();
if($errors){
	echo $this->msg->err('<ul>' . $errors . '</ul>', $this->lang->line('FORM_ERRORS'));
}

echo form_open('rooms/attributes/save', NULL, array('field_id' => $field_id));

// Start tabindex
$t = 1;
?>

<table class="form" cellpadding="6" cellspacing="0" border="0" width="100%">
	
	
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
			$input['maxlength'] = '20';
			$input['tabindex'] = $t;
			$input['value'] = @set_value('name', $field->name);
			echo form_input($input);
			$t++;
			?>
		</td>
	</tr>
	
	
	<tr<?php echo (isset($field->type)) ? ' style="display:none"' : ''; ?>>
		<td class="caption">
			<label for="type" class="r" accesskey="T"><u>T</u>ype</label>
		</td>
		<td class="field">
			<?php
			$js = 'onchange="toggleoptions();"';
			echo form_dropdown('type', $fieldtypes, set_value('type', isset($field->type) ? $field->type : 'text'), 'id="type" tabindex="'.$t.'" '.$js.'');
			$t++;
			?>
		</td>
	</tr>
	
	
	<tr id="field-options">
		<td class="caption">
			<label for="options" accesskey="O" title="Enter possible options for the drop-down list, separated by new lines or commas."><u>O</u>ptions</label>
		</td>
		<td class="field">
			<?php
			unset($input);
			$input['accesskey'] = 'O';
			$input['name'] = 'options';
			$input['id'] = 'options';
			$input['cols'] = '40';
			$input['rows'] = '6';
			$input['maxlength'] = '255';
			$input['tabindex'] = $t;
			$input['autocomplete'] = 'off';
			$input['value'] = @set_value($input['name'], implode("\n", $field->options));
			echo form_textarea($input);
			$t++;
			?>
		</td>
	</tr>
	
	
	<?php
	if($field_id == NULL){
		$submittext = $this->lang->line('ACTION_ADD') . ' ' . strtolower($this->lang->line('W_FIELD'));
	} else {
		$submittext = $this->lang->line('ACTION_SAVE') . ' ' . strtolower($this->lang->line('W_FIELD'));
	}
	unset($buttons);
	$buttons[] = array('submit', 'ok', $submittext, $t);
	$buttons[] = array('link', 'cancel', $this->lang->line('ACTION_CANCEL'), $t+1, site_url('rooms/attributes'));
	$this->load->view('parts/buttons', array('buttons' => $buttons));
	?>
	

</table>
</form>


<script type="text/javascript">
// Show the options table row if the type is a drop-down
function toggleoptions(){
	if($('#type').val() == 'select'){
		$('#field-options').show();
	} else {
		$('#field-options').hide();
	}
}
toggleoptions();
</script>