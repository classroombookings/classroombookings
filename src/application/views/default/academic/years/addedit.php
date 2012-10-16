<?php
$errors = validation_errors();
if ($errors)
{
	echo '<div class="row">';
	echo $this->msg->err('<ul class="square">' . $errors . '</ul>', 'Please check the following invalid item(s) and try again.');
	echo '</div>';
}


echo form_open('academic/years/save', null, array('year_id' => $year_id));


// Start tabindex
$t = 1;
?>


<div class="alpha three columns"><h6>Details</h6></div>

<div class="omega nine columns">

	<label for="name">Name</label>
	<?php
	unset($input);
	$input['accesskey'] = 'N';
	$input['name'] = 'name';
	$input['id'] = 'name';
	$input['size'] = '30';
	$input['maxlength'] = '20';
	$input['tabindex'] = $t;
	$input['value'] = @set_value('name', $year->name);
	echo form_input($input);
	$t++;
	?>
	
	
	
	<label for="date_start">Start date</label>
	<?php
	unset($input);
	$input['type'] = 'date';
	$input['accesskey'] = 'S';
	$input['name'] = 'date_start';
	$input['id'] = 'input_date_start';
	$input['size'] = '15';
	$input['maxlength'] = '10';
	$input['tabindex'] = $t;
	$input['class'] = 'date_input';
	$input['value'] = @set_value($input['name'], $year->date_start);
	echo form_input($input);
	$t++;
	?>
	
	<label for="date_end">End date</label>
	<?php
	unset($input);
	$input['type'] = 'date';
	$input['accesskey'] = 'S';
	$input['name'] = 'date_end';
	$input['id'] = 'input_date_end';
	$input['size'] = '15';
	$input['maxlength'] = '10';
	$input['tabindex'] = $t;
	$input['class'] = 'date_input';
	$input['value'] = @set_value($input['name'], $year->date_end);
	echo form_input($input);
	$t++;
	?>

	
	<label for="current" class="check">
	<?php
	echo form_hidden('current', '0');
	unset($input);
	$input['name'] = 'current';
	$input['id'] = 'current';
	$input['value'] = '1';
	$input['checked'] = @set_checkbox($input['name'], $input['value'], 
				($year->current == $input['value']));
	$input['tabindex'] = $t;
	echo form_checkbox($input);
	$t++;
	?>Make current
	</label>
	
	<br class="clear">

</div>


<hr>


<div class="alpha three columns">&nbsp;</div>
<div class="omega nine columns"><?php
$text = ($year_id == null) ? 'Add' : 'Save';
unset($buttons);
$buttons[] = array('submit', 'blue', "$text academic year", $t);
$this->load->view('parts/buttons', array('buttons' => $buttons));
?></div>


</form>



<script type="text/javascript">
_jsQ.push(function(){
	$("input.date_input").dateinput({ format: 'yyyy-mm-dd', firstDay: 1 });
	
	$("input.date_input:first").data("dateinput").change(function(){
		$("input.date_input:last").data("dateinput").setMin(this.getValue(), true);
	});
});
</script>