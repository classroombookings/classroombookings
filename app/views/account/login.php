<div class="row">

<?php
$foo = validation_errors();
if($foo)
{
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

<div class="one-third column">&nbsp;</div>

<div class="one-third column">

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
		$buttons[] = array('submit', 'blue', lang('LOGIN'), $t);
		$this->load->view('parts/buttons', array('buttons' => $buttons));
		?>

	</div>
	
</div>

<div class="one-third column">&nbsp;</div>

</div>