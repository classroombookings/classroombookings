<?php

// ===== CSV file

$section = 'csv';

$this->form->add_section($section, lang('users_import_csv_file'), lang('users_import_csv_file_hint'));
	
	
	// ----- File upload
	
	$name = 'userfile';
	
	$this->form->add_input(array(
		
		'section' => $section,
		'name' => $name,
		
		'content' => form_upload(array(
			'name' => $name,
			'id' => $name,
			'size' => 40,
			'max_length' => 1024,
			'tabindex' => tab_index(),
			'autocomplete' => 'off',
		)) . '<br><br>',
		
	));
	
	
	// ----- Existing users action
	
	$name = 'existing';
	
	$content = '';
	
	$value = 'skip';
	$content .= '<label class="radio">';
	$content .= form_radio(array(
		'name' => $name,
		'id' => $name . '_' . $value,
		'tabindex' => tab_index(),
		'value' => $value,
		'checked' => TRUE,
	));
	$content .= lang('users_import_existing_skip');
	$content .= '</label>';
	
	$value = 'update';
	$content .= '<label class="radio">';
	$content .= form_radio(array(
		'name' => $name,
		'id' => $name . '_' . $value,
		'tabindex' => tab_index(),
		'value' => $value,
		'checked' => TRUE,
	));
	$content .= lang('users_import_existing_update');
	$content .= '</label>';
	
	
	$this->form->add_input(array(
		'section' => $section,
		'name' => $name,
		'label' => lang('users_import_existing_action'),
		'content' => $content,
	));



// ===== Defaults

$section = 'defaults';

$this->form->add_section($section, lang('users_import_defaults'), lang('users_import_defaults_hint'));
	
	
	// ----- Password
	
	$name = 'password';
	
	$this->form->add_input(array(
			
		'section' => $section,
		'name' => $name,
		'label' => lang('password'),
		
		'content' => form_input(array(
			'name' => $name,
			'id' => $name,
			'class' => 'text-input',
			'size' => 30,
			'max_length' => 100,
			'autocomplete' => 'off',
			'tabindex' => tab_index(),
		)),
		
	));
	
	
	// ----- Group
	
	$name = 'g_id';
	
	$this->form->add_input(array(
		
		'section' => $section,
		'name' => $name,
		'label' => lang('users_group'),
		
		'content' => form_dropdown(
			$name, 
			$groups, 
			'',
			'tabindex="' . tab_index() . '" id="' . $name . '" class="text-input" '
		),
		
	));
	
	
	// ----- Department
	
	$local_departments = array('' => '');
	$local_departments += $departments;
	
	$name = 'd_id';
	
	$this->form->add_input(array(
		'section' => $section,
		'name' => $name,
		'label' => lang('users_import_department'),
		'content' => form_dropdown(
			$name, 
			$local_departments,
			'',
			'tabindex="' . tab_index() . '" id="' . $name . '" class="text-input" '
		),
	));
	
	
	// ----- Email domain
	
	$name = 'email_domain';
	$default = end(explode('@', $this->session->userdata('u_email')));
	
	$this->form->add_input(array(
		
		'section' => $section,
		'name' => $name,
		'label' => lang('users_import_email_domain'),
		
		'content' => form_input(array(
			'name' => $name,
			'id' => $name,
			'class' => 'text-input',
			'size' => 30,
			'max_length' => 100,
			'tabindex' => tab_index(),
			'value' => $default,
		)),
		
	));
	
	
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
		'checked' => FALSE,
	));
	$content .= '<strong>' . lang('users_import_enable_account') . '</strong>';
	$content .= '</label><br>';
	
	
	$this->form->add_input(array(
		'section' => $section,
		'name' => $name,
		'content' => $content,
	));
	
	
$this->form->add_button(form_button(array(
	'type' => 'submit',
	'class' => 'blue',
	'text' => lang('next'),
	'tab_index' => tab_index(),
)));


echo form_open_multipart(current_url(), array('id' => 'user_import_1_form'));
echo $this->form->render();
echo '</form>';
