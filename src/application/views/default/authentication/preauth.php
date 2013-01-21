<?php echo form_open('authentication/save_preauth', array('id' => 'auth_preauth_form')) ?>

	<fieldset class="form clearfix">
		
		<legend>Options</legend>
		
		<div class="inputs">
			
			<?php $name = 'auth_preauth_g_id' ?>
			<label for="<?php echo $name ?>">Default Classroombookings group</label>
			<?php
			echo form_dropdown(
				$name,
				$groups, set_value($name, element($name, $settings)),
				'tabindex="' . tab_index() . '" id="' . $name . '"'
			);
			?>
			
			<?php
			$name = 'auth_preauth_email_domain';
			$default_parts = explode('@', $this->session->userdata('u_email'));
			$default = end($default_parts);
			?>
			<label for="<?php echo $name ?>">Email domain</label>
			<?php echo form_input(array(
				'name' => $name,
				'id' => $name,
				'size' => 30,
				'maxlength' => 100,
				'class' => 'text-input half-bottom',
				'value' => set_value($name, element($name, $settings, $default)),
			)) ?>
			<p class="hint light">Email domain appended to the username of 
			authenticated users to generate their email address. No <strong>@</strong>.</p>
			<br>
			
		</div>
		
	</fieldset>
	
	
	<fieldset class="form clearfix">
		
		<legend>Your key</legend>
		
		<div class="inputs">
		
			<?php echo element('auth_preauth_key', $settings, '(Not set)') ?>
			<br><br>
			<?php echo form_button(array(
				'type' => 'link',
				'url' => 'authentication/save_preauth?new_key=1',
				'class' => 'small grey',
				'text' => 'Generate new key',
				'tab_index' => tab_index(),
			)) ?>
			<br><br>
			
		</div>
		
	</fieldset>
	
	
	<fieldset class="form clearfix actions">
		<?php echo form_button(array(
			'type' => 'submit',
			'class' => 'blue',
			'text' => lang('ACTION_SAVE') . ' pre-authentication ' . strtolower(lang('W_SETTINGS')),
			'tab_index' => tab_index(),
		)) ?>
	</fieldset>

</form>

<!--
	
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

-->