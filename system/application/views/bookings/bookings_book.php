<script type="text/javascript">
function toggle_recurring(){
	$recurring = $('recurring').checked;
	$('week_id').disabled = !$recurring;
	$('day_num').disabled = !$recurring;
	//$('user_id').disabled = !$recurring;
}
</script><?php
echo $this->session->flashdata('saved');
echo form_open('bookings/save', array('id'=>'bookings_book', 'class'=>'cssform'), $hidden);
$t = 1;
?>


<fieldset><legend accesskey="I" tabindex="<?php echo $t; $t++; ?>">Booking Information</legend>


<p>
	<label>Use:</label>
  <?php
	$notes = @field($this->validation->notes, $booking['notes']);
	$input['name'] = 'notes';
	$input['id'] = 'notes';
	$input['size'] = '50';
	$input['maxlength'] = '100';
	$input['tabindex'] = $t;
	$input['value'] = $notes;
	echo form_input($input);
	unset($input);
	$t++;
	?>
</p>


<?php if($this->userauth->CheckAuthLevel(ADMINISTRATOR, $this->authlevel)){ ?>
<p>
	<label>Date:</label>
  <?php
	$date = @field($this->validation->date, $booking['date']);
	$input['name'] = 'date';
	$input['id'] = 'date';
	$input['size'] = '10';
	$input['maxlength'] = '10';
	$input['tabindex'] = $t;
	$input['value'] = $date;
	echo form_input($input);
	unset($input);
	$t++;
	?>
</p>


<p>
  <label for="room_id" class="required">Room:</label>
  <?php
  foreach($rooms as $room){
  	$roomlist[$room->room_id] = $room->name;
  }
	$room_id = @field($this->validation->room_id, $booking['room_id']);
	echo form_dropdown('room_id', $roomlist, $room_id, 'tabindex="'.$t.'"');
	$t++;
	?>
</p>
<?php echo @field($this->validation->room_id_error); ?>


<p>
  <label for="period_id" class="required">Period:</label>
  <?php
  foreach($periods as $period){
  	$periodlist[$period->period_id] = $period->name . ' ('.date('G:i', strtotime($period->time_start)).' - '.date('G:i', strtotime($period->time_end)).')';
  }
	$period_id = @field($this->validation->period_id, $booking['period_id']);
	echo form_dropdown('period_id', $periodlist, $period_id, 'tabindex="'.$t.'"');
	$t++;
	?>
</p>
<?php echo @field($this->validation->period_id_error); ?>


<p>
  <label for="user_id">User:</label>
  <?php
	$userlist['0'] = '(None)';
  foreach($users as $user){
  	if( $user->displayname == '' ){ $user->displayname = $user->username; }
  	$userlist[$user->user_id] = $user->displayname;		#@field($user->displayname, $user->username);
  }
	$user_id = @field($this->validation->user_id, $booking['user_id'], $this->session->userdata('user_id'));
	echo form_dropdown('user_id', $userlist, $user_id, 'id="user_id" tabindex="'.$t.'"');
	$t++;
	?>
</p>
<?php echo @field($this->validation->user_id_error); ?>


<?php } ?>


</fieldset>




<?php if($this->userauth->CheckAuthLevel(ADMINISTRATOR, $this->authlevel)){ ?>
<fieldset><legend accesskey="R" tabindex="<?php echo $t; $t++; ?>">Recurring options</legend>


<p>
	<label for="recurring">Recurring?</label>
  <?php
	echo form_checkbox(array( 
		'name' => 'recurring',
		'id' => 'recurring',
		'value' => '1',
		'tabindex' => $t,
		'checked' => false,
		'onchange' => 'toggle_recurring()',
	));
	$t++;
	?>
</p>


<p>
  <label for="week_id">Week:</label>
  <?php
  $weeklist[0] = '(None)';
  foreach($weeks as $week){
  	$weeklist[$week->week_id] = $week->name;
  }
	$week_id = @field($this->validation->week_id, $booking['week_id']);
	echo form_dropdown('week_id', $weeklist, $week_id, 'id="week_id" tabindex="'.$t.'"');
	$t++;
	?>
</p>
<?php echo @field($this->validation->day_num_error); ?> 


<p>
  <label for="day_num">Day:</label>
  <?php
  $days['X'] = '(None)';
	$day_num = @field($this->validation->day_num, $booking['day_num'], 'X');
	echo form_dropdown('day_num', array_reverse($days, True), $day_num, 'id="day_num" tabindex="'.$t.'"');
	$t++;
	?>
</p>
<?php echo @field($this->validation->day_num_error); ?>


</fieldset>
<?php } ?>


<?php
$submit['submit'] = array('Book', $t);
$submit['cancel'] = array('Cancel', $t+1, $this->session->userdata('uri'));
$this->load->view('partials/submit', $submit);
echo form_close();
?>

<script type="text/javascript">toggle_recurring();</script>
