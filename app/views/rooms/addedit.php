<?php
$errors = validation_errors();
if($errors){
	echo $this->msg->err('<ul>' . $errors . '</ul>', 'Please check the following invalid item(s) and try again.');
}

echo form_open('rooms/save', NULL, array('room_id' => $room_id));

// Start tabindex
$t = 1;
?>

<table class="form" cellpadding="6" cellspacing="0" border="0" width="100%">
	
	<tr class="h"><td colspan="2">Room information</td></tr>
	
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
			$input['value'] = @set_value($input['name'], $room->name);
			echo form_input($input);
			$t++;
			?>
		</td>
	</tr>
	
	<tr>
		<td class="caption">
			<label for="user_id" accesskey="O" title="Select the owner for this room from the list of users"><u>O</u>wner</label>
		</td>
		<td class="field">
			<?php
			echo form_dropdown('user_id', $users, set_value('user_id', (isset($room->user_id) ? $room->user_id : -1)), 'tabindex="'.$t.'"');
			$t++;
			?>
		</td>
	</tr>
	
	<tr>
		<td class="caption">
			<label for="bookable" accesskey="B" title="Untick this box to prevent the room from showing the bookings list and to prevent bookings."><u>B</u>ookable</label>
		</td>
		<td class="field">
			<label for="bookable" class="check">
			<?php
			unset($check);
			$check['name'] = 'bookable';
			$check['id'] = 'bookable';
			$check['value'] = '1';
			$check['checked'] = @set_checkbox($check['name'], $check['value'], ($room->bookable == 1));
			$check['tabindex'] = $t;
			echo form_checkbox($check);
			$t++;
			?>
			</label>
		</td>
	</tr>
	
	<?php
	if($room_id == NULL){
		$submittext = 'Add room';
	} else {
		$submittext = 'Save room';
	}
	unset($buttons);
	$buttons[] = array('submit', 'positive', $submittext, 'disk1.gif', $t);
	#$buttons[] = array('submit', '', 'Save and add another', 'add.gif', $t+1);
	$buttons[] = array('cancel', 'negative', 'Cancel', 'arr-left.gif', $t+2, site_url('rooms'));
	$this->load->view('parts/buttons', array('buttons' => $buttons));
	?>

</table>
</form>
