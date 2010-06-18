<?php
$errors = validation_errors();
if($errors){
	echo $this->msg->err('<ul>' . $errors . '</ul>', $this->lang->line('FORM_ERRORS'));
}

echo form_open('rooms/manage/save_attrs', NULL, array('room_id' => $room_id));

// Start tabindex
$t = 1;

// Get room attribute values
#$values = $room->attrs;

foreach($room->attrs as $field){
	if($field->type == 'select'){
		$values[$field->field_id] = $field->option_id;
	} else {
		$values[$field->field_id] = $field->value;
	}
}

?>

<table class="form" cellpadding="6" cellspacing="0" border="0" width="100%">


	<?php
	#print_r($room['attrs']);
	
	/*
		Iterate through all the fields.
		For each field, load a custom view for that 'type' of field (pass the name/values as array)
	*/
	
	$t = 1;
	foreach($attrs as $attr){
		unset($data);
		$data['name'] = sprintf('fields[%d]', $attr->field_id);
		$data['attr'] = $attr;
		$data['t'] = $t; 
		$data['values'] = (isset($values)) ? $values : array();	//array();		// This needs to be a 2D array of the values for this room's fields
			// Like [field_id] = actual value
		switch($attr->type){
			case 'text':
				$this->load->view('rooms/attributes/field.text.php', $data);
			break;
			case 'select':
				$this->load->view('rooms/attributes/field.select.php', $data);
			break;
			case 'check':
				$this->load->view('rooms/attributes/field.check.php', $data);
			break;
		}
		$t++;
	}
	
	?>
	
	
	<?php
	$submittext = $this->lang->line('ACTION_SAVE') . ' ' . strtolower($this->lang->line('W_ATTRIBUTES'));
	unset($buttons);
	$buttons[] = array('submit', 'positive', $submittext, 'disk1.gif', $t);
	$buttons[] = array('cancel', 'negative', $this->lang->line('ACTION_CANCEL'), 'arr-left.gif', $t+2, site_url('rooms/manage'));
	$this->load->view('parts/buttons', array('buttons' => $buttons));
	?>
	
	
</table>
<?php echo form_close() ?>