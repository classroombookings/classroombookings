<?php
// Load errors
#echo $this->validation->error_string;

echo form_open(
	'configure/save',
	array('id' => 'conf-main')
);

// Start tabindex
$t = 1;
?>


<table class="form" cellpadding="6" cellspacing="0" border="0" width="99%">
	<tr>
		<td class="caption"><label for="schoolname" class="r" accesskey="N">School <u>n</u>ame</label></td>
		<td class="field">
		  <?php
			#$ = @field($this->validation->username);
			echo form_input(array(
				'accesskey' => 'N',
				'name' => 'schoolname',
				'id' => 'schoolname',
				'size' => '40',
				'maxlenght' => '30',
				'tabindex' => $t,
				'value' => set_value('schoolname'),
			));
			$t++;
			?>
		</td>
	</tr>
	
	
	<tr>
		<td class="caption"><label for="schoolname" class="r" accesskey="W"><u>W</u>ebsite address</label></td>
		<td class="field">
		  <?php
			#$ = @field($this->validation->username);
			echo form_input(array(
				'accesskey' => 'W',
				'name' => 'url',
				'id' => 'url',
				'size' => '50',
				'maxlenght' => '100',
				'tabindex' => $t,
				'value' => set_value('url'),
			));
			$t++;
			?>
		</td>
	</tr>
	
	
	<tr>
		<td class="caption">&nbsp;</td>
		<td class="field">
			<label for="remember" class="check">
			<?php
				echo form_checkbox(array(
				'name' => 'remember',
				'id' => 'remember',
				'value' => 'true',
				'checked' => FALSE,
				));
				$t++;
			?>
			Remember me on this computer
			</label>
		</td>
	</tr>
	
	
	<?php
	$submit['submit'] = array('Save main settings', $t);
	$submit['cancel'] = array('Cancel', $t+1, site_url());
	$this->load->view('parts/submit', $submit);
	echo form_close();
	?>
	

</table>