<?php

// ===== Server settings

$section = 'server';

$this->form->add_section($section, lang('authentication_ldap_server'), lang('authentication_ldap_server_hint'));
	
	
	// ----- Server hostname

	$name = 'auth_ldap_host';
	
	$this->form->add_input(array(
		
		'section' => $section,
		'name' => $name,
		'label' => lang('authentication_ldap_host'),
		
		'content' => form_input(array(
			'name' => $name,
			'id' => $name,
			'class' => 'text-input',
			'size' => 30,
			'max_length' => 100,
			'tabindex' => tab_index(),
			'value' => set_value($name, element($name, $settings)),
		)),
		
	));
	
	
	// ----- Port number
	
	$name = 'auth_ldap_port';
	
	$this->form->add_input(array(
		
		'section' => $section,
		'name' => $name,
		'label' => lang('authentication_ldap_port'),
		
		'content' => form_input(array(
			'name' => $name,
			'id' => $name,
			'class' => 'text-input',
			'size' => 5,
			'max_length' => 5,
			'tabindex' => tab_index(),
			'value' => set_value($name, element($name, $settings, '389')),
		)),
		
	));
	
	
	// ---- Base DNs
	
	$name = 'auth_ldap_base';
	
	$this->form->add_input(array(
		
		'section' => $section,
		'name' => $name,
		'label' => lang('authentication_ldap_base'),
		'hint' => lang('authentication_ldap_base_hint'),
		
		'content' => form_textarea(array(
			'name' => $name,
			'id' => $name,
			'class' => 'text-input',
			'rows' => 10,
			'cols' => 60,
			'tabindex' => tab_index(),
			'value' => set_value($name, element($name, $settings)),
		)),
		
	));
	
	
	// ----- Query filter
	
	$name = 'auth_ldap_filter';
	$default = '(& (| (!(displayname=Administrator*)) (!(displayname=Admin*)) ) (cn=%u) )';
	
	$this->form->add_input(array(
		
		'section' => $section,
		'name' => $name,
		'label' => lang('authentication_ldap_filter'),
		'hint' => lang('authentication_ldap_filter_hint'),
		
		'content' => form_textarea(array(
			'name' => $name,
			'id' => $name,
			'class' => 'text-input half-bottom',
			'rows' => 10,
			'cols' => 60,
			'tabindex' => tab_index(),
			'value' => set_value($name, element($name, $settings, $default)),
		)),
		
	));
	
	
// ===== Test settings

$section = 'test';

$this->form->add_section($section, lang('authentication_ldap_test'), lang('authentication_ldap_test_hint'));
	
	// ----- Username
	
	$name = 'ldap_test_username';
	
	$this->form->add_input(array(
		
		'section' => $section,
		'name' => $name,
		'label' => lang('username'),
		
		'content' => form_input(array(
			'name' => $name,
			'id' => $name,
			'class' => 'text-input',
			'size' => 30,
			'max_length' => 100,
			'autocomplete' => 'off',
			'tabindex' => tab_index(),
			'value' => set_value($name),
		)),
		
	));
	
	
	// ----- Password
	
	$name = 'ldap_test_password';
	
	$this->form->add_input(array(
		
		'section' => $section,
		'name' => $name,
		'label' => lang('username'),
		
		'content' => form_password(array(
			'name' => $name,
			'id' => $name,
			'class' => 'text-input',
			'size' => 30,
			'max_length' => 100,
			'autocomplete' => 'off',
			'tabindex' => tab_index(),
		)),
		
	));
	
	
	$name = 'ldap_test_button';
	
	$this->form->add_input(array(
		'section' => $section,
		'name' => $name,
		'content' => form_button(array(
			'type' => 'button',
			'id' => 'test_ldap',
			'class' => 'small grey half-bottom',
			'text' => 'Test',
			'tab_index' => tab_index(),
		)),
	));
	
	
	$extra = '<div id="ldap_test_response" class="box">
					<h6>LDAP server response</h6>
					<p class="response-text"></p>
				</div>';
	
	$this->form->add_section_extra($section, $extra);
	
	
// ===== Integration

$section = 'integration';

$this->form->add_section($section, lang('authentication_ldap_integration'));

	// ----- Default Group ID
	
	$name = 'auth_ldap_g_id';
	
	$this->form->add_input(array(
		
		'section' => $section,
		'name' => $name,
		'label' => lang('authentication_ldap_default_group'),
		'hint' => lang('authentication_ldap_default_group_hint'),
		
		'content' => form_dropdown(
			$name, 
			$groups, 
			set_value($name, element($name, $settings)), 
			'tabindex="' . tab_index() . '" id="' . $name . '"'
		),
		
	));
	
	
	// ----- Update details on every login
	
	$name = 'auth_ldap_update';
	$value = '1';
	
	$content = form_hidden($name, '0');
	$content .= '<label class="check">';
	$content .= form_checkbox(array(
		'name' => $name,
		'id' => $name . '_1',
		'tabindex' => tab_index(),
		'value' => $value,
		'checked' => set_checkbox($name, $value, (element($name, $settings) == $value)),
	));
	$content .= lang('authentication_ldap_update_label');
	$content .= '</label>';
	
	
	$this->form->add_input(array(
		'section' => $section,
		'name' => $name,
		'label' => lang('authentication_ldap_update'),
		'hint' => lang('authentication_ldap_update_hint'),
		'content' => $content,
	));
	
	
$this->form->add_button(form_button(array(
	'type' => 'submit',
	'class' => 'primary',
	'text' => lang('save'),
	'tab_index' => tab_index(),
)));


echo form_open(current_url(), array('id' => 'auth_ldap_form'));
echo $this->form->render();
echo '</form>';