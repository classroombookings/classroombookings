<?php

// ===== Group details

$section = 'details';

$this->form->add_section($section, lang('groups_group_details'));

	
	// ----- Name
	
	$name = 'g_name';
	
	$this->form->add_input(array(
		
		'section' => $section,
		'name' => $name,
		'label' => lang('groups_group_name'),
		
		'content' => form_input(array(
			'name' => $name,
			'id' => $name,
			'class' => 'text-input',
			'size' => 30,
			'max_length' => 20,
			'tabindex' => tab_index(),
			'autocomplete' => 'off',
			'value' => set_value($name, element($name, $group)),
		)),
		
	));
	
	
	// ---- Description
	
	$name = 'g_description';
	
	$this->form->add_input(array(
		
		'section' => $section,
		'name' => $name,
		'label' => lang('groups_group_description'),
		
		'content' => form_textarea(array(
			'name' => $name,
			'id' => $name,
			'class' => 'text-input',
			'rows' => 5,
			'cols' => 50,
			'tabindex' => tab_index(),
			'value' => set_value($name, element($name, $group)),
		)),
		
	));


if (option('auth_ldap_enable'))
{
	// ===== LDAP groups

	$section = 'ldap';

	$this->form->add_section($section, lang('groups_ldap_groups'), lang('groups_ldap_groups_hint'));
	
		// ----- LDAP groups
		
		$name = 'ldap_groups[]';
		
		$content = '<select name="ldap_groups[]" tabindex="' . tab_index() . '" id="ldap_groups[]" size="20" multiple="multiple" class="text-input" style="width: 350px">';
		foreach ($ldap_groups as $lg_id => $lg_name)
		{
			$selected = (array_key_exists($lg_id, element('ldap_groups', $group, array()))) ? 'selected="selected"' : '';
			$content .= '<option value="' . $lg_id . '" ' . $selected . '>' . $lg_name . '</option>'."\n";
		}
		$content .= '</select>';
		
		$this->form->add_input(array(
			'section' => $section,
			'name' => $name,
			'content' => $content,
		));
	
}

$this->form->add_button(form_button(array(
	'type' => 'submit',
	'class' => 'primary',
	'text' => lang('save'),
	'tab_index' => tab_index(),
)));


echo form_open(current_url(), array('id' => 'group_set_form'));
echo $this->form->render();
echo '</form>';