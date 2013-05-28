<?php

// ===== User details

$section = 'details';

$this->form->add_section($section, lang('users_user_details'));

	// ----- Username
	
	$name = 'u_username';
	
	$this->form->add_input(array(
		
		'section' => $section,
		'name' => $name,
		'label' => lang('users_username'),
		
		'content' => form_input(array(
			'name' => $name,
			'id' => $name,
			'class' => 'text-input',
			'size' => 30,
			'max_length' => 64,
			'tabindex' => tab_index(),
			'autocomplete' => 'off',
			'value' => set_value($name, element($name, $user)),
		)),
		
	));
	
	
	if ( ! $user || ($user['u_auth_method'] === 'local' && $this->auth->check('users.edit.password')))
	{
		// Password fields here only valid when creating a new account or existing user is local
		
		// ----- Password
		
		$name = 'password1';
		
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
				'autocomplete' => 'off',
				'tabindex' => tab_index(),
			)),
			
		));
		
		// ----- Password confirmation
		
		$name = 'password2';
		
		$this->form->add_input(array(
			
			'section' => $section,
			'name' => $name,
			'label' => lang('password_confirm'),
			
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
		
	}
	
	
	// ----- Display name
	
	$name = 'u_display';
	
	$this->form->add_input(array(
		
		'section' => $section,
		'name' => $name,
		'label' => lang('users_display'),
		'hint' => lang('users_display_hint'),
		
		'content' => form_input(array(
			'name' => $name,
			'id' => $name,
			'class' => 'text-input',
			'size' => 30,
			'max_length' => 64,
			'tabindex' => tab_index(),
			'autocomplete' => 'off',
			'value' => set_value($name, element($name, $user)),
		)),
		
	));
	
	
	// ----- Email address
	
	$name = 'u_email';
	
	$this->form->add_input(array(
		
		'section' => $section,
		'name' => $name,
		'label' => lang('users_email'),
		'hint' => lang('users_email_hint'),
		
		'content' => form_input(array(
			'name' => $name,
			'id' => $name,
			'class' => 'text-input',
			'size' => 50,
			'max_length' => 64,
			'tabindex' => tab_index(),
			'autocomplete' => 'off',
			'value' => set_value($name, element($name, $user)),
		)),
		
	));
	
	
	/*
	Not ready just yet.
	if ($this->auth->check('quota.set.user'))
	{
		// ===== Quota
		
		$section = 'quota';
		
		$this->form->add_section($section, lang('users_quota'));
		
	}
	*/
	
	
// ===== Options

$section = 'options';

$this->form->add_section($section, lang('users_options'));

	// ----- Enable account

	$name = 'u_enabled';
	$value = '1';
	
	$content = form_hidden($name, '0');
	$content .= '<label class="check">';
	$content .= form_checkbox(array(
		'name' => $name,
		'id' => $name . '_1',
		'tabindex' => tab_index(),
		'value' => $value,
		'checked' => set_checkbox($name, $value, (element($name, $user) == $value)),
	));
	$content .= lang('users_account_enabled');
	$content .= '</label><br>';
	
	
	$this->form->add_input(array(
		'section' => $section,
		'name' => $name,
		'content' => $content,
	));
	
	
	// ----- Auth method
	
	$name = 'u_auth_method';
	
	$this->form->add_input(array(
		
		'section' => $section,
		'name' => $name,
		'label' => lang('users_auth_method'),
		
		'content' => form_dropdown(
			$name, 
			$auth_methods, 
			set_value($name, element($name, $user)), 
			'tabindex="' . tab_index() . '" id="' . $name . '"'
		),
		
	));
	
	
// ===== Membership

$section = 'membership';

$this->form->add_section($section, lang('users_membership'));

	// ----- Group
	
	$name = 'u_g_id';
	
	$this->form->add_input(array(
		
		'section' => $section,
		'name' => $name,
		'label' => lang('users_group'),
		
		'content' => form_dropdown(
			$name, 
			$groups, 
			set_value($name, element($name, $user)), 
			'tabindex="' . tab_index() . '" id="' . $name . '"'
		),
		
	));
	
	
	// ----- Departments
	
	$name = 'departments[]';
	
	$content = '<select name="departments[]" tabindex="' . tab_index() . '" id="departments[]" size="10" multiple="multiple" class="text-input" style="width: 150px">';
	foreach ($departments as $d_id => $d_name)
	{
		$selected = (array_key_exists($d_id, element('departments', $user, array()))) ? 'selected="selected"' : '';
		$content .= '<option value="' . $d_id . '" ' . $selected . '>' . $d_name . '</option>'."\n";
	}
	$content .= '</select>';
	
	$this->form->add_input(array(
		'section' => $section,
		'name' => $name,
		'label' => lang('users_departments'),
		'content' => $content,
	));
	
	
$this->form->add_button(form_button(array(
	'type' => 'submit',
	'class' => 'primary',
	'text' => lang('save'),
	'tab_index' => tab_index(),
)));


echo form_open(current_url(), array('id' => 'user_set_form'));
echo $this->form->render();
echo '</form>';