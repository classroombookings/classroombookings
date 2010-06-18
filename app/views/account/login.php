<?php
$foo = validation_errors();
if($foo){
	echo $this->msg->err('<ul>' . $foo . '</ul>', 'Form field validation error');
}

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
		<td class="field"><?php
			unset($input);
			$input['accesskey'] = 'U';
			$input['name'] = 'username';
			$input['id'] = 'usernamename';
			$input['size'] = '30';
			$input['maxlength'] = '104';
			$input['tabindex'] = $t;
			$input['value'] = @set_value('username');
			echo form_input($input);
			$t++;
		?></td>
	</tr>
	
	
	<tr>
		<td class="caption"><label for="password" class="r" accesskey="P"><u>P</u>assword</label></td>
		<td class="field"><?php
			unset($input);
			$input['accesskey'] = 'P';
			$input['name'] = 'password';
			$input['id'] = 'password';
			$input['size'] = '30';
			$input['maxlength'] = '104';
			$input['tabindex'] = $t;
			echo form_password($input);
			$t++;
		?></td>
	</tr>
	
	
	<tr>
		<td class="caption">&nbsp;</td>
		<td class="field">
			<label for="remember" class="check">
			<?php
			unset($check);
			$check['name'] = 'remember';
			$check['id'] = 'remember';
			$check['value'] = '1';
			$check['checked'] = FALSE;
			$check['tabindex'] = $t;
			echo form_checkbox($check);
			?>
			Remember me on this computer
			</label>
		</td>
	</tr>
	
	<?php
	unset($buttons);
	$buttons[] = array('submit', 'positive', 'Login', 'key2.gif', $t);
	$this->load->view('parts/buttons', array('buttons' => $buttons));
	?>

</table>

</form>
