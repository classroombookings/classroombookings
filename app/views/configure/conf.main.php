<?php
$errors = validation_errors();
if($errors){
	echo $this->msg->err('<ul>' . $errors . '</ul>', 'Please check the following invalid item(s) and try again.');
}
?>

<?php
echo form_open('configure/save_main');

// Start tabindex
$t = 1;
?>

<div class="grey"><div>
<table class="form">
	
	<tr class="h"><td colspan="2"><div>School Information</div></td></tr>
	
	<tr>
		<td class="caption">
			<label for="schoolname" class="r" accesskey="N" title="The school name will appear beneath the Classroombookings logo at the top of the page.">School name</label>
		</td>
		<td class="field">
		  <?php
			#$ = @field($this->validation->username);
			$schoolname['accesskey'] = 'N';
			$schoolname['name'] = 'school_name';
			$schoolname['id'] = 'school_name';
			$schoolname['size'] = '40';
			$schoolname['maxlength'] = '100';
			$schoolname['tabindex'] = $t;
			$schoolname['value'] = set_value('school_name', $settings['school.name']);
			echo form_input($schoolname);
			$t++;
			?>
		</td>
	</tr>
	
	<tr>
		<td class="caption">
			<label for="schoolurl" accesskey="W">Website address</label>
		</td>
		<td class="field">
		  <?php
			#$ = @field($this->validation->username);
			$schoolurl['accesskey'] = 'W';
			$schoolurl['name'] = 'school_url';
			$schoolurl['id'] = 'school_url';
			$schoolurl['size'] = '50';
			$schoolurl['maxlength'] = '100';
			$schoolurl['tabindex'] = $t;
			$schoolurl['value'] = set_value('school_url', $settings['school.url']);
			echo form_input($schoolurl);
			$t++;
			?>
		</td>
	</tr>
	
</table>
</div></div>


<div class="grey"><div>
<table class="form">
	
	<tr class="h"><td colspan="2"><div>Booking display settings</div></td></tr>
	
	<tr>
		<td class="caption">
			<label class="r" accesskey="D" title="This is the style in which the main booking table will be displayed.">Timetable view</label>
			<p class="tip">This is the style in which the main booking table will be displayed.</p>
		</td>
		<td class="field">
			<label for="timetable_view_day" class="check">
			<?php
			unset($check);
			$check['name'] = 'timetable_view';
			$check['id'] = 'timetable_view_day';
			$check['value'] = 'day';
			$check['checked'] = set_radio($check['name'], $check['value'], ($settings['timetable.view'] == $check['value']));
			$check['tabindex'] = $t;
			echo form_radio($check);
			$t++;
			?>One day at a time
			</label>
			<label for="timetable_view_room" class="check">
			<?php
			#$ = @field($this->validation->username);
			unset($check);
			$check['name'] = 'timetable_view';
			$check['id'] = 'timetable_view_room';
			$check['value'] = 'room';
			$check['checked'] = set_radio($check['name'], $check['value'], ($settings['timetable.view']	== $check['value']));
			$check['tabindex'] = $t;
			echo form_radio($check);
			$t++;
			?>One room at a time
			</label>
		</td>
	</tr>
	
	
	<tr>
		<td class="caption">
			<label class="r" accesskey="C" title="This controls what information is displayed in columns on your bookings table - the other option will be displayed in the rows.">Column item</label>
			<p class="tip">This controls what information is displayed in columns on your bookings table - the other option will be displayed in the rows.</p>
		</td>
		<td class="field">
			<label for="timetable_cols_periods" class="check">
			<?php
			unset($check);
			$check['name'] = 'timetable_cols';
			$check['id'] = 'timetable_cols_periods';
			$check['value'] = 'periods';
			$check['checked'] = set_radio($check['name'], $check['value'], ($settings['timetable.cols'] == $check['value']));
			$check['tabindex'] = $t;
			echo form_radio($check);
			$t++;
			?>Periods
			</label>
			<label for="timetable_cols_days" class="check">
			<?php
			unset($check);
			$check['name'] = 'timetable_cols';
			$check['id'] = 'timetable_cols_days';
			$check['value'] = 'days';
			$check['checked'] = set_radio($check['name'], $check['value'], ($settings['timetable.cols'] == $check['value']));
			$check['tabindex'] = $t;
			echo form_radio($check);
			$t++;
			?>Days
			</label>
		</td>
	</tr>
	
	<!-- <tr>
		<td class="caption">
			<label class="r" accesskey="O" title="Set which order rooms are displayed in - alphabetically by name or by the order that you set on each room.">Room order</label>
			<p class="tip">Set which order rooms are displayed in - alphabetically by name or by the order that you set on each room.</p>
		</td>
		<td class="field">
			<label for="order_alpha" class="check">
			<?php
			unset($check);
			$check['name'] = 'room_order';
			$check['id'] = 'order_alpha';
			$check['value'] = 'alpha';
			$check['checked'] = set_radio($check['name'], $check['value'], ($main->room_order == $check['value']));
			$check['tabindex'] = $t;
			echo form_radio($check);
			$t++;
			?>Alphabetically
			</label>
			<label for="order_order" class="check">
			<?php
			unset($check);
			$check['name'] = 'room_order';
			$check['id'] = 'order_order';
			$check['value'] = 'order';
			$check['checked'] = set_radio($check['name'], $check['value'], ($main->room_order == $check['value']));
			$check['tabindex'] = $t;
			echo form_radio($check);
			$t++;
			?>By order
			</label>
		</td>
	</tr> -->
	
</table>
</div></div>

<table class="form">
	
	<?php
	unset($buttons);
	$buttons[] = array('submit', 'ok', 'Save main settings', $t);
	#$buttons[] = array('submit', '', 'Save and add another', 'add.gif', $t+1);
	#$buttons[] = array('cancel', 'negative', 'Cancel', 'arr-left.gif', $t+2, site_url('dashboard'));
	$this->load->view('parts/buttons', array('buttons' => $buttons));
	?>

</table>


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