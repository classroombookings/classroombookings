<?php
echo $this->session->flashdata('saved');
echo form_open_multipart(current_url(), array('id'=>'schooldetails', 'class'=>'cssform'));
?>


<fieldset>

	<legend accesskey="I" tabindex="<?php echo tab_index(); ?>"><?= lang('app.information') ?></legend>

	<p>
		<label for="schoolname" class="required"><?= lang('organisation.field.name') ?></label>
		<?php
		$value = set_value('schoolname', element('name', $settings), FALSE);
		echo form_input(array(
			'name' => 'schoolname',
			'id' => 'schoolname',
			'size' => '30',
			'maxlength' => '255',
			'tabindex' => tab_index(),
			'value' => $value,
		));
		?>
	</p>
	<?php echo form_error('schoolname'); ?>

	<p>
		<label for="website"><?= lang('organisation.field.website') ?></label>
		<?php
		$value = set_value('website', element('website', $settings), FALSE);
		echo form_input(array(
			'name' => 'website',
			'id' => 'website',
			'size' => '40',
			'maxlength' => '255',
			'tabindex' => tab_index(),
			'value' => $value,
		));
		?>
	</p>
	<?php echo form_error('website'); ?>

</fieldset>



<fieldset>

	<legend accesskey="L" tabindex="<?php echo tab_index() ?>"><?= lang('organisation.field.logo') ?></legend>

	<div><?= lang('organisation.field.logo.summary') ?></div><br>

	<p>
		<label><?= lang('organisation.field.logo') ?></label>
		<?php
		$logo = element('logo', $settings);
		$image_url = image_url($logo);
		if ( ! empty($image_url)) {
			echo img($image_url, FALSE, "style='padding:1px; border:1px solid #ccc; max-width: 300px; width: auto; height: auto'");
		} else {
			echo sprintf("<span><em>%s</em></span>", lang('app.none'));
		}
		?>
	</p>

	<p>
		<label for="userfile"><?= lang('app.upload.upload_file') ?></label>
		<?php
		echo form_upload(array(
			'name' => 'userfile',
			'id' => 'userfile',
			'size' => '25',
			'maxlength' => '255',
			'tabindex' => tab_index(),
			'value' => '',
		));
		?>
	</p>

	<?php
	if ($this->session->flashdata('image_error') != '') {
		echo "<p class='hint error'><span>" . $this->session->flashdata('image_error') . "</span></p>";
	}
	?>

	<p>
		<label for="logo_delete"><?= lang('organisation.field.logo.delete') ?></label>
		<?php
		echo form_checkbox(array(
			'name' => 'logo_delete',
			'id' => 'logo_delete',
			'value' => '1',
			'tabindex' => tab_index(),
			'checked' => FALSE,
		));
		?>
		<p class="hint"><?= lang('organisation.field.logo.delete.hint') ?></p>
	</p>

</fieldset>


<?php

$this->load->view('partials/submit', array(
	'submit' => array(lang('app.action.save'), tab_index()),
	'cancel' => array(lang('app.action.cancel'), tab_index(), 'setup'),
));

echo form_close();
