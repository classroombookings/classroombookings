<?php
$errors = validation_errors();
if($errors){
	echo $this->msg->err('<ul>' . $errors . '</ul>', $this->lang->line('FORM_ERRORS'));
}

echo form_open('academic/years/save', NULL, array('year_id' => $year_id));

// Start tabindex
$t = 1;
?>


<table class="form" cellpadding="6" cellspacing="0" border="0" width="100%">
	
	<!-- <tr class="h"><td colspan="2">Year details</td></tr> -->
	
	<tr>
		<td class="caption">
			<label for="name" class="r" accesskey="N"><u>N</u>ame</label>
		</td>
		<td class="field">
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
		</td>
	</tr>
	
	
	<tr>
		<td class="caption">
			<label for="time_start" class="r" accesskey="S"><u>S</u>tart date</label>
		</td>
		<td class="field">
			<?php
			unset($input);
			$input['accesskey'] = 'S';
			$input['name'] = 'date_start';
			$input['id'] = 'input_date_start';
			$input['size'] = '15';
			$input['maxlength'] = '10';
			$input['tabindex'] = $t;
			$input['class'] = 'date';
			$input['value'] = @set_value($input['name'], $year->date_start);
			echo form_input($input);
			$t++;
			?>
			<div class="datepicker" id="date_start"></div>
		</td>
	</tr>
	
	
	<tr>
		<td class="caption">
			<label for="time_end" class="r" accesskey="E"><u>E</u>nd date</label>
		</td>
		<td class="field">
			<?php
			unset($input);
			$input['accesskey'] = 'E';
			$input['name'] = 'date_end';
			$input['id'] = 'input_date_end';
			$input['size'] = '15';
			$input['maxlength'] = '10';
			$input['tabindex'] = $t;
			$input['class'] = 'date';
			$input['value'] = @set_value($input['name'], $year->date_end);
			echo form_input($input);
			$t++;
			?>
			<div class="datepicker" id="date_end"></div>
		</td>
	</tr>
	
	
	<tr>
		<td class="caption">
			<label for="active" accesskey="C" title="Tick this box to set this year to be the current or active year">A<u>c</u>tive</label>
		</td>
		<td class="field">
			<label for="active" class="check">
			<?php
			unset($check);
			$check['name'] = 'active';
			$check['id'] = 'active';
			$check['value'] = '1';
			$check['checked'] = @set_checkbox($check['name'], $check['value'], ($year->active == 1));
			$check['tabindex'] = $t;
			echo form_checkbox($check);
			$t++;
			?>Yes
			</label>
		</td>
	</tr>
	
	
	<?php
	if($year_id == NULL){
		$submittext = $this->lang->line('ACTION_ADD') . ' ' . strtolower($this->lang->line('W_YEAR'));
	} else {
		$submittext = $this->lang->line('ACTION_SAVE') . ' ' . strtolower($this->lang->line('W_YEAR'));
	}
	unset($buttons);
	$buttons[] = array('submit', 'positive', $submittext, 'disk1.gif', $t);
	$buttons[] = array('cancel', 'negative', $this->lang->line('ACTION_CANCEL'), 'arr-left.gif', $t+2, site_url('academic/years'));
	$this->load->view('parts/buttons', array('buttons' => $buttons));
	?>
	

</table>
</form>


<script type="text/javascript">
$(function(){
	$(".date").hide();
	$(".datepicker").datepicker({
		firstDat: 1,
		dateFormat: 'yy-mm-dd',
		onSelect: function(dateText, inst){$('#input_' + this.id).val(dateText)},
	});
	
});


	
	
	
/* $.extend(DateInput.DEFAULT_OPTS, {
	stringToDate: function(string){
		var matches;
		if(matches = string.match(/^(\d{4,4})-(\d{2,2})-(\d{2,2})$/)){
			return new Date(matches[1], matches[2] - 1, matches[3]);
		} else {
			return null;
		};
	},
	dateToString: function(date){
		var month = (date.getMonth() + 1).toString();
		var dom = date.getDate().toString();
		if (month.length == 1) month = "0" + month;
		if (dom.length == 1) dom = "0" + dom;
		return date.getFullYear() + "-" + month + "-" + dom;
	}
});

$(function(){
	$(".date").date_input();
}); */

/* Calendar View, deprecated..
document.observe('dom:loaded',function(){
	Calendar.setup({
		dateField:'date_start',
		parentElement:'calendar_date_start'
	});
	$('date_start').hide();
	Calendar.setup({
		dateField:'date_end',
		parentElement:'calendar_date_end'
	});
	$('date_end').hide();
});
*/

</script>