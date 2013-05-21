<?php
$t = 1;
echo form_open('install/submit', array('id' => 'install', 'class' => 'cssform') );
?>




<fieldset><legend accesskey="M" tabindex="<?php echo $t; $t++; ?>">Database information</legend>
	The script will be installed with the following database configuration:<br /><br />
	<table cellpadding="5">
		<tr><td width="140"><strong>Hostname:</strong></td><td><?php echo $db['hostname'] ?></td></tr>
		<tr><td width="140"><strong>Username:</strong></td><td><?php echo $db['username'] ?></td></tr>
		<tr><td width="140"><strong>Password:</strong></td><td><?php echo $db['password'] ?></td></tr>
		<tr><td width="140"><strong>Database:</strong></td><td><?php echo $db['database'] ?></td></tr>
	</table>
</fieldset>




<fieldset><legend accesskey="I" tabindex="<?php echo $t; $t++; ?>">School Information</legend>
	<p>
	  <label for="schoolname" class="required">School name</label>
	  <?php
		$schoolname = @field($this->validation->schoolname, NULL);
		echo form_input(array(
			'name' => 'schoolname',
			'id' => 'schoolname',
			'size' => '30',
			'maxlength' => '255',
			'tabindex' => $t,
			'value' => $schoolname,
		));
		$t++;
		?>
	</p>
	<?php echo @field($this->validation->schoolname_error); ?>
	
	
	<p>
	  <label for="website">Website address</label>
	  <?php
		$website = @field($this->validation->website, NULL, 'http://');
		echo form_input(array(
			'name' => 'website',
			'id' => 'website',
			'size' => '30',
			'maxlength' => '255',
	    'tabindex' => $t,
			'value' => $website,
		));
		$t++;
		?>
	</p>
	<?php echo @field($this->validation->website_error); ?>
</fieldset>




<fieldset><legend accesskey="A" tabindex="5">Administrator Account</legend>
	<p>
	  <label for="username" class="required">Username</label>
	  <?php
		$username = @field($this->validation->username, NULL);
		echo form_input(array(
			'name' => 'username',
			'id' => 'username',
			'size' => '15',
			'maxlength' => '20',
			'tabindex' => $t,
			'value' => $username,
		));
		$t++;
		?><p class="hint">Between 4 and 20 characters</p>
	</p>
	<?php echo @field($this->validation->username_error); ?>
	
	
	<p>
	  <label for="password1" class="required">Password</label>
	  <?php
	  $password1 = @field($this->validation->password1, NULL);
		echo form_password(array(
			'name' => 'password1',
			'id' => 'password1',
			'size' => '15',
			'maxlength' => '20',
			'tabindex' => $t,
			'value' => $password1,
		));
		$t++;
		?><p class="hint">Between 6 and 20 characters</p>
	</p>
	<?php echo @field($this->validation->password1_error); ?>
	
	
	<p>
	  <label for="password2" class="required">Password (again)</label>
	  <?php
	  $password2 = @field($this->validation->password2, NULL);
		echo form_password(array(
			'name' => 'password2',
			'id' => 'password2',
			'size' => '15',
			'maxlength' => '20',
			'tabindex' => $t,
			'value' => $password2,
		));
		$t++;
		?>
	</p>
	<?php echo @field($this->validation->password2_error); ?>
	
	
	<p>
	  <label for="email">Email address</label>
	  <?php
		$email = @field($this->validation->email, NULL);
		echo form_input(array(
			'name' => 'email',
			'id' => 'email',
			'size' => '30',
			'maxlength' => '100',
			'tabindex' => $t,
			'value' => $email,
		));
		$t++;
		?>
	</p>
	<?php echo @field($this->validation->email_error); ?>
</fieldset>




<?php
$submit['submit'] = array('Install', $t);
$submit['cancel'] = array('Cancel', $t+1, 'rooms');
$this->load->view('partials/submit', $submit);
echo form_close();
?>
