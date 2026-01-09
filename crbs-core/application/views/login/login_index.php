<?php
if (!empty($message)) {
	echo "<div>" . nl2br((string) $message) . "</div><br><br>";
}

echo $error ?? '';

echo validation_errors();

echo form_open(current_url(), array('id'=>'login','class'=>'cssform'), array('page' => $this->uri->uri_string()) );

?>


<fieldset style="width:336px;"><legend accesskey="L" tabindex="<?php echo tab_index() ?>"><?= lang('auth.log_in') ?></legend>

	<p>
	  <label for="username" class="required"><?= lang('user.field.username') ?></label>
	  <?php
		$value = set_value('username', '', FALSE);
		echo form_input(array(
			'name' => 'username',
			'id' => 'username',
			'size' => '20',
			'maxlength' => '255',
			'tabindex' => tab_index(),
			'value' => $value,
		));
		?>
	</p>


	<p>
	  <label for="password" class="required"><?= lang('user.field.password') ?></label>
	  <?php
		echo form_password(array(
			'name' => 'password',
			'id' => 'password',
			'size' => '20',
			'tabindex' => tab_index(),
		));
		?>
	</p>

</fieldset>


<?php

$this->load->view('partials/submit', array(
	'submit' => array(lang('auth.log_in'), tab_index()),
));

echo form_close();
