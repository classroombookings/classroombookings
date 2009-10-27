<?php echo form_open(
	'academic/years/change_working',
	NULL,
	array('uri' => $this->uri->uri_string())
	) ?>
	<label for="workingyear_id" title="Change the working academic year for this session, allowing you to configure items for other academic years without affecting the running of the system."><strong>Change working academic year:</strong></label>
	<select name="workingyear_id" id="workingyear_id" onchange="this.form.submit()" >
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
	<input type="submit" value="Change" id="btnchangeyear" />
</form>
<script type="text/javascript">$('#btnchangeyear').hide();</script>