<?php
$errors = validation_errors();
if($errors){
	echo $this->msg->err('<ul>' . $errors . '</ul>', 'Please check the following invalid item(s) and try again.');
}

// Check paths are writable
$paths = array();
if(!is_really_writable("temp")){
	array_push($paths, '<li>The ./temp directory is not writable.</li>');
}
if(!is_really_writable("web/upload")){
	array_push($paths, '<li>The ./web/upload directory is not writable.</li>');
}
if(count($paths) > 0){
	$path_err = $this->msg->warn('<ul>'.implode("<br />", $paths).'</ul>', 'Unable to upload photo');
}

echo form_open_multipart('rooms/save', NULL, array('room_id' => $room_id));

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
			<label for="name" accesskey="D" title="Enter a description of this room"><u>D</u>escription</label>
		</td>
		<td class="field">
			<?php
			unset($input);
			$input['accesskey'] = 'D';
			$input['name'] = 'description';
			$input['id'] = 'description';
			$input['size'] = '40';
			$input['maxlength'] = '50';
			$input['tabindex'] = $t;
			$input['value'] = @set_value($input['name'], $room->name);
			echo form_input($input);
			$t++;
			?>
		</td>
	</tr>
	
	<tr>
		<td class="caption">
			<label for="category" accesskey="C" title="Choose a category for this room to belong to or type a new one"><u>C</u>ategory</label>
		</td>
		<td class="field">
			<?php
			echo form_dropdown('category_id', $cats, set_value('category_id', (isset($room->category_id) ? $room->category_id : -1)), 'id="category_id" tabindex="'.$t.'"');
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
	
	<tr class="h"><td colspan="2">Room photo</td></tr>
	
	<?php if(isset($path_err)){ ?>
	
	<tr>
		<td colspan="2">
			<?php echo $path_err; ?>
		</td>
	</tr>
	
	<?php } else { ?>
	
	<tr>
		<td class="caption">
			<label for="name" accesskey="U" title="JPEG, PNG or GIF files accepted - uploaded images will be resized."><u>U</u>pload file</label>
		</td>
		<td class="field">
			<?php
			$rpupload = $this->session->userdata('rpupload');
			if(empty($rpupload)){
				unset($input);
				$input['accesskey'] = 'U';
				$input['name'] = 'userfile';
				$input['id'] = 'userfile';
				$input['size'] = '40';
				$input['maxlength'] = '1024';
				$input['tabindex'] = $t;
				$input['autocomplete'] = 'off';
				echo form_upload($input);
				$t++;
			} else {
				echo sprintf('<em>%s</em>', $rpupload['orig_name']);
			}
			?>
		</td>
	</tr>
	
	<?php } ?>
	
	<?php if(!empty($room->photo)){ ?>
	
	<tr>
		<td class="caption">
			<label>Current photo</label>
		</td>
		<td class="field">
			<?php
			echo $room->photo;
			?>
		</td>
	</tr>
	<tr>
		<td class="caption">
			<label for="delete" accesskey="D" title="Tick this box to delete the current photo (not necessary when uploading a new one)."><u>D</u>elete</label>
		</td>
		<td class="field">
			<label for="delete" class="check">
			<?php
			unset($check);
			$check['name'] = 'delete';
			$check['id'] = 'delete';
			$check['value'] = '1';
			$check['checked'] = FALSE;
			$check['tabindex'] = $t;
			echo form_checkbox($check);
			$t++;
			?>
			</label>
		</td>
	</tr>
	
	<?php } ?>
	
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

<script type="text/javascript">
$("#category_id").change(function(){
	if(this.value == -2){
		var newcat = prompt("Enter a name for the new category you wish to add.");
		if(newcat != 0){
			$("#category_id").append(
				$(document.createElement("option")).attr("selected","selected").attr("value", newcat).text(newcat)
			);
		}
	}
});
</script>