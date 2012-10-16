<?php
$errors = validation_errors();
if ($errors)
{
  echo '<div class="row">';
  echo $this->msg->err('<ul class="square">' . $errors . '</ul>', 'Please check the following invalid item(s) and try again.');
  echo '</div>';
}
?>

<?php
echo form_open('authentication/save_preauth');

// Start tabindex
$t = 1;
?>



<div class="alpha three columns"><h6>Options</h6></div>

<div class="omega nine columns">

	<label for="auth_preauth_groupid">Default Classroombookings group</label>
	<?php
	echo form_dropdown(
		'auth_preauth_groupid', 
		$groups, 
		set_value(
			'auth_preauth_groupid', 
			(isset($settings['auth_preauth_groupid'])) ? $settings['auth_preauth_groupid'] : 0
		), 
		'tabindex="' . $t . '" id="auth_preauth_groupid"'
	);
	$t++;
	?>
	
	<label for="school_name">Email domain</label>
	<?php
	unset($input);
	$input['accesskey'] = 'E';
	$input['name'] = 'auth_preauth_emaildomain';
	$input['id'] = 'auth_preauth_emaildomain';
	$input['size'] = '60';
	$input['maxlength'] = '100';
	$input['tabindex'] = $t;
	$input['value'] = set_value($input['name'], $settings['auth_preauth_emaildomain']);
	$input['class'] = 'remove-bottom';
	echo form_input($input);
	$t++;
	?>
	<p class="hint add-bottom">Default email address domain applied to new users created via preauthentication.</p>
	
</div>


<hr>


<div class="alpha three columns"><h6>Your key</h6></div>

<div class="omega nine columns">

	<?php echo $settings['auth_preauth_key'] ?>

</div>


<hr>


<div class="row">
<div class="alpha three columns">&nbsp;</div>
<div class="omega nine columns"><?php

$save = 'Save pre-authentication settings';
$newkey = 'Generate new key';

echo form_hidden('action_save', $save);
echo form_hidden('action_newkey', $newkey);

unset($buttons);
$buttons[] = array('submit', 'blue', $save, $t);
$buttons[] = array('submit', 'green', $newkey, $t);
$this->load->view('parts/buttons', array('buttons' => $buttons));
?></div>
</div>


</form>