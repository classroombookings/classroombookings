<?php
$errors = validation_errors();
if($errors){
	echo $this->msg->err('<ul>' . $errors . '</ul>', $this->lang->line('FORM_ERRORS'));
}
?>

<table class="list form" width="100%" cellpadding="0" cellspacing="0" border="0">
	<col /><col /><col />
	<thead>
	<tr class="heading">
		<td class="h" title="D">&nbsp;</td>
		<td class="h" title="Name">Name</td>
		<td class="h" title="DateStart">Start Date</td>
		<td class="h" title="DateEnd">End Date</td>
		<td class="h" title="X">&nbsp;</td>
	</tr>
	</thead>
	<tbody>
	<?php
	echo form_open('academic/holidays/save');
	
	$i = 0;
	$t = 1;
	
	if($holidays != 0){
		foreach($holidays as $holiday){
		
		echo form_hidden('holiday_ids[]', $holiday->holiday_id);
		
		echo form_hidden("holiday[{$holiday->holiday_id}][holiday_id]", $holiday->holiday_id);
		
		?>
		<tr>
			
			<td align="center" width="20"><?php
			unset($check);
			$check['name'] = "holiday[{$holiday->holiday_id}][delete]";
			$check['id'] = "delete_{$holiday->holiday_id}";
			$check['value'] = '1';
			$check['tabindex'] = $t;
			$check['title'] = 'Tick this box to allow deletion of multiple holidays at once.';
			$t++;
			echo form_checkbox($check);
			?></td>
			
			<td class="field"><?php
			unset($input);
			$input['name'] = "holiday[{$holiday->holiday_id}][name]";
			$input['id'] = "name_{$holiday->holiday_id}";
			$input['value'] = $holiday->name;
			$input['size'] = 25;
			$input['maxlength'] = 50;
			$input['tabindex'] = $t;
			echo form_input($input);
			$t++;
			?></td>
			
			<td class="field"><?php
			unset($input);
			$input['name'] = "holiday[{$holiday->holiday_id}][date_start]";
			$input['id'] = "date_start_{$holiday->holiday_id}}";
			$input['value'] = $holiday->date_start;	#date("l jS F Y", todate($term->date_start));
			$input['size'] = 15;
			$input['maxlength'] = 10;
			$input['class'] = 'date';
			$input['tabindex'] = $t;
			echo form_input($input);
			$t++;
			?></td>
			
			<td class="field"><?php
			unset($input);
			$input['name'] = "holiday[{$holiday->holiday_id}][date_end]";
			$input['id'] = "date_end_{$holiday->holiday_id}}";
			$input['value'] = $holiday->date_end;	#date("l jS F Y", todate($term->date_start));
			$input['size'] = 15;
			$input['maxlength'] = 10;
			$input['class'] = 'date';
			$input['tabindex'] = $t;
			echo form_input($input);
			$t++;
			?></td>
			
			<td class="il" width="75">
			<?php
			$actiondata[] = array('academic/holidays/delete/'.$holiday->holiday_id, 'Delete', 'cross_sm.gif');
			$this->load->view('parts/listactions', $actiondata);
			unset($actiondata);
			?></td>
			
		</tr>
		<?php
		$i++;
		}
	}
	?>
	
	<tr>
		<td>&nbsp;</td>
		
		<td class="field"><?php
		unset($input);
		$input['name'] = "newholiday[name]";
		$input['id'] = 'new_name';
		$input['value'] = set_value('newholiday[name]');
		$input['size'] = 25;
		$input['maxlength'] = 50;
		$input['tabindex'] = $t;
		echo form_input($input);
		$t++;
		?></td>
		
		<td class="field"><?php
		unset($input);
		$input['name'] = "newholiday[date_start]";
		$input['id'] = 'new_date_start';
		$input['value'] = set_value('newholiday[date_start]');
		$input['size'] = 15;
		$input['maxlength'] = 10;
		$input['class'] = 'date';
		$input['tabindex'] = $t;
		echo form_input($input);
		$t++;
		?></td>
		
		<td class="field"><?php
		unset($input);
		$input['name'] = "newholiday[date_end]";
		$input['id'] = 'new_date_end';
		$input['value'] = set_value('newholiday[date_end]');
		$input['size'] = 15;
		$input['maxlength'] = 10;
		$input['class'] = 'date';
		$input['tabindex'] = $t;
		echo form_input($input);
		$t++;
		?></td>
		
		<td class="il" width="75">&nbsp;</td>
	</tr>
	
	<tr>
		<td width="20">&nbsp;</td>
		<td colspan="4">
			<div class="buttons">
				<?php if($holidays != 0){
					$savetext = 'Save';
					$saveimg = 'disk1.gif';
				} else {
					$savetext = 'Add';
					$saveimg = 'plus.gif';
				}
				?>
				<button type="submit" name="btn_submit" value="save" class="positive" tabindex="<?php echo $t; $t++; ?>">
				<img src="img/ico/<?php echo $saveimg ?>" alt="" width="16" height="16" /><?php echo $savetext ?></button>
				
				<?php if($holidays != 0){ ?>
				<button type="submit" name="btn_delete" value="delete" class="negative" tabindex="<?php echo $t ?>">
				<img src="img/ico/cross.gif" alt="" width="16" height="16" />Delete selected</button>
				<?php } ?>
			</div>
		</td>
	</tr>
	
	</form>
	</tbody>
</table>




<script type="text/javascript">
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


// Set up calendars on all .date input fields
document.observe('dom:loaded', function(){
	$$('input.date').each(function(el){
		Calendar.setup({
			dateField:el.id,
			triggerElement:el.id
		});
	});
});
</script>
</script>