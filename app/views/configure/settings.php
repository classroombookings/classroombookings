<?php
$errors = validation_errors();
if($errors){
	echo $this->msg->err('<ul class="square">' . $errors . '</ul>', 'Please check the following invalid item(s) and try again.');
}
?>

<?php
echo form_open('configure/save_settings');

// Start tabindex
$t = 1;
?>



<div class="alpha three columns"><h6><?php echo lang('W_SCHOOL') ?> <?php echo strtolower(lang('W_DETAILS')) ?></h6></div>

<div class="omega nine columns">

	<label for="school_name"><?php echo lang('W_SCHOOL') ?> <?php echo strtolower(lang('W_NAME')) ?></label>
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

	<label for="school_name"><?php echo lang('W_SCHOOL') ?> <?php echo strtolower(lang('WEBADDR')) ?></label>
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

<hr>

<div class="alpha three columns"><h6>Booking page</h6></div>

<div class="omega nine columns">

	<fieldset>
		<label for="">Timetable view</label>
		<label for="timetable_view_day">
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
			<span>One day at a time</span>
		</label>
		<label for="timetable_view_room">
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
			<span>One room at a time</span>
		</label>
	</fieldset>

	<fieldset>
		<label for="">Timetable column item</label>
		<label for="timetable_cols_periods">
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
			<span>Periods</span>
		</label>
		<label for="timetable_cols_days">
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
			<span>Days of the week</span>
		</label>
	</fieldset>

</div>


<hr>


<div class="alpha three columns">&nbsp;</div>
<div class="omega nine columns"><?php
unset($buttons);
$buttons[] = array('submit', 'blue', lang('ACTION_SAVE') . ' ' . strtolower(lang('W_SETTINGS')), $t);
$this->load->view('parts/buttons', array('buttons' => $buttons));
?></div>


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