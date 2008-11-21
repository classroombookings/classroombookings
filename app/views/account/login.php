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
				'size' => '20',
				'maxlenght' => '20',
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
				'size' => '20',
				'maxlength' => '20',
				'tabindex' => $t,
			));
			$t++;
			?>
		</td>
	</tr>
	
	
	<?php
	$submit['submit'] = array('Login', $t);
	$submit['cancel'] = array('Cancel', $t+1, site_url());
	$this->load->view('parts/submit', $submit);
	echo form_close();
	?>
	

</table>