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
	echo form_open('academic/terms/save');
	
	$i = 0;
	$t = 1;
	
	if($terms != 0){
		foreach($terms as $term){
		
		echo form_hidden('term_ids[]]', $term->term_id);
		
		echo form_hidden("term[{$term->term_id}][term_id]", $term->term_id);
		
		?>
		<tr>
			
			<td align="center" width="20"><?php
			unset($check);
			$check['name'] = "term[{$term->term_id}][delete]";
			$check['id'] = "delete_{$term->term_id}";
			$check['value'] = '1';
			$check['tabindex'] = $t;
			$check['title'] = 'Tick this box to allow deletion of multiple terms at once.';
			$t++;
			echo form_checkbox($check);
			?></td>
			
			<td class="field"><?php
			unset($input);
			$input['name'] = "term[{$term->term_id}][name]";
			$input['id'] = "name_{$term->term_id}";
			$input['value'] = $term->name;
			$input['size'] = 25;
			$input['maxlength'] = 50;
			$input['tabindex'] = $t;
			echo form_input($input);
			$t++;
			?></td>
			
			<td class="field"><?php
			unset($input);
			$input['name'] = "term[{$term->term_id}][date_start]";
			$input['id'] = "date_start_{$term->term_id}}";
			$input['value'] = $term->date_start;	#date("l jS F Y", todate($term->date_start));
			$input['size'] = 15;
			$input['maxlength'] = 10;
			$input['class'] = 'date';
			$input['tabindex'] = $t;
			echo form_input($input);
			$t++;
			?></td>
			
			<td class="field"><?php
			unset($input);
			$input['name'] = "term[{$term->term_id}][date_end]";
			$input['id'] = "date_end_{$term->term_id}}";
			$input['value'] = $term->date_end;	#date("l jS F Y", todate($term->date_start));
			$input['size'] = 15;
			$input['maxlength'] = 10;
			$input['class'] = 'date';
			$input['tabindex'] = $t;
			echo form_input($input);
			$t++;
			?></td>
			
			<td class="il" width="150">
			<?php
			$actiondata[] = array('academic/terms/delete/'.$term->term_id, 'Delete', 'cross_sm.gif');
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
		$input['name'] = "newterm[name]";
		$input['id'] = 'new_name';
		$input['value'] = set_value('newterm[name]');
		$input['size'] = 25;
		$input['maxlength'] = 50;
		$input['tabindex'] = $t;
		echo form_input($input);
		$t++;
		?></td>
		
		<td class="field"><?php
		unset($input);
		$input['name'] = "newterm[date_start]";
		$input['id'] = 'new_date_start';
		$input['value'] = set_value('newterm[date_start]');
		$input['size'] = 15;
		$input['maxlength'] = 10;
		$input['class'] = 'date';
		$input['tabindex'] = $t;
		echo form_input($input);
		$t++;
		?></td>
		
		<td class="field"><?php
		unset($input);
		$input['name'] = "newterm[date_end]";
		$input['id'] = 'new_date_end';
		$input['value'] = set_value('newterm[date_end]');
		$input['size'] = 15;
		$input['maxlength'] = 10;
		$input['class'] = 'date';
		$input['tabindex'] = $t;
		echo form_input($input);
		$t++;
		?></td>
		
		<td class="il" width="150">&nbsp;</td>
	</tr>
	
	<tr>
		<td width="20">&nbsp;</td>
		<td colspan="4">
			<div class="buttons">
				<?php if($terms != 0){
					$savetext = 'Save';
					$saveimg = 'disk1.gif';
				} else {
					$savetext = 'Add';
					$saveimg = 'plus.gif';
				}
				?>
				<button type="submit" name="btn_submit" value="save" class="positive" tabindex="<?php echo $t; $t++; ?>">
				<img src="img/ico/<?php echo $saveimg ?>" alt="" width="16" height="16" /><?php echo $savetext ?></button>
				
				<?php if($terms != 0){ ?>
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
$.extend(DateInput.DEFAULT_OPTS, {
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
});
</script>