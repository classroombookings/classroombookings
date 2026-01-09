<?php

echo $this->session->flashdata('saved');

echo form_open('profile/save', array('class' => 'cssform', 'id' => 'profile_edit'));

?>


<fieldset>

	<legend accesskey="U" tabindex="<?php tab_index() ?>"><?= lang('account.details') ?></legend>

	<p>
	  <label for="email"><?= lang('user.field.email') ?></label>
	  <?php
		$email = set_value('email', $user->email, FALSE);
		echo form_input(array(
			'name' => 'email',
			'id' => 'email',
			'size' => '35',
			'maxlength' => '255',
			'tabindex' => tab_index(),
			'value' => $email,
		));
		?>
	</p>
	<?php echo form_error('email'); ?>


	<p>
	  <label for="password1"><?= lang('user.field.password') ?></label>
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
	  <label for="password2"><?= lang('user.field.password2') ?></label>
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


<fieldset>


	<p>
	  <label for="firstname"><?= lang('user.field.firstname') ?></label>
	  <?php
		$firstname = set_value('firstname', $user->firstname, FALSE);
		echo form_input(array(
			'name' => 'firstname',
			'id' => 'firstname',
			'size' => '20',
			'maxlength' => '100',
			'tabindex' => tab_index(),
			'value' => $firstname,
		));
		?>
	</p>
	<?php echo form_error('firstname'); ?>


	<p>
	  <label for="lastname"><?= lang('user.field.lastname') ?></label>
	  <?php
		$lastname = set_value('lastname', $user->lastname, FALSE);
		echo form_input(array(
			'name' => 'lastname',
			'id' => 'lastname',
			'size' => '20',
			'maxlength' => '100',
			'tabindex' => tab_index(),
			'value' => $lastname,
		));
		?>
	</p>
	<?php echo form_error('lastname'); ?>


	<p>
	  <label for="displayname"><?= lang('user.field.displayname') ?></label>
	  <?php
		$displayname = set_value('displayname', $user->displayname, FALSE);
		echo form_input(array(
			'name' => 'displayname',
			'id' => 'displayname',
			'size' => '20',
			'maxlength' => '100',
			'tabindex' => tab_index(),
			'value' => $displayname,
		));
		?>
	</p>
	<?php echo form_error('displayname'); ?>


	<p>
	  <label for="ext"><?= lang('user.field.ext') ?></label>
	  <?php
		$ext = set_value('ext', $user->ext, FALSE);
		echo form_input(array(
			'name' => 'ext',
			'id' => 'ext',
			'size' => '10',
			'maxlength' => '10',
			'tabindex' => tab_index(),
			'value' => $ext,
		));
		?>
	</p>
	<?php echo form_error('ext'); ?>


</fieldset>

<?php if ($can_change_lang): ?>
<fieldset>
	<p>
	  <label for="language"><?= lang('language.language') ?></label>
	  <?php
		$value = set_value('language', $user_settings['language'] ?: '', false);
		echo form_dropdown([
			'name' => 'language',
			'id' => 'language',
			'options' => array_merge(['' => sprintf('(%s)', lang('app.default'))], $language_options),
			'selected' => $value,
			'tabindex' => tab_index(),
		]);
		?>
	</p>
	<?php echo form_error('lastname'); ?>
</fieldset>
<?php endif; ?>

<?php
$this->load->view('partials/submit', array(
	'submit' => array(lang('app.action.save'), tab_index()),
));

echo form_close();
