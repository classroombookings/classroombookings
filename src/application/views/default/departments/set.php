<?php

// ===== Department details

$section = 'details';

$this->form->add_section($section, lang('departments_department_details'));

	
	// ----- Name
	
	$name = 'd_name';
	
	$this->form->add_input(array(
		
		'section' => $section,
		'name' => $name,
		'label' => lang('departments_department_name'),
		
		'content' => form_input(array(
			'name' => $name,
			'id' => $name,
			'class' => 'text-input',
			'size' => 30,
			'max_length' => 20,
			'tabindex' => tab_index(),
			'autocomplete' => 'off',
			'value' => set_value($name, element($name, $department)),
		)),
		
	));
	
	
	// ---- Description
	
	$name = 'd_description';
	
	$this->form->add_input(array(
		
		'section' => $section,
		'name' => $name,
		'label' => lang('departments_department_description'),
		
		'content' => form_textarea(array(
			'name' => $name,
			'id' => $name,
			'class' => 'text-input',
			'rows' => 5,
			'cols' => 50,
			'tabindex' => tab_index(),
			'value' => set_value($name, element($name, $department)),
		)),
		
	));

	
	// ----- Colour
	
	$name = 'd_colour';
	
	$this->form->add_input(array(
		
		'section' => $section,
		'name' => $name,
		'label' => lang('departments_department_colour'),
		
		'content' => form_input(array(
			'name' => $name,
			'id' => $name,
			'class' => 'text-input colorpicker',
			'size' => 10,
			'max_length' => 7,
			'tabindex' => tab_index(),
			'autocomplete' => 'off',
			'value' => set_value($name, element($name, $department)),
		)),
		
	));


if (option('auth_ldap_enable'))
{
	// ===== LDAP groups

	$section = 'ldap';

	$this->form->add_section($section, lang('departments_ldap_groups'), lang('departments_ldap_groups_hint'));
	
		// ----- LDAP groups
		
		$name = 'ldap_groups[]';
		
		$content = '<select name="ldap_groups[]" tabindex="' . tab_index() . '" id="ldap_groups[]" size="20" multiple="multiple" class="text-input" style="width: 350px">';
		$content .= '<option value="">(None)</option>';
		foreach ($ldap_groups as $lg_id => $lg_name)
		{
			$selected = (array_key_exists($lg_id, element('ldap_groups', $department, array()))) ? 'selected="selected"' : '';
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
?>


<script>
Q.push(function() {
	
	$.fn.colorPicker.defaults.colors = [
		"FCE94F", "EDD400", "C4A000", "FCAF3E", "F57900", "CE5C00",
		"E9B96E", "C17D11", "8F5902", "8AE234", "73D216", "4E9A06",
		"729FCF", "3465A4", "204A87", "AD7FA8", "75507B", "5C3566",
		"EF2929", "CC0000", "A40000", "EEEEEC", "BABDB6", "2E3436"
	];
	
	$('.colorpicker').colorPicker();
	
})
</script>