<?php
// Load errors
#echo $this->validation->error_string;

echo form_open(
	'configure/save',
	array('id' => 'conf-main')
);

// Start tabindex
$t = 1;
?>


<table class="form" cellpadding="6" cellspacing="0" border="0" width="50%">
	
	<tr class="h"><td colspan="2">School Information</td></tr>
	
	<tr>
		<td class="caption"><label for="schoolname" class="r" accesskey="N">School <u>n</u>ame</label></td>
		<td class="field">
		  <?php
			#$ = @field($this->validation->username);
			echo form_input(array(
				'accesskey' => 'N',
				'name' => 'schoolname',
				'id' => 'schoolname',
				'size' => '40',
				'maxlenght' => '30',
				'tabindex' => $t,
				'value' => set_value('schoolname'),
			));
			$t++;
			?>
		</td>
	</tr>
	
	
	<tr>
		<td class="caption"><label for="schoolname" class="r" accesskey="W"><u>W</u>ebsite address</label></td>
		<td class="field">
		  <?php
			#$ = @field($this->validation->username);
			echo form_input(array(
				'accesskey' => 'W',
				'name' => 'url',
				'id' => 'url',
				'size' => '50',
				'maxlenght' => '100',
				'tabindex' => $t,
				'value' => set_value('url'),
			));
			$t++;
			?>
		</td>
	</tr>
	
	<tr class="h"><td colspan="2">Booking settings</td></tr>
	
	<tr>
		<td class="caption"><label for="booking-display" class="r" accesskey="D"><u>D</u>isplay mode</label></td>
		<td class="field">
			<label for="bd-day" class="check">
			<?php
			echo form_radio(array(
				'name' => 'booking-display',
				'id' => 'bd-day',
				'tabindex' => $t,
				'value' => 'day',
				'checked' => FALSE,
			));
			$t++;
			?>One day at a time
			</label>
			<label for="bd-room" class="check">
			<?php
			#$ = @field($this->validation->username);
			echo form_radio(array(
				'name' => 'booking-display',
				'id' => 'bd-room',
				'tabindex' => $t,
				'value' => 'room',
				'checked' => FALSE,
			));
			$t++;
			?>One room at a time
			</label>
		</td>
	</tr>
	
	
	<tr>
		<td class="caption"><label for="booking-columns" class="r" accesskey="C"><u>C</u>olumn item</label></td>
		<td class="field">
			<label for="col-periods" class="check">
			<?php
			#$ = @field($this->validation->username);
			echo form_radio(array(
				'name' => 'bd-col',
				'id' => 'col-periods',
				'tabindex' => $t,
				'value' => 'periods',
				'checked' => FALSE,
			));
			$t++;
			?>Periods
			</label>
			<label for="col-days" class="check">
			<?php
			echo form_radio(array(
				'name' => 'bd-col',
				'id' => 'col-days',
				'tabindex' => $t,
				'value' => 'days',
				'checked' => FALSE,
			));
			$t++;
			?>Days
			</label>
			<label for="col-rooms" class="check">
			<?php
			echo form_radio(array(
				'name' => 'bd-col',
				'id' => 'col-rooms',
				'tabindex' => $t,
				'value' => 'rooms',
				'checked' => FALSE,
			));
			$t++;
			?>Rooms
			</label>
		</td>
	</tr>

	
	<?php
	/*$submit['submit'] = array('Save main settings', $t);
	$submit['cancel'] = array('Cancel', $t+1, site_url());
	$this->load->view('parts/submit', $submit);
	echo form_close();*/
	?>
	
	<?php
	$buttons[] = array('submit', 'positive', 'Save main settings', 'disk1.gif', $t);
	#$buttons[] = array('submit', '', 'Save and add another', 'add.gif', $t+1);
	#$buttons[] = array('cancel', 'negative', 'Cancel', 'arr-left.gif', $t+2, site_url('dashboard'));
	$this->load->view('parts/buttons', array('buttons' => $buttons));
	?>
	

</table>
</form>

<script type="text/javascript">
$("#bd-day").bind("click", function(e){
	$("#col-periods").removeAttr("disabled");
	$("#col-days").attr("disabled","disabled");
	$("#col-rooms").removeAttr("disabled");
	});
$("#bd-room").bind("click", function(e){
	$("#col-periods").removeAttr("disabled");
	$("#col-days").removeAttr("disabled","disabled");
	$("#col-rooms").attr("disabled", "disabled");
	});
</script>