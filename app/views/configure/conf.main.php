<?php
echo form_open('configure/save_main');

// Start tabindex
$t = 1;
?>


<table class="form" cellpadding="6" cellspacing="0" border="0" width="100%">
	
	<tr class="h"><td colspan="2">School Information</td></tr>
	
	<tr>
		<td class="caption">
			<label for="schoolname" class="r" accesskey="N" title="The school name will appear beneath the Classroombookings logo at the top of the page.">School <u>n</u>ame</label>
		</td>
		<td class="field">
		  <?php
			#$ = @field($this->validation->username);
			$schoolname['accesskey'] = 'N';
			$schoolname['name'] = 'schoolname';
			$schoolname['id'] = 'schoolname';
			$schoolname['size'] = '40';
			$schoolname['maxlength'] = '100';
			$schoolname['tabindex'] = $t;
			$schoolname['value'] = set_value('schoolname', $main->schoolname);
			echo form_input($schoolname);
			$t++;
			?>
		</td>
	</tr>
	
	<tr>
		<td class="caption">
			<label for="schoolurl" class="r" accesskey="W"><u>W</u>ebsite address</label>
		</td>
		<td class="field">
		  <?php
			#$ = @field($this->validation->username);
			$schoolurl['accesskey'] = 'W';
			$schoolurl['name'] = 'schoolurl';
			$schoolurl['id'] = 'schoolurl';
			$schoolurl['size'] = '50';
			$schoolurl['maxlength'] = '100';
			$schoolurl['tabindex'] = $t;
			$schoolurl['value'] = set_value('schoolurl', $main->schoolurl);
			echo form_input($schoolurl);
			$t++;
			?>
		</td>
	</tr>
	
	<tr class="h"><td colspan="2">Booking display settings</td></tr>
	
	<tr>
		<td class="caption">
			<label class="r" accesskey="D" title="This is the style in which the main booking table will be displayed.">Timetable <u>v</u>iew</label>
		</td>
		<td class="field">
			<label for="view_day" class="check">
			<?php
			unset($check);
			$check['name'] = 'tt_view';
			$check['id'] = 'view_day';
			$check['value'] = 'day';
			$check['checked'] = set_radio($check['name'], $check['value'], ($main->tt_view == $check['value']));
			$check['tabindex'] = $t;
			echo form_radio($check);
			$t++;
			?>One day at a time
			</label>
			<label for="view_room" class="check">
			<?php
			#$ = @field($this->validation->username);
			unset($check);
			$check['name'] = 'tt_view';
			$check['id'] = 'view_room';
			$check['value'] = 'room';
			$check['checked'] = set_radio($check['name'], $check['value'], ($main->tt_view == $check['value']));
			$check['tabindex'] = $t;
			echo form_radio($check);
			$t++;
			?>One room at a time
			</label>
		</td>
	</tr>
	
	
	<tr>
		<td class="caption">
			<label class="r" accesskey="C" title="This controls what information is displayed in columns on your bookings table - the other option will be displayed in the rows."><u>C</u>olumn item</label>
		</td>
		<td class="field">
			<label for="col_periods" class="check">
			<?php
			unset($check);
			$check['name'] = 'tt_cols';
			$check['id'] = 'col_periods';
			$check['value'] = 'periods';
			$check['checked'] = set_radio('bd_col', 'periods', ($main->tt_cols == $check['value']));
			$check['tabindex'] = $t;
			echo form_radio($check);
			$t++;
			?>Periods
			</label>
			<label for="col_days" class="check">
			<?php
			unset($check);
			$check['name'] = 'tt_cols';
			$check['id'] = 'col_days';
			$check['value'] = 'days';
			$check['checked'] = set_radio('bd_col', 'days', ($main->tt_cols == $check['value']));
			$check['tabindex'] = $t;
			echo form_radio($check);
			$t++;
			?>Days
			</label>
			<!-- <label for="col_rooms" class="check">
			<?php
			unset($check);
			$check['name'] = 'bd_col';
			$check['id'] = 'col_rooms';
			$check['value'] = 'rooms';
			$check['checked'] = set_radio('bd_col', 'rooms', ($main->bd_col == $check['value']));
			$check['tabindex'] = $t;
			echo form_radio($check);
			$t++;
			?>Rooms
			</label> -->
		</td>
	</tr>
	
	<tr>
		<td class="caption">
			<label class="r" accesskey="O" title="Set which order rooms are displayed in - alphabetically by name or by the order that you set on each room.">Room <u>o</u>rder</label>
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
	</tr>
	
	<?php
	unset($buttons);
	$buttons[] = array('submit', 'positive', 'Save main settings', 'disk1.gif', $t);
	#$buttons[] = array('submit', '', 'Save and add another', 'add.gif', $t+1);
	#$buttons[] = array('cancel', 'negative', 'Cancel', 'arr-left.gif', $t+2, site_url('dashboard'));
	$this->load->view('parts/buttons', array('buttons' => $buttons));
	?>

</table>
</form>

<script type="text/javascript">
function tt_day(){
	$("#col_periods").removeAttr("disabled");
	$("#col_days").attr("disabled","disabled");
	$("#col_rooms").attr("disabled","disabled");
	$("#col_periods").attr("checked", "checked");
}
function tt_room(){
	$("#col_periods").removeAttr("disabled");
	$("#col_days").removeAttr("disabled");
	$("#col_rooms").attr("disabled", "disabled");
}
$("#view_day").bind("click", function(e){ tt_day(); });
$("#view_room").bind("click", function(e){ tt_room(); });
if($("#view_day").attr("checked")){ tt_day(); }
if($("#view_room").attr("checked")){ tt_room(); }
</script>