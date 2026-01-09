<p><?= lang('account.password.intro.1') ?></p>
<br>

<?php

echo $this->session->flashdata('saved');

echo form_open('profile/new_password', array('class' => 'cssform', 'id' => 'new_password'));

?>


<fieldset>



	<p>
	  <label for="password1">Password</label>
	  <?php
		echo form_password(array(
			'name' => 'password1',
			'id' => 'password1',
			'size' => '20',
			'tabindex' => tab_index(),
			'value' => '',
		));
		?>
	</p>
	<p class="hint"><?= lang('user.field.password.hint') ?></p>
	<?php echo form_error('password1'); ?>


	<p>
	  <label for="password2">Password (again)</label>
	  <?php
		echo form_password(array(
			'name' => 'password2',
			'id' => 'password2',
			'size' => '20',
			'tabindex' => tab_index(),
			'value' => '',
		));
		?>
	</p>
	<?php echo form_error('password2'); ?>

</fieldset>


<?php
$this->load->view('partials/submit', array(
	'submit' => array('Change password', tab_index()),
));

echo form_close();
