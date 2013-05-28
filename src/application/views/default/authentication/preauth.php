<?php

// ===== Key

$section = 'key';

$this->form->add_section($section, lang('authentication_preauth_your_key'));

	// ----- Display key
	$name = 'key';
	$this->form->add_input(array(
		'section' => $section,
		'name' => $name,
		'content' => element('auth_preauth_key', $settings) . '<br><br>',
	));
	
	// ----- Generate new key

	$name = 'new_key';
	
	$this->form->add_input(array(
		'section' => $section,
		'name' => $name,
		'content' => form_hidden($name, '1') . form_button(array(
			'type' => 'submit',
			'id' => 'new_key',
			'class' => 'grey half-bottom',
			'text' => lang('authentication_preauth_new_key'),
			'tab_index' => tab_index(),
		)),
	));
	
echo form_open(current_url(), array('id' => 'auth_preauth_new_key_form'));
echo $this->form->render();
echo '</form>';




$this->form->clear();




// ===== Defaults

$section = 'defaults';

$this->form->add_section($section, lang('authentication_preauth_defaults'), lang('authentication_preauth_defaults_hint'));

	// ----- Default Group ID
	
	$name = 'auth_preauth_g_id';
	
	$default_groups = array('' => lang('authentication_preauth_no_create'));
	$default_groups += $groups;
	
	$this->form->add_input(array(
		
		'section' => $section,
		'name' => $name,
		'label' => lang('authentication_preauth_default_group'),
		
		'content' => form_dropdown(
			$name, 
			$default_groups, 
			set_value($name, element($name, $settings)), 
			'tabindex="' . tab_index() . '" id="' . $name . '"'
		),
		
	));
	
	
	// ----- Email domain
	
	$name = 'auth_preauth_email_domain';
	$default = end(explode('@', $this->session->userdata('u_email')));
	
	$this->form->add_input(array(
		
		'section' => $section,
		'name' => $name,
		'label' => lang('authentication_preauth_email_domain'),
		'hint' => lang('authentication_preauth_email_domain_hint'),
		
		'content' => form_input(array(
			'name' => $name,
			'id' => $name,
			'class' => 'text-input',
			'size' => 30,
			'max_length' => 100,
			'tabindex' => tab_index(),
			'value' => set_value($name, element($name, $settings), $default),
		)),
		
	));
	
	
$this->form->add_button(form_button(array(
	'type' => 'submit',
	'class' => 'primary',
	'text' => lang('save'),
	'tab_index' => tab_index(),
)));




echo form_open(current_url(), array('id' => 'auth_preauth_form'));
echo $this->form->render();
echo '</form>';

?>



<script>
</script>