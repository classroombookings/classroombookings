<?php
$errors = validation_errors();
if($errors){
	echo $this->msg->err('<ul>' . $errors . '</ul>', 'Please check the following invalid item(s) and try again.');
}

echo form_open('security/groups/save', NULL, array('group_id' => $group_id));

// Start tabindex
$t = 1;
?>

<?php if($group_id == NULL){ ?>
<p>Please fill in the required fields below to add a new group to the system.</p>
<?php } ?>

<table class="form" cellpadding="6" cellspacing="0" border="0" width="50%">
	
	<tr class="h"><td colspan="2">Group details</td></tr>
	
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
			$input['autocomplete'] = 'off';
			$input['value'] = @set_value($input['name'], $group->name);
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
			$input['value'] = @set_value($input['name'], $group->description);
			echo form_textarea($input);
			$t++;
			?>
		</td>
	</tr>

	<tr class="h"><td colspan="2">Other options</td></tr>
	
	<tr>
		<td class="caption"><label for="daysahead" title="The number of days ahead users can create a booking. Leave blank to allow bookings at any time in the future.">Booking ahead</label></td>
		<td class="field">
			<?php
		  	unset($input);
			$input['name'] = 'bookahead';
			$input['id'] = 'bookahead';
			$input['size'] = '10';
			$input['maxlength'] = '3';
			$input['value'] = @set_value($input['name'], $group->bookahead);
			echo form_input($input);
			?>
		</td>
	</tr>
	
	<tr>
		<td class="caption"><label for="quota_num" title="The number of bookings a user in this group can make in a given period of time.">Booking quota</label></td>
		<td class="field">
			<?php
			unset($input);
			$input['name'] = 'quota_num';
			$input['id'] = 'quota_num';
			$input['size'] = '10';
			$input['maxlength'] = '3';
			$input['value'] = @set_value($input['name'], $group->quota_num);
			echo form_input($input);
			?>
			
			<label for="quota-unlimited" class="check" title="Can make any number of bookings.">
			<?php
			unset($radio);
			$radio['name'] = 'quota_type';
			$radio['id'] = 'quota-unlimited';
			$radio['value'] = 'unlimited';
			$radio['checked'] = @set_radio($radio['name'], $radio['value'], ($group->quota_type == NULL));
			echo form_radio($radio);
			?>Unlimited
			</label>
			
			<label for="quota-current" class="check" title="This means that a user can only have a given amount of bookings at one time, and must wait until their earliest one has passed until they can make another.">
			<?php
			unset($radio);
			$radio['name'] = 'quota_type';
			$radio['id'] = 'quota-current';
			$radio['value'] = 'current';
			$radio['checked'] = @set_radio($radio['name'], $radio['value'], ($group->quota_type == $radio['value']));
			echo form_radio($radio);
			?>Concurrent bookings
			</label>
			
			<label for="quota-day" class="check" title="A user can make this amount of bookings in a day, for any time in the future (up to and including) the day limit set above.">
			<?php
			unset($radio);
			$radio['name'] = 'quota_type';
			$radio['id'] = 'quota-day';
			$radio['value'] = 'day';
			$radio['checked'] = @set_radio($radio['name'], $radio['value'], ($group->quota_type == $radio['value']));
			echo form_radio($radio);
			?>Per day
			</label>
			
			<label for="quota-week" class="check" title="A user can make this amount of bookings in a week, for any time in the future (up to and including) the day limit set above.">
			<?php
			unset($radio);
			$radio['name'] = 'quota_type';
			$radio['id'] = 'quota-week';
			$radio['value'] = 'week';
			$radio['checked'] = @set_radio($radio['name'], $radio['value'], ($group->quota_type == $radio['value']));
			echo form_radio($radio);
			?>Per week
			</label>
			
			<label for="quota-month" class="check" title="A user can make this amount of bookings in a month, for any time in the future (up to and including) the day limit set above.">
			<?php
			unset($radio);
			$radio['name'] = 'quota_type';
			$radio['id'] = 'quota-month';
			$radio['value'] = 'month';
			$radio['checked'] = @set_radio($radio['name'], $radio['value'], ($group->quota_type == $radio['value']));
			echo form_radio($radio);
			?>Per month
			</label>
		</td>
	</tr>
	
	<?php
	if($group_id == NULL){
		$submittext = 'Add group';
	} else {
		$submittext = 'Save group';
	}
	unset($buttons);
	$buttons[] = array('submit', 'positive', $submittext, 'disk1.gif', $t);
	#$buttons[] = array('submit', '', 'Save and add another', 'add.gif', $t+1);
	$buttons[] = array('cancel', 'negative', 'Cancel', 'arr-left.gif', $t+2, site_url('security/groups'));
	$this->load->view('parts/buttons', array('buttons' => $buttons));
	?>

</table>
</form>
