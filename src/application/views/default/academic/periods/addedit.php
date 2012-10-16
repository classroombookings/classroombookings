<?php
$errors = validation_errors();
if($errors){
	echo $this->msg->err('<ul>' . $errors . '</ul>', 'Please check the following invalid item(s) and try again.');
}

echo form_open('academic/periods/save', NULL, array('period_id' => $period_id));

// Start tabindex
$t = 1;
?>

<table class="form" cellpadding="6" cellspacing="0" border="0" width="100%">
	
	<!-- <tr class="h"><td colspan="2">Period details</td></tr> -->
	
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
			$input['size'] = '20';
			$input['maxlength'] = '20';
			$input['tabindex'] = $t;
			$input['value'] = @set_value('name', $period->name);
			echo form_input($input);
			$t++;
			?>
		</td>
	</tr>
	
	
	<tr>
		<td class="caption">
			<label for="time_start" class="r" accesskey="S"><u>S</u>tart Time</label>
		</td>
		<td class="field">
			<?php
			unset($input);
			unset($val);
			$input['accesskey'] = 'S';
			$input['name'] = 'time_start';
			$input['id'] = 'time_start';
			$input['size'] = '10';
			$input['maxlength'] = '5';
			$input['tabindex'] = $t;
			$val = strtotime(@set_value($input['name'], $period->time_start));
			$input['value'] = ($val == FALSE) ? NULL : strftime('%H:%M', $val);
			echo form_input($input);
			$t++;
			?>
		</td>
	</tr>
	
	
	<tr>
		<td class="caption">
			<label for="time_end" class="r" accesskey="E"><u>E</u>nd Time</label>
		</td>
		<td class="field">
			<?php
			unset($input);
			unset($val);
			$input['accesskey'] = 'E';
			$input['name'] = 'time_end';
			$input['id'] = 'time_end';
			$input['size'] = '10';
			$input['maxlength'] = '5';
			$input['tabindex'] = $t;
			$val = strtotime(@set_value($input['name'], $period->time_end));
			$input['value'] = ($val == FALSE) ? NULL : strftime('%H:%M', $val);
			echo form_input($input);
			$t++;
			?>
		</td>
	</tr>
	


	
	
	<tr>
		<td class="caption">
			<label for="days" class="r" accesskey="D"><u>D</u>ays</label>
		</td>
		<td class="field">
			<?php
			foreach($days as $num => $name){
			?>
			<label for="day_<?php echo $num ?>" class="check">
			<?php
			unset($check);
			$check['name'] = 'days[]';
			$check['id'] = 'day_' . $num;
			$check['value'] = "$num";
			$check['checked'] = set_checkbox('days[]', $num, (@in_array($num, $period->days)));
			$check['tabindex'] = $t;
			echo form_checkbox($check);
			$t++;
			echo $name;
			?>
			</label>
			<?php
			}
			?>
		</td>
	</tr>
	
	
	<tr>
		<td class="caption">
			<label for="bookable" accesskey="B" title="Can lessons be booked for this period?">Can be <u>b</u>ooked</label>
		</td>
		<td class="field">
			<label for="bookable" class="check">
			<?php
			unset($check);
			$check['name'] = 'bookable';
			$check['id'] = 'bookable';
			$check['value'] = '1';
			$check['checked'] = @set_checkbox($check['name'], $check['value'], ($period->bookable == 1));
			$check['tabindex'] = $t;
			echo form_checkbox($check);
			$t++;
			?>Yes
			</label>
		</td>
	</tr>
	
	
	<?php
	if($period_id == NULL){
		$submittext = lang('ACTION_ADD') . ' ' . strtolower(lang('W_PERIOD'));
	} else {
		$submittext = lang('ACTION_SAVE') . ' ' . strtolower(lang('W_PERIOD'));
	}
	unset($buttons);
	$buttons[] = array('submit', 'ok', $submittext, $t);
	$buttons[] = array('submit', 'misc', 'Save and add another', $t+1);
	$buttons[] = array('link', 'cancel', lang('ACTION_CANCEL'), $t+2, site_url('academic/periods'));
	$this->load->view('parts/buttons', array('buttons' => $buttons));
	?>

</table>
</form>





<script type="text/javascript">
window.onload = function(){
	$("#time_start, #time_end").timePicker({
		step: 15,
		startTime:new Date(0, 0, 0, 7, 0, 0),
		endTime:new Date(0, 0, 0, 22, 0, 0)
	});
}

$("#time_end").change(function(){
	if($.timePicker("#time_start").getTime() > $.timePicker(this).getTime()){
		$(this).addClass("bg-red");
	} else {
		$(this).removeClass("bg-red");
	}
});
</script>