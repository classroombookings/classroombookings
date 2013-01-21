<?php

// ===== School Details

$section = 'school_details';

$this->form->add_section($section, lang('school') . ' ' . strtolower(lang('details')));


	// ----- School name

	$name = 'school_name';
	
	$this->form->add_input($section, $name, lang('school') . ' ' . lang('name'));
	
	$this->form->set_content($section, $name, form_input(array(
		'name' => $name,
		'id' => $name,
		'class' => 'text-input',
		'size' => 30,
		'max_length' => 100,
		'tabindex' => tab_index(),
		'value' => set_value($name, element($name, $settings)),
	)));	
	
	
	// ----- School URL

	$name = 'school_url';

	$this->form->add_input($section, $name, lang('settings_school_url'));
	
	$this->form->set_content($section, $name, form_input(array(
		'name' => $name,
		'id' => $name,
		'class' => 'text-input',
		'size' => 50,
		'maxlength' => 255,
		'tabindex' => tab_index(),
		'value' => set_value($name, element($name, $settings)),
	)));
	
	
// ===== Booking page

$section = 'booking';

$this->form->add_section($section, lang('settings_booking_page'));
	
	
	// ----- Timetable view
	
	$name = 'timetable_view';
	$this->form->add_input($section, $name, lang('settings_timetable_view'));
	
	$content = '';
	
	$value = 'day';
	$content .= '<label class="radio">';
	$content .= form_radio(array(
		'name' => $name,
		'id' => $name . '_day',
		'tabindex' => tab_index(),
		'value' => $value,
		'checked' => set_radio($name, $value, (element($name, $settings) === $value)),
	));
	$content .= lang('settings_timetable_day');
	$content .= '</label>';
	
	$value = 'room';
	$content .= '<label class="radio">';
	$content .= form_radio(array(
		'name' => $name,
		'id' => $name . '_room',
		'tabindex' => tab_index(),
		'value' => $value,
		'checked' => set_radio($name, $value, (element($name, $settings) === $value)),
	));
	$content .= lang('settings_timetable_room');
	$content .= '</label>';
	
	$this->form->set_content($section, $name, $content);
	
	$this->form->set_hint($section, $name, lang('settings_timetable_view_hint'));
	
	
	
	// ----- Timetable columns
	
	$name = 'timetable_cols';
	$this->form->add_input($section, $name, lang('settings_timetable_cols'));
	
	$content = '';
	
	$value = 'rooms';
	$content .= '<label class="radio">';
	$content .= form_radio(array(
		'name' => $name,
		'id' => $name . '_rooms',
		'tabindex' => tab_index(),
		'value' => $value,
		'class' => 'view-day',
		'checked' => set_radio($name, $value, (element($name, $settings) === $value)),
	));
	$content .= lang('settings_timetable_rooms');
	$content .= '</label>';
	
	$value = 'periods';
	$content .= '<label class="radio">';
	$content .= form_radio(array(
		'name' => $name,
		'id' => $name . '_periods',
		'tabindex' => tab_index(),
		'value' => $value,
		'class' => 'view-day view-room',
		'checked' => set_radio($name, $value, (element($name, $settings) === $value)),
	));
	$content .= lang('settings_timetable_periods');
	$content .= '</label>';
	
	$value = 'days';
	$content .= '<label class="radio">';
	$content .= form_radio(array(
		'name' => $name,
		'id' => $name . '_days',
		'tabindex' => tab_index(),
		'value' => $value,
		'class' => 'view-room',
		'checked' => set_radio($name, $value, (element($name, $settings) === $value)),
	));
	$content .= lang('settings_timetable_days');
	$content .= '</label>';
	
	
	$this->form->set_content($section, $name, $content);
	
	$this->form->set_hint($section, $name, lang('settings_timetable_cols_hint'));
	

$this->form->add_button(form_button(array(
	'type' => 'submit',
	'class' => 'blue',
	'text' => lang('save_settings'),
	'tab_index' => tab_index(),
)));


// Render form

echo form_open(current_url(), array('id' => 'settings_form'));
echo $this->form->render();
echo '</form>';

?>



<script>
Q.push(function() {
	
	// Handle interactively enabling/disabling timetable view radio options
	$("input[name=timetable_view]").on("change", function(e) {
		
		// Get value of checked item
		var value = $(this).filter(":checked").val();
		
		// Disable all other radio group's options
		$("input[name=timetable_cols]")
			.prop("disabled", "disabled");
		
		// Enable other radio group's relevant options
		var inputs = $("input[name='timetable_cols'].view-" + value);
		inputs.prop("disabled", false);
		
	}).filter(":checked").trigger("change");
	
});
</script>