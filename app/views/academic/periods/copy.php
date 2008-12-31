<?php
$errors = validation_errors();
if($errors){
	echo $this->msg->err('<ul>' . $errors . '</ul>', 'Please check the following invalid item(s) and try again.');
}

echo form_open('academic/periods/copy');

// Start tabindex
$t = 1;
?>

<table class="form" cellpadding="6" cellspacing="0" border="0" width="100%">
	
	<tr class="h"><td colspan="2">Copy periods from other academic year</td></tr>
	
	<tr>
		<td class="caption">
			<label for="name" class="r" accesskey="N">Academic <u>y</u>ear</label>
		</td>
		<td class="field">
			<select name="year_id" id="year_id">
			<?php
			foreach($years as $id => $name){
				if($this->session->userdata('year_working') != $id){
					echo sprintf('<option value="%d"%s>%s</option>', $id, $selected, $name);
				}
			}
			?>
			</select>
		</td>
	</tr>
	
	
	<?php
	unset($buttons);
	$buttons[] = array('submit', 'positive', 'Copy', 'disks.gif', $t);
	$this->load->view('parts/buttons', array('buttons' => $buttons));
	?>

</table>
</form>