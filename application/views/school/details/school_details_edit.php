<?php
echo $this->session->flashdata('saved');
echo form_open_multipart('school/details_submit', array('id'=>'schooldetails', 'class'=>'cssform'));
?>


<fieldset>

	<legend accesskey="I" tabindex="<?php echo tab_index(); ?>">School Information</legend>

	<p>
		<label for="schoolname" class="required">School name</label>
		<?php
		$value = set_value('schoolname', element('name', $settings), FALSE);
		echo form_input(array(
			'name' => 'schoolname',
			'id' => 'schoolname',
			'size' => '30',
			'maxlength' => '255',
			'tabindex' => tab_index(),
			'value' => $value,
		));
		?>
	</p>
	<?php echo form_error('schoolname'); ?>

	<p>
		<label for="website">Website address</label>
		<?php
		$value = set_value('website', element('website', $settings), FALSE);
		echo form_input(array(
			'name' => 'website',
			'id' => 'website',
			'size' => '40',
			'maxlength' => '255',
			'tabindex' => tab_index(),
			'value' => $value,
		));
		?>
	</p>
	<?php echo form_error('website'); ?>

</fieldset>



<fieldset>

	<legend accesskey="L" tabindex="<?php echo tab_index() ?>">School Logo</legend>

	<div>Use this section to upload a school logo.</div>

	<p>
		<label>Current logo</label>
		<?php
		$logo = element('logo', $settings);
		if ( ! empty($logo) && is_file(FCPATH . 'uploads/' . $logo)) {
			echo img('uploads/' . $logo, FALSE, "style='padding:1px; border:1px solid #ccc; max-width: 300px; width: auto; height: auto'");
		} else {
			echo "<span><em>None found</em></span>";
		}
		?>
	</p>

	<p>
		<label for="userfile">File upload</label>
		<?php
		echo form_upload(array(
			'name' => 'userfile',
			'id' => 'userfile',
			'size' => '25',
			'maxlength' => '255',
			'tabindex' => tab_index(),
			'value' => '',
		));
		?>
		<p class="hint">Uploading a new logo will <span>overwrite</span> the current one.</p>
	</p>

	<?php
	if ($this->session->flashdata('image_error') != '' ) {
		echo "<p class='hint error'><span>" . $this->session->flashdata('image_error') . "</span></p>";
	}
	?>

	<p>
		<label for="logo_delete">Delete logo?</label>
		<?php
		echo form_checkbox(array(
			'name' => 'logo_delete',
			'id' => 'logo_delete',
			'value' => '1',
			'tabindex' => tab_index(),
			'checked' => FALSE,
		));
		?>
		<p class="hint">Tick this box to <span>delete the current logo</span>. If you are uploading a new logo this will be done automatically.</p>
	</p>

</fieldset>


<fieldset>

	<legend accesskey="S" tabindex="<?php echo tab_index() ?>">Settings</legend>

	<?php
	/*
	<p>
		<label for="colour">Header colour</label>
		<?php
		$value = set_value('colour', element('colour', $settings, '468ED8'), FALSE);
		echo colour_widget(array(
			'name' => 'colour',
			'tabindex' => tab_index(),
			'value' => $value,
		));
		?>
		<p class="hint">In hexadecimal format. Leave blank to use default blue.</p>
	</p>
	<?php echo form_error('colour'); ?>
	*/
	?>

	<p>
		<label for="bia">Booking in advance</label>
		<?php
		$value = (int) set_value('bia', element('bia', $settings), FALSE);
		echo form_input(array(
			'name' => 'bia',
			'id' => 'bia',
			'size' => '5',
			'maxlength' => '3',
			'tabindex' => tab_index(),
			'value' => $value,
		));
		?>
		<p class="hint">How many days in the future users can make their own bookings. Enter 0 for no restriction.</p>
	</p>
	<?php echo form_error('bia') ?>

	<hr size="1" />

	<p>
		<label for="displaytype">Bookings display type</label>
		<?php
		$displaytype = set_value('displaytype', element('displaytype', $settings), FALSE);
		$options = array(
			'day' => 'One day at a time',
			'room' => 'One room at a time',
		);
		echo form_dropdown(
			'displaytype',
			$options,
			$displaytype,
			' id="displaytype" tabindex="' . tab_index() . '"'
		);
		?>
		<p class="hint">Specify the main focus of the bookings page.<br />
			<strong><span>One day at a time</span></strong> - all periods and rooms are shown for the selected date.<br />
			<strong><span>One room at a time</span></strong> - all periods and days of the week are shown for the selected room.
		</p>
	</p>
	<?php echo form_error('displaytype'); ?>

	<p>
		<label for="columns">Bookings columns</label>
		<?php
		$columns = set_value('d_columns', element('d_columns', $settings), FALSE);
		?>
		<select name="d_columns" id="d_columns" tabindex="<?php echo tab_index() ?>">
			<option value="periods" class="day room" <?= $columns == 'periods' ? 'selected="selected"' : '' ?>>Periods</option>
			<option value="rooms" class="day" <?= $columns == 'rooms' ? 'selected="selected"' : '' ?>>Rooms</option>
			<option value="days" class="room" <?= $columns == 'days' ? 'selected="selected"' : '' ?>>Days</option>
		</select>
		<p class="hint">Select which details you want to be displayed along the top of the bookings page.</p>
	</p>
	<?php echo form_error('d_columns') ?>

</fieldset>

<script type="text/javascript">
Q.push(function() {
	dynamicSelect('displaytype', 'd_columns');
});
</script>

<?php

$this->load->view('partials/submit', array(
	'submit' => array('Save', tab_index()),
	'cancel' => array('Cancel', tab_index(), 'controlpanel'),
));

echo form_close();
