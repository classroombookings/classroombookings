<?php
$errors = validation_errors();
if($errors){
	echo $this->msg->err('<ul>' . $errors . '</ul>', 'Please check the following invalid item(s) and try again.');
}

echo form_open('academic/weeks/save', NULL, array('week_id' => $week_id));

// Start tabindex
$t = 1;
?>

<table class="form" cellpadding="6" cellspacing="0" border="0" width="100%">
	
	<tr class="h"><td colspan="2">Basic details</td></tr>
	
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
			$input['size'] = '25';
			$input['maxlength'] = '20';
			$input['tabindex'] = $t;
			$input['value'] = @set_value('name', $week->name);
			echo form_input($input);
			$t++;
			?>
		</td>
	</tr>
	
	<tr>
		<td class="caption">
			<label for="colour" accesskey="C"><u>C</u>olour</label>
		</td>
		<td class="field">
			<?php
			unset($input);
			$input['accesskey'] = 'C';
			$input['name'] = 'colour';
			$input['id'] = 'colour';
			$input['size'] = '10';
			$input['maxlength'] = '7';
			$input['tabindex'] = $t;
			$input['value'] = @set_value('colour', $week->colour);
			echo form_input($input);
			$t++;
			?>
			<script type="text/javascript"><!--
			new Control.ColorPicker('colour');
			// -->
			</script>
			<?php /* <div id="cp"></div>
			<script type="text/javascript"><!--
			$(document).ready(function(){
				$('#cp').colorPicker({
					activeColour: '<?php echo $input['value'] ?>',
					click: function(c){$('#colour').val(c);}
				});
				$('#colour').css("display", "none");
			});
			// --></script> */ ?>
		</td>
	</tr>
	
	<tr class="h"><td colspan="2">Week dates</td></tr>
	
	<tr>
		<?php
		echo $calendar;
		?>
	</tr>
	
	<?php
	if($week_id == NULL){
		$submittext = 'Add week';
	} else {
		$submittext = 'Save week';
	}
	unset($buttons);
	$buttons[] = array('submit', 'positive', $submittext, 'disk1.gif', $t);
	#$buttons[] = array('submit', '', 'Save and add another', 'add.gif', $t+1);
	$buttons[] = array('cancel', 'negative', 'Cancel', 'arr-left.gif', $t+2, site_url('academic/weeks'));
	$this->load->view('parts/buttons', array('buttons' => $buttons));
	?>
	
</table>
</form>