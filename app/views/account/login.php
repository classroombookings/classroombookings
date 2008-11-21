<?php
// Load errors
#echo $this->validation->error_string;

echo form_open(
	'account/loginsubmit',
	array('id' => 'login'),
	array('uri' => $this->session->userdata('uri'))
);

// Start tabindex
$t = 1;
?>


<?php
$foo = validation_errors();
if($foo){
	echo $this->msg->err('<ul>' . $foo . '</ul>', 'Form field validation error');
}
?>


<table class="form" cellpadding="6" cellspacing="0" border="0">
	<tr>
		<td class="caption"><label for="username" class="r" accesskey="U"><u>U</u>sername</label></td>
		<td class="field">
		  <?php
			$v_username = @field($this->validation->username);
			echo form_input(array(
				'accesskey' => 'U',
				'name' => 'username',
				'id' => 'username',
				'size' => '30',
				'maxlenght' => '30',
				'tabindex' => $t,
				'value' => $v_username,
			));
			$t++;
			?>
		</td>
	</tr>
	
	
	<tr>
		<td class="caption"><label for="password" class="r" accesskey="P"><u>P</u>assword</label></td>
		<td class="field">
		  <?php
			echo form_password(array(
				'name' => 'password',
				'id' => 'password',
				'size' => '30',
				'maxlength' => '30',
				'tabindex' => $t,
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
	$submit['submit'] = array('Login', $t);
	$submit['cancel'] = array('Cancel', $t+1, site_url());
	$this->load->view('parts/submit', $submit);
	echo form_close();
	?>
	

</table>