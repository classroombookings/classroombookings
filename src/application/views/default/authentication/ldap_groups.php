<?php

// ===== Server settings

$section = 'ldap_groups';

$this->form->add_section($section, lang('authentication_ldap_groups'), lang('authentication_ldap_groups_hint'));

$current_groups = '<h3 class="sub-heading">' . lang('authentication_ldap_groups_current') . '</h3>';
$current_groups .= '<p><strong>Total: </strong>' . count($ldap_groups) . '</p>';

if ($ldap_groups)
{
	$current_groups .= '<ul class="ldap-groups">';
	foreach ($ldap_groups as $group)
	{
		$current_groups .= '<li title="' . $group['lg_description'] . '">' . $group['lg_name'] . '</li>'."\n";
	}
	$current_groups .= '</ul>';
}

$this->form->add_section_extra($section, $current_groups);


	// ----- Hostname and port
	
	$name = 'auth_ldap_groups_hostport';
	
	$this->form->add_input(array(
		
		'section' => $section,
		'name' => $name,
		'label' => lang('authentication_ldap_groups_hostport'),
		
		'content' => form_input(array(
			'name' => $name,
			'id' => $name,
			'class' => 'text-input',
			'size' => 30,
			'max_length' => 100,
			'tabindex' => tab_index(),
			'disabled' => 'disabled',
			'value' => element('auth_ldap_host', $settings, '(None)') . ':' . element('auth_ldap_port', $settings, '(0)'),
		)),
		
	));
	
	
	// ---- Base DNs
	
	$name = 'auth_ldap_base';
	
	$this->form->add_input(array(
		
		'section' => $section,
		'name' => $name,
		'label' => lang('authentication_ldap_base'),
		
		'content' => form_textarea(array(
			'name' => $name,
			'id' => $name,
			'class' => 'text-input',
			'rows' => 10,
			'cols' => 60,
			'tabindex' => tab_index(),
			'value' => set_value($name),		//, element($name, $settings)),
		)),
		
	));
	
	
	// ----- Username
	
	$name = 'username';
	
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
			'tabindex' => tab_index(),
			'autocomplete' => 'off',
			'value' => set_value($name),
		)),
		
	));
	
	
	// ----- Pasword
	
	$name = 'password';
	
	$this->form->add_input(array(
		
		'section' => $section,
		'name' => $name,
		'label' => lang('password'),
		
		'content' => form_password(array(
			'name' => $name,
			'id' => $name,
			'class' => 'text-input',
			'size' => 30,
			'max_length' => 100,
			'tabindex' => tab_index(),
			'autocomplete' => 'off',
			'value' => '',
		)),
		
	));
	
	
	// ----- Mode
	
	$name = 'mode';
	
	$content = '';
	
	$value = 'sync';
	$content .= '<label class="radio">';
	$content .= form_radio(array(
		'name' => $name,
		'id' => $name . '_day',
		'tabindex' => tab_index(),
		'value' => $value,
		'checked' => set_radio($name, $value),
	));
	$content .= lang('authentication_ldap_groups_mode_sync');
	$content .= '</label>';
	
	$value = 'reload';
	$content .= '<label class="radio">';
	$content .= form_radio(array(
		'name' => $name,
		'id' => $name . '_room',
		'tabindex' => tab_index(),
		'value' => $value,
		'checked' => set_radio($name, $value),
	));
	$content .= lang('authentication_ldap_groups_mode_reload');
	$content .= '</label><br><br>';
	
	
	$this->form->add_input(array(
		'section' => $section,
		'name' => $name,
		'label' => lang('authentication_ldap_groups_mode'),
		'content' => $content,
	));
	
	
	// ----- Submit
	
	$name = 'submit';
	
	$this->form->add_input(array(
		'section' => $section,
		'name' => $name,
		'content' => form_button(array(
			'type' => 'submit',
			'class' => 'blue',
			'text' => lang('authentication_ldap_groups_get'),
			'tab_index' => tab_index(),
		)),
	));

echo form_open(current_url(), array('id' => 'auth_ldap_groups_form'));
echo $this->form->render();
echo '</form>';

?>