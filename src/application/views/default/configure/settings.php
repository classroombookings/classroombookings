<?php
$errors = validation_errors();
if($errors){
	echo $this->msg->err('<ul class="square">' . $errors . '</ul>', 'Please check the following invalid item(s) and try again.');
}
?>


<?php
echo form_open('configure/save_settings', 'class="form-horizontal"');

// Start tabindex
$t = 1;
?>


<fieldset>
	
	<legend><?php echo lang('W_SCHOOL') ?> <?php echo strtolower(lang('W_DETAILS')) ?></legend>
	
	<div class="control-group">
		<label class="control-label" for="input01">
			<?php echo lang('W_SCHOOL') ?> <?php echo strtolower(lang('W_NAME')) ?>
		</label>
		<div class="controls">
			<?php
			unset($input);
			$input['accesskey'] = 'N';
			$input['name'] = 'school_name';
			$input['id'] = 'school_name';
			$input['size'] = '40';
			$input['maxlength'] = '100';
			$input['tabindex'] = $t;
			$input['value'] = set_value('school_name', $settings['school_name']);
			echo form_input($input);
			$t++;
			?>
		</div>
	</div>
	
	<div class="control-group">
		<label class="control-label" for="input01">
			<?php echo lang('W_SCHOOL') ?> <?php echo strtolower(lang('WEBADDR')) ?>
		</label>
		<div class="controls">
			<?php
			unset($input);
			$input['accesskey'] = 'N';
			$input['name'] = 'school_url';
			$input['id'] = 'school_url';
			$input['size'] = '60';
			$input['maxlength'] = '255';
			$input['tabindex'] = $t;
			$input['value'] = set_value('school_url', $settings['school_url']);
			echo form_input($input);
			$t++;
			?>
		</div>
	</div>
		
</fieldset>



<fieldset>
	
	<legend>Booking page</legend>
	
	<div class="control-group">
		<label class="control-label">Timetable view</label>
		<div class="controls">
			<label class="radio">
			<?php
			unset($input);
			$input['name'] = 'timetable_view';
			$input['id'] = 'timetable_view_day';
			$input['value'] = 'day';
			$input['checked'] = set_radio($input['name'], $input['value'], 
				($settings['timetable_view'] == $input['value']));
			$input['tabindex'] = $t;
			echo form_radio($input);
			$t++;
			?>
			One day at a time
		  </label>
			<label class="radio">
			<?php
			unset($input);
			$input['name'] = 'timetable_view';
			$input['id'] = 'timetable_view_room';
			$input['value'] = 'room';
			$input['checked'] = set_radio($input['name'], $input['value'], 
				($settings['timetable_view'] == $input['value']));
			$input['tabindex'] = $t;
			echo form_radio($input);
			$t++;
			?>
			One room at a time
		  </label>
		</div>
	</div>
	
	<div class="control-group">
		<label class="control-label">Timetable column item</label>
		<div class="controls">
			<label class="radio">
			<?php
			unset($input);
			$input['name'] = 'timetable_cols';
			$input['id'] = 'timetable_cols_periods';
			$input['value'] = 'periods';
			$input['checked'] = set_radio($input['name'], $input['value'], 
				($settings['timetable_cols'] == $input['value']));
			$input['tabindex'] = $t;
			echo form_radio($input);
			$t++;
			?>
			Periods
		  </label>
			<label class="radio">
			<?php
			unset($input);
			$input['name'] = 'timetable_cols';
			$input['id'] = 'timetable_cols_days';
			$input['value'] = 'days';
			$input['checked'] = set_radio($input['name'], $input['value'],
				($settings['timetable_cols'] == $input['value']));
			$input['tabindex'] = $t;
			echo form_radio($input);
			$t++;
			?>
			Days of the week
		  </label>
		</div>
	</div>
	
</fieldset>



<fieldset>
	<div class="form-actions">
		<button type="submit" class="btn btn-primary"><?php echo lang('ACTION_SAVE') . ' ' . strtolower(lang('W_SETTINGS')) ?></button>
		<button class="btn">Cancel</button>
	</div>
</fieldset>


</form>




<script type="text/javascript">
function tt_day(){
	$("#timetable_cols_periods").removeAttr("disabled");
	$("#timetable_cols_days").attr("disabled","disabled");
	$("#timetable_cols_rooms").attr("disabled","disabled");
	$("#timetable_cols_periods").attr("checked", "checked");
}
function tt_room(){
	$("#timetable_cols_periods").removeAttr("disabled");
	$("#timetable_cols_days").removeAttr("disabled");
	$("#timetable_cols_rooms").attr("disabled", "disabled");
}
_jsQ.push(function(){
	$("#timetable_view_day").bind("click", function(e){ tt_day(); });
	$("#timetable_view_room").bind("click", function(e){ tt_room(); });
	if($("#timetable_view_day").attr("checked")){ tt_day(); }
	if($("#timetable_view_room").attr("checked")){ tt_room(); }
});
</script>