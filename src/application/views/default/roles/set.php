<?php

// ===== Role details

$section = 'details';

$this->form->add_section($section, lang('roles_role_details'));

	
	// ----- Name
	
	$name = 'r_name';
	
	$this->form->add_input(array(
		
		'section' => $section,
		'name' => $name,
		'label' => lang('roles_role_name'),
		
		'content' => form_input(array(
			'name' => $name,
			'id' => $name,
			'class' => 'text-input',
			'size' => 30,
			'max_length' => 20,
			'tabindex' => tab_index(),
			'autocomplete' => 'off',
			'value' => set_value($name, element($name, $role)),
		)),
		
	));


$this->form->add_button(form_button(array(
	'type' => 'submit',
	'class' => 'primary',
	'text' => lang('save'),
	'tab_index' => tab_index(),
)));


echo form_open(current_url(), array('id' => 'role_set_form'));
echo $this->form->render();
echo '</form>';
?>