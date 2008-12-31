<?php echo form_open(
	'academic/years/change_working',
	NULL,
	array('uri' => $this->uri->uri_string())
	) ?>
<div>
	<label for="workingyear_id" title="Change the working academic year for this session, allowing you to configure items for other academic years without affecting the running of the system.">Change working academic year:</label>
	<select name="workingyear_id" id="workingyear_id">
	<?php
	$w = FALSE;
	foreach($years as $id => $name){
		$selected = '';		
		if($this->session->userdata('year_active') == $id AND $w == FALSE){
			$selected = ' selected="selected"';
		}
		if($this->session->userdata('year_working') == $id){
			$name = '* ' . $name;
			$selected = ' selected="selected"';
			$w = TRUE;
		}
		echo sprintf('<option value="%d"%s>%s</option>', $id, $selected, $name);
	}
	?>
	</select>
	<input type="submit" value="Change" />
</div>
</form>