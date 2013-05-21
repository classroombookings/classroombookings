<?php
if( !isset($holiday_id) ){
	$holiday_id = @field($this->uri->segment(3, NULL), $this->validation->holiday_id, 'X');
}
$errorstr = $this->validation->error_string;

echo form_open('holidays/save', array('class' => 'cssform', 'id' => 'holiday_add'), array('holiday_id' => $holiday_id) );
?>


<fieldset><legend accesskey="H" tabindex="1">Holiday Information</legend>
<p>
  <label for="name" class="required">Name</label>
  <?php
	$name = @field($this->validation->name, $holiday->name);
	echo form_input(array(
		'name' => 'name',
		'id' => 'name',
		'size' => '30',
		'maxlength' => '40',
		'tabindex' => '2',
		'value' => $name,
	));
	?>
</p>
<?php echo @field($this->validation->name_error) ?>


<p>
  <label for="date_start" class="required">Start Date</label>
  <?php
	$date_start = date("d/m/Y", strtotime(@field($this->validation->date_start, $holiday->date_start, date("Y-m-d"))));
	echo form_input(array(
		'name' => 'date_start',
		'id' => 'date_start',
		'size' => '10',
		'maxlength' => '10',
		'tabindex' => '3',
		'value' => $date_start,
	));
	?>
	<img style="cursor:pointer" align="top" src="webroot/images/ui/cal_day.gif" width="16" height="16" title="Choose date" onclick="displayDatePicker('date_start', false);" />
</p>
<?php echo @field($this->validation->date_end_error) ?>


<p>
  <label for="date_end" class="required">End Date</label>
  <?php
	$date_end = date("d/m/Y", strtotime(@field($this->validation->date_end, $holiday->date_end, date("Y-m-d"))));
	echo form_input(array(
		'name' => 'date_end',
		'id' => 'date_end',
		'size' => '10',
		'maxlength' => '10',
		'tabindex' => '4',
		'value' => $date_end,
	));
	?>
	<img style="cursor:pointer" align="top" src="webroot/images/ui/cal_day.gif" width="16" height="16" title="Choose date" onclick="displayDatePicker('date_end', false);" />
</p>
<?php echo @field($this->validation->date_end_error) ?>


</fieldset>



<div class="submit" style="border-top:0px;">
  <?php echo form_submit(array('value' => 'Save', 'tabindex' => '5')) ?> 
	&nbsp;&nbsp; 
	<?php echo anchor('holidays', 'Cancel', array('tabindex' => '6')) ?>
</div>
