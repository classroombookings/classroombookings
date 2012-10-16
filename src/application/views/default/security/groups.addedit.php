<?php
$errors = validation_errors();
if($errors){
	echo $this->msg->err('<ul>' . $errors . '</ul>', 'Please check the following invalid item(s) and try again.');
}

echo form_open('security/groups/save', NULL, array('group_id' => $group_id));

// Start tabindex
$t = 1;
?>

<div class="grey"><div>
<table class="form" width="100%">
	
	<tr class="h"><td colspan="2"><div>Group details</div></td></tr>
	
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
	
	<?php if($this->settings->ldap() == TRUE){ ?>
	<tr>
		<td class="caption">
			<label for="ldapgroups" class="r" accesskey="L"><u>L</u>DAP Groups</label>
			<p class="tip">Users who belong to the selected LDAP group(s) will be put in this classroombookings group.<br />The selected group(s) will not be available to assign to other groups, becuase a user can only be a member of one group.</p>
		</td>
		<td class="field">
			<select name="ldapgroups[]" id="ldapgroups" size="20" tabindex="<?php echo $t ?>" multiple="multiple">
			<option value="-1">(None)</option>
			<?php
			foreach($ldapgroups as $id => $name){
				$selected = (in_array($id, $group->ldapgroups)) ? ' selected="selected"' : '';
				echo sprintf('<option value="%1$d"%3$s>%2$s</option>', $id, $name, $selected);
			}
			$t++;
			?>
			</select>
			
		</td>
	</tr>
	<?php } ?>
</table>
</div></div>

<div class="grey"><div>
<table class="form" width="100%">
	
	<tr class="h"><td colspan="2"><div>Other options</div></td></tr>
	
	<tr>
		<td class="caption">
			<label for="daysahead">Booking ahead</label>
			<p class="tip">The number of days ahead users can create a booking. Leave blank to allow bookings at any time in the future.</p>
		</td>
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
		<td class="caption">
			<label for="quota_num">Booking quota</label>
			<p class="tip">The number of bookings a user in this group can make in a given period of time.</p>
		</td>
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
			
			<label for="quota-unlimited" class="check">
			<?php
			unset($radio);
			$radio['name'] = 'quota_type';
			$radio['id'] = 'quota-unlimited';
			$radio['value'] = 'unlimited';
			$radio['checked'] = @set_radio($radio['name'], $radio['value'], ($group->quota_type == NULL));
			echo form_radio($radio);
			?>Unlimited
			</label>
			
			<label for="quota-current" class="check">
			<?php
			unset($radio);
			$radio['name'] = 'quota_type';
			$radio['id'] = 'quota-current';
			$radio['value'] = 'current';
			$radio['checked'] = @set_radio($radio['name'], $radio['value'], ($group->quota_type == $radio['value']));
			echo form_radio($radio);
			?>Concurrent bookings
			</label>
			
			<label for="quota-day" class="check">
			<?php
			unset($radio);
			$radio['name'] = 'quota_type';
			$radio['id'] = 'quota-day';
			$radio['value'] = 'day';
			$radio['checked'] = @set_radio($radio['name'], $radio['value'], ($group->quota_type == $radio['value']));
			echo form_radio($radio);
			?>Per day
			</label>
			
			<label for="quota-week" class="check">
			<?php
			unset($radio);
			$radio['name'] = 'quota_type';
			$radio['id'] = 'quota-week';
			$radio['value'] = 'week';
			$radio['checked'] = @set_radio($radio['name'], $radio['value'], ($group->quota_type == $radio['value']));
			echo form_radio($radio);
			?>Per week
			</label>
			
			<label for="quota-month" class="check">
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
</table>
</div></div>

<table class="form" width="100%">
	<?php
	if($group_id == NULL){
		$submittext = 'Add group';
	} else {
		$submittext = 'Save group';
	}
	unset($buttons);
	$buttons[] = array('submit', 'ok', $submittext, $t);
	#$buttons[] = array('submit', '', 'Save and add another', 'add.gif', $t+1);
	#$buttons[] = array('cancel', 'negative', 'Cancel', 'arr-left.gif', $t+2, site_url('security/groups'));
	$this->load->view('parts/buttons', array('buttons' => $buttons));
	?>
</table>


</form>