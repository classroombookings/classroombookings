<h2>Log in</h2>

<?php
if (strlen($message)) {
	echo "<div>" . nl2br($message) . "</div><br><br>";
}

echo $this->session->flashdata('auth');

echo validation_errors();

echo form_open('login/submit', array('id'=>'login','class'=>'cssform'), array('page' => $this->uri->uri_string()) );

?>


<fieldset style="width:336px;"><legend accesskey="L" tabindex="<?php echo tab_index() ?>">Log in</legend>

	<p>
	  <label for="username" class="required">Username</label>
	  <?php
		$value = set_value('username', '', FALSE);
		echo form_input(array(
			'name' => 'username',
			'id' => 'username',
			'size' => '20',
			'maxlength' => '20',
			'tabindex' => tab_index(),
			'value' => $value,
		));
		?>
	</p>


	<p>
	  <label for="password" class="required">Password</label>
	  <?php
		echo form_password(array(
			'name' => 'password',
			'id' => 'password',
			'size' => '20',
			'tabindex' => tab_index(),
			'maxlength' => '20',
		));
		?>
	</p>

</fieldset>


<?php

$this->load->view('partials/submit', array(
	'submit' => array('Login', tab_index()),
));

echo form_close();
