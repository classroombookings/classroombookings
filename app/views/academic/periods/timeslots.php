<?php
$errors = validation_errors();
if($errors){
	echo $this->msg->err('<ul>' . $errors . '</ul>', 'Please check the following invalid item(s) and try again.');
}

echo form_open('academic/periods/save_timeslots', NULL);

// Start tabindex
$t = 1;
?>

<div class="grey"><div>
<table class="form" cellpadding="6" cellspacing="0" border="0" width="100%">
	
	<tr>
		<td class="caption">
			<label for="start_time" class="r" accesskey="S">Day <u>s</u>tart time</label>
		</td>
		<td class="field">
			<?php
			unset($input);
			unset($val);
			$input['accesskey'] = 'S';
			$input['name'] = 'start_time';
			$input['id'] = 'start_time';
			$input['size'] = '10';
			$input['maxlength'] = '5';
			$input['tabindex'] = $t;
			$val = strtotime(@set_value($input['name'], $timeslots->start_time));
			$input['value'] = ($val == FALSE) ? NULL : strftime('%H:%M', $val);
			echo form_input($input);
			$t++;
			?>
		</td>
	</tr>
	
	
	<tr>
		<td class="caption">
			<label for="end_time" class="r" accesskey="E">Day <u>e</u>nd time</label>
		</td>
		<td class="field">
			<?php
			unset($input);
			unset($val);
			$input['accesskey'] = 'E';
			$input['name'] = 'end_time';
			$input['id'] = 'end_time';
			$input['size'] = '10';
			$input['maxlength'] = '5';
			$input['tabindex'] = $t;
			$val = strtotime(@set_value($input['name'], $timeslots->end_time));
			$input['value'] = ($val == FALSE) ? NULL : strftime('%H:%M', $val);
			echo form_input($input);
			$t++;
			?>
		</td>
	</tr>
	
	
	<tr>
		<td class="caption">
			<label for="interval" class="r" accesskey="I"><u>I</u>nterval</label>
		</td>
		<td class="field">
			<?php
			$times = array(1, 2, 3, 4, 5, 10, 15, 20, 25, 30, 35, 40, 45, 50, 55, 60);
			$measures = array(60 => 'minutes', 3600 => 'hours');
			?>
			
			<select name="interval[time]" id="interval[time]">
			<?php foreach($times as $time){
				$sel_time = ($time == $timeslots->interval['time']) ? ' selected="selected"' : '';
				printf('<option value="%1$d"%2$s>%1$d</option>', $time, $sel_time);
			} ?>
			</select>
			
			<select name="interval[measure]" id="interval[measure]">
			<?php foreach($measures as $s => $m){
				$sel_measure = ($s == $timeslots->interval['measure']) ? ' selected="selected"' : '';
				printf('<option value="%1$d"%3$s>%2$s</option>', $s, $m, $sel_measure);
			} ?>
			</select>
		</td>
	</tr>
	
	
	<?php
	unset($buttons);
	$buttons[] = array('submit', 'ok', 'Save', $t);
	$buttons[] = array('link', 'cancel', lang('ACTION_CANCEL'), $t+1, site_url('academic/periods'));
	$this->load->view('parts/buttons', array('buttons' => $buttons));
	?>

</table>
</form>
</div></div>