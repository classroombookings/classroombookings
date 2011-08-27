<?php
$foo = validation_errors();
if($foo){
	echo $this->msg->err('Check form fields and try again.');
}

echo form_open(
	'account/loginsubmit',
	array('id' => 'login'),
	array('uri' => $this->session->userdata('uri'))
);

// Start tabindex
$t = 1;
?>

<h3 class="add-bottom"><?php echo lang('LOGIN') ?></h3>

	<div class="login-form">

		<label for="username"><?php echo lang('USERNAME') ?></label>
		<?php
		unset($input);
		$input['accesskey'] = 'U';
		$input['name'] = 'username';
		$input['id'] = 'usernamename';
		$input['size'] = '30';
		$input['maxlength'] = '104';
		$input['tabindex'] = $t;
		$input['value'] = set_value('username', '');
		echo form_input($input);
		$t++;
		?>

		<label for="password"><?php echo lang('PASSWORD') ?></label>
		<?php
		unset($input);
		$input['accesskey'] = 'P';
		$input['name'] = 'password';
		$input['id'] = 'password';
		$input['size'] = '30';
		$input['maxlength'] = '104';
		$input['tabindex'] = $t;
		echo form_password($input);
		$t++;
		?>

		<br>
		
		<?php
		unset($buttons);
		$buttons[] = array('submit', 'green', lang('LOGIN'), $t);
		$this->load->view('parts/buttons', array('buttons' => $buttons));
		?>

	</div>

<?php
/*

<div class="grey" style="width:50%;margin:40px auto 0 auto;"><div>
<table class="form">
	<tr>
		<td class="caption"><label for="username" class="r" accesskey="U">Username</label></td>
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
		<td class="caption"><label for="password" class="r" accesskey="P">Password</label></td>
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
	
</table>
</div></div>

<div style="width:50%;margin:0 auto;">
<table class="form">
	<?php
	unset($buttons);
	$buttons[] = array('submit', 'ok', lang('LOGIN'), $t);
	$this->load->view('parts/buttons', array('buttons' => $buttons));
	?>
</table>
</div>

</form>

*/
?>