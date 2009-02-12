<?php
$t = 0;
?>
<!-- Current permissions -->

<table class="list" width="100%" cellpadding="0" cellspacing="0" border="0">
	<col /><col /><col />
	<thead>
	<tr class="heading">
		<td class="h" title="Who">Who</td>
		<td class="h" title="Permissions">Permissions</td>
		<td class="h" title="X">&nbsp;</td>
	</tr>
	</thead>
	<tbody>
	
	<?php
	if(!empty($room->permissions)){
		foreach($room->permissions as $permission){
			?>
			<tr>
				<td class="x" valign="top">Everyone</td>
				<td class="x" valign="top"><p>Make a single booking</p><p>Make recurring bookings</p></td>
				<td class="il">
				<?php
				unset($actiondata);
				$actiondata[] = array('rooms/delete/'.$room->room_id, 'Delete', 'cross_sm.gif' );
				$this->load->view('parts/listactions', $actiondata);
				?>	
				</td>
			</tr>
			<?php
			$i++;
		}
	} else {
		?>
		<tr>
			<td colspan="3">No permission entries have been created yet.</td>
		</tr>
		<?php
	}
	?>
	
	</tbody>
	
</table>




<!-- Add a new permission entry -->

<table class="form" cellpadding="6" cellspacing="0" border="0" width="100%">
	
	<tr class="h"><td colspan="5">Add permission entry</td></tr>
	
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