<div style="width:300px;">
<?php echo form_open(
	'academic/years/change_working',
	NULL,
	array('uri' => $this->uri->uri_string())
	) ?>
	<p>Change the working academic year for this session, allowing you to configure items for other academic years without affecting the running of the system.</p>
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
</div>
<script type="text/javascript">_jsQ.push(function(){ $('#btnchangeyear').hide(); });</script>