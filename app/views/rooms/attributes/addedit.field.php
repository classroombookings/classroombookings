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
	
	
	<tr>
		<td class="caption">
			<label for="type" class="r" accesskey="S"><u>T</u>ype</label>
		</td>
		<td class="field">
			<?php
			unset($input);
			$input['accesskey'] = 'S';
			$input['name'] = 'date_start';
			$input['id'] = 'date_start';
			$input['size'] = '15';
			$input['maxlength'] = '10';
			$input['tabindex'] = $t;
			$input['class'] = 'date';
			$input['value'] = @set_value($input['name'], $year->date_start);
			echo form_input($input);
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
	$buttons[] = array('submit', 'positive', $submittext, 'disk1.gif', $t);
	$buttons[] = array('cancel', 'negative', $this->lang->line('ACTION_CANCEL'), 'arr-left.gif', $t+2, site_url('rooms/attributes'));
	$this->load->view('parts/buttons', array('buttons' => $buttons));
	?>
	

</table>
</form>