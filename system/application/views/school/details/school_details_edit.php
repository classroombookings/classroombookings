<?php
echo $this->session->flashdata('saved');
echo form_open_multipart('school/details_submit', array('id'=>'schooldetails', 'class'=>'cssform'));
$t = 1;
?>


<fieldset><legend accesskey="I" tabindex="<?php echo $t; $t++; ?>">School Information</legend>


<p>
  <label for="schoolname" class="required">School name</label>
  <?php
	$schoolname = @field($this->validation->schoolname, $info->name);
	echo form_input(array(
		'name' => 'schoolname',
		'id' => 'schoolname',
		'size' => '30',
		'maxlength' => '255',
		'tabindex' => $t,
		'value' => $schoolname,
	));
	$t++;
	?>
</p>
<?php echo @field($this->validation->schoolname_error); ?>


<p>
  <label for="website">Website address</label>
  <?php
	$website = @field($this->validation->website, $info->website, 'http://');
	echo form_input(array(
		'name' => 'website',
		'id' => 'website',
		'size' => '40',
		'maxlength' => '255',
		'tabindex' => $t,
		'value' => $website,
	));
	$t++;
	?>
</p>
<?php echo @field($this->validation->website_error); ?>


</fieldset>





<fieldset><legend accesskey="L" tabindex="<?php echo $t; $t++; ?>">School Logo</legend>


Please use this section to upload a school logo. Thumbnails will be created of large images.


<p>
  <label>Current logo</label>
  <?php
	if( isset($info->logo) && $info->logo != ''){
		$logo['300'] = 'webroot/images/schoollogo/300/'.$info->logo;
		$logo['100'] = 'webroot/images/schoollogo/100/'.$info->logo;
		if( file_exists($logo['100']) && file_exists($logo['300']) ){
			echo '<a href="'.$logo['300'].'" title="View Logo">';
			echo '<img src="'.$logo['100'].'" style="padding:1px;border:1px solid #ccc" alt="View Logo" />';
			echo '</a>';
		} else {
			echo '<em>None on file</em>';
		}
	} else {
		echo '<em>None in database</em>';
	}
	?>
</p>


<p>
  <label for="userfile">File upload</label>
  <?php
	#$photo = @field($this->validation->photo, $room->photo);
	echo form_upload(array(
		'name' => 'userfile',
		'id' => 'userfile',
		'size' => '25',
		'maxlength' => '255',
		'tabindex' => $t,
		'value' => '',
	));
	$t++;
	?>
	<p class="hint">Uploading a new logo will <span>overwrite</span> the current one.</p>
</p>
<?php if($this->session->flashdata('image_error') != '' ){ ?>
<p class="hint error"><span><?php echo @field($image_error, $this->session->flashdata('image_error')) ?></span></p>
<?php } ?>


<p>
  <label for="logo_delete">Delete logo?</label>
  <?php
	#$photo = @field($this->validation->name, $room->name);
	echo form_checkbox( array(
		'name' => 'logo_delete',
		'id' => 'logo_delete',
		'value' => 'true',
		'tabindex' => $t,
		'checked' => false,
	));
	$t++;
	?>
	<p class="hint">Tick this box to <span>delete the current logo</span>. If you are uploading a new logo this will be done automatically.</p>
</p>


</fieldset>





<fieldset><legend accesskey="S" tabindex="<?php echo $t ?>">Settings</legend>


<p>
  <label for="colour">Header colour</label>
  <?php
	$colour = @field($this->validation->colour, $info->colour, '468ED8');
	$coloursel['name'] = 'colour';
	$coloursel['tabindex'] = $t;
	$coloursel['value'] = $colour;
	$this->load->view('partials/input-coloursel', $coloursel);
	$t++;
	?>
	<p class="hint">Leave blank to use default blue</p>
</p>
<?php echo @field($this->validation->colour_error) ?>


<p>
	<label for="bia">Booking in advance</label>
  <?php
	$bia = (int) @field($this->validation->bia, $info->bia);
	echo form_input(array(
		'name' => 'bia',
		'id' => 'bia',
		'size' => '5',
		'maxlength' => '3',
		'tabindex' => $t,
		'value' => $bia,
	));
	$t++;
	?>
	<p class="hint">How many days in advance users can make their own bookings. Enter 0 for unlimited (within the academic year).</p>
</p>
<?php echo @field($this->validation->bia_error) ?>


<!-- <p>
	<label for="bia">Booking quota</label>
  <?php
	/* $bquota = @field($this->validation->bquota, $info->bquota);
	echo form_input(array(
		'name' => 'bquota',
		'id' => 'bquota',
		'size' => '5',
		'maxlength' => '3',
		'tabindex' => $t,
		'value' => $bquota,
	));
	$t++; */
	?>
	<p class="hint">Number of bookings a teacher is allowed to make, within the number of days configured above. Enter 0 for unlimited.</p>
</p> -->
<?php #echo @field($this->validation->bquota_error) ?>

<hr size="1" />


<p>
	<label for="displaytype">Bookings display type</label>
  <?php
	$displaytype = @field($this->validation->displaytype, $info->displaytype);
	$options = array('day' => 'One day at a time', 'room' => 'One room at a time', );
	echo form_dropdown(
		'displaytype',
		$options,
		$displaytype,
		' id="displaytype" tabindex="'.$t.'"'
	);
	$t++;
	?>
	<p class="hint">Select how users view the bookings page.<br />
		<strong><span>One day at a time</span></strong> - all periods and rooms are shown for the selected date.<br />
		<strong><span>One room at a time</span></strong> - all periods and days of the week are shown for the selected room.
	</p>
</p>
<?php echo @field($this->validation->displaytype_error) ?>


<p>
	<label for="columns">Bookings columns</label>
  <?php
	$columns = @field($this->validation->columns, $info->columns);
	?>
	<select name="d_columns" id="d_columns" tabinde="<?php echo $t; $t++; ?>">
		<option value="periods" class="day room">Periods</option>
		<option value="rooms" class="day">Rooms</option>
		<option value="days" class="room">Days</option>
	</select>
	<?php
	/*$options = array('periods' => 'Periods', 'rooms' => 'Rooms', 'days' => 'Days');
	echo form_dropdown(
		'columns',
		$options,
		$columns,
		' id="columns" tabindex="'.$t.'"'
	);*/
	$t++;
	?>
	<p class="hint">Select which details you want to be displayed along the top of the bookings page.</p>
</p>
<?php echo @field($this->validation->columns_error) ?>


</fieldset>


<script type="text/javascript">
dynamicSelect('displaytype', 'd_columns');
</script>


<?php
$submit['submit'] = array('Save', $t);
$submit['cancel'] = array('Cancel', $t+1, 'controlpanel');
$this->load->view('partials/submit', $submit);
echo form_close();
?>
