<?php
echo $this->session->flashdata('saved');
echo form_open(current_url(), array('id'=>'settings', 'class'=>'cssform'));
?>


<fieldset>

	<legend accesskey="S" tabindex="<?php echo tab_index() ?>"><?= lang('settings.general.bookings') ?></legend>

	<p id="settings_displaytype">
		<label for="displaytype"><?= lang('settings.general.displaytype.label') ?></label>
		<?php

		$field = "displaytype";
		$value = set_value($field, element($field, $settings), FALSE);

		$options = [
			[
				'value' => 'day',
				'label' => lang('settings.general.displaytype.day'),
				'enable' => 'd_columns_rooms',
			],
			[
				'value' => 'room',
				'label' => lang('settings.general.displaytype.room'),
				'enable' => 'd_columns_days',
			],
		];

		foreach ($options as $opt) {
			$id = "{$field}_{$opt['value']}";
			$input = form_radio(array(
				'name' => $field,
				'id' => $id,
				'value' => $opt['value'],
				'checked' => ($value == $opt['value']),
				'tabindex' => tab_index(),
				'up-switch' => '.d_columns_target',
			));
			echo "<label for='{$id}' class='ni'>{$input}{$opt['label']}</label>";
		}

		?>
		<p class="hint"><?= lang('settings.general.displaytype.hint') ?><br />
			<?php
			echo sprintf('<strong><span>%s</span></strong>: %s',
				lang('settings.general.displaytype.day'),
				lang('settings.general.displaytype.day.hint')
			);
			echo '<br />';
			echo sprintf('<strong><span>%s</span></strong>: %s',
				lang('settings.general.displaytype.room'),
				lang('settings.general.displaytype.room.hint')
			);
			?>
		</p>
	</p>
	<?php echo form_error('displaytype'); ?>

	<p id="settings_columns">
		<label for="columns">Columns</label>
		<?php

		$field = 'd_columns';
		$value = set_value($field, element($field, $settings), FALSE);

		$options = [
			[
				'value' => 'periods',
				'label' => lang('settings.general.columns.periods'),
				'for' => '',
			],
			[
				'value' => 'rooms',
				'label' => lang('settings.general.columns.rooms'),
				'for' => 'day',
			],
			[
				'value' => 'days',
				'label' => lang('settings.general.columns.days'),
				'for' => 'room',
			],
		];

		foreach ($options as $opt) {
			$id = "{$field}_{$opt['value']}";
			$input = form_radio(array(
				'name' => $field,
				'id' => $id,
				'value' => $opt['value'],
				'checked' => ($value == $opt['value']),
				'tabindex' => tab_index(),
			));
			echo "<label for='{$id}' class='d_columns_target ni' up-show-for='{$opt['for']}'>{$input}{$opt['label']}</label>";
		}
		?>
		<p class="hint"><?= lang('settings.general.columns.hint') ?></p>
	</p>
	<?php echo form_error('d_columns') ?>

	<p id="settings_highlight">
		<label for="columns"><?= lang('settings.general.grid_highlight.label') ?></label>
		<?php

		$field = 'grid_highlight';
		$value = set_value($field, element($field, $settings, '0'), FALSE);
		echo form_hidden($field, '0');
		$input = form_checkbox(array(
			'name' => $field,
			'id' => $field,
			'value' => '1',
			'tabindex' => tab_index(),
			'checked' => ($value == '1')
		));
		$hint = lang('settings.general.grid_highlight.hint');
		echo "<label for='{$field}' class='ni'>{$input} {$hint}</label>";
		?>
	</p>
	<br>
	<?php echo form_error('grid_highlight') ?>

</fieldset>




<fieldset>

	<legend accesskey="D" tabindex="<?php echo tab_index() ?>"><?= lang('settings.general.datetime') ?></legend>


	<div style="padding: 16px 0;">
		<?php
		$link = anchor('https://www.php.net/manual/en/function.date.php#refsect1-function.date-parameters', lang('settings.general.datetime.link'), ['target' => '_blank']);
		echo sprintf('%s - %s', lang('settings.general.datetime.hint'), $link);
		?>
	</div>

	<p>
		<label for="timezone"><?= lang('settings.general.timezone.label') ?></label>
		<?php
		$value = set_value('timezone', element('timezone', $settings, date_default_timezone_get()), FALSE);
		$input = form_dropdown([
			'name' => 'timezone',
			'id' => 'timezone',
			'options' => $timezones,
			'selected' => $value,
			'tabindex' => tab_index(),
			'up-autocomplete' => '',
		]);
		echo "<span style='display:inline-block;width:100%;max-width:320px;position:relative;background:transparent'>{$input}</span>";
		?>
		<p></p>
	</p>
	<?php echo form_error('timezone') ?>

	<p>
		<label for="pattern_long"><?= lang('settings.general.date_format_long.label') ?></label>
		<?php
		$value = set_value('pattern_long', element('pattern_long', $date_settings), FALSE);
		echo form_dropdown([
			'name' => 'pattern_long',
			'id' => 'pattern_long',
			'options' => ['' => '(Default)'] + $date_pattern_options,
			'selected' => $value,
			'tabindex' => tab_index(),
			'value' => $value,
		]);
		?>
		<p class="hint"><?= lang('settings.general.date_format_long.hint') ?></p>
	</p>
	<?php echo form_error('pattern_long') ?>

	<p>
		<label for="pattern_weekday"><?= lang('settings.general.date_format_weekday.label') ?></label>
		<?php
		$value = set_value('pattern_weekday', element('pattern_weekday', $date_settings), FALSE);
		echo form_dropdown([
			'name' => 'pattern_weekday',
			'id' => 'pattern_weekday',
			'options' => ['' => '(Default)'] + $date_pattern_options,
			'selected' => $value,
			'tabindex' => tab_index(),
			'value' => $value,
		]);
		?>
		<p class="hint"><?= lang('settings.general.date_format_weekday.hint') ?></p>
	</p>
	<?php echo form_error('pattern_weekday') ?>

	<p>
		<label for="pattern_time"><?= lang('settings.general.time_format_period.label') ?></label>
		<?php
		$value = set_value('pattern_time', element('pattern_time', $date_settings), FALSE);
		echo form_dropdown([
			'name' => 'pattern_time',
			'id' => 'pattern_time',
			'options' => ['' => '(Default)'] + $time_pattern_options,
			'selected' => $value,
			'tabindex' => tab_index(),
			'value' => $value,
		]);
		?>
		<p class="hint"><?= lang('settings.general.time_format_period.hint') ?></p>
	</p>
	<?php echo form_error('pattern_time') ?>


</fieldset>


<fieldset>

	<legend accesskey="L" tabindex="<?php echo tab_index() ?>"><?= lang('settings.general.login_message') ?></legend>

	<div><?= lang('settings.general.login_message.hint') ?></div>

	<?php
	$field = 'login_message_enabled';
	$value = set_value($field, element($field, $settings, '0'), FALSE);
	?>
	<p>
		<label for="<?= $field ?>"><?= lang('app.enable') ?></label>
		<?php
		echo form_hidden($field, '0');
		echo form_checkbox(array(
			'name' => $field,
			'id' => $field,
			'value' => '1',
			'tabindex' => tab_index(),
			'checked' => ($value == '1')
		));
		?>
	</p>

	<?php
	$field = 'login_message_text';
	$value = set_value($field, element($field, $settings, ''), FALSE);
	?>
	<p>
		<?= form_label(lang('settings.general.login_message_text'), $field) ?>
		<?php
		echo form_textarea(array(
			'name' => $field,
			'id' => $field,
			'rows' => '5',
			'cols' => '60',
			'tabindex' => tab_index(),
			'value' => $value,
		));
		?>
	</p>
	<?php echo form_error($field) ?>

</fieldset>

<fieldset>

	<legend accesskey="M" tabindex="<?php echo tab_index() ?>"><?= lang('settings.general.maintenance_mode') ?></legend>

	<div><?= lang('settings.general.maintenance_mode.hint') ?></div>

	<p>
		<label for="maintenance_mode"><?= lang('app.enable') ?></label>
		<?php
		$value = set_value('maintenance_mode', element('maintenance_mode', $settings, '0'), FALSE);
		echo form_hidden('maintenance_mode', '0');
		echo form_checkbox(array(
			'name' => 'maintenance_mode',
			'id' => 'maintenance_mode',
			'value' => '1',
			'tabindex' => tab_index(),
			'checked' => ($value == '1')
		));
		?>
	</p>


	<p>
		<label for="maintenance_mode_message"><?= lang('settings.general.maintenance_mode_message') ?></label>
		<?php
		$field = 'maintenance_mode_message';
		$value = set_value($field, element($field, $settings, ''), FALSE);
		echo form_textarea(array(
			'name' => $field,
			'id' => $field,
			'rows' => '5',
			'cols' => '60',
			'tabindex' => tab_index(),
			'value' => $value,
		));
		?>
		<p class="hint"><?= lang('settings.general.maintenance_mode_message.hint') ?></p>
	</p>
	<?php echo form_error($field) ?>

</fieldset>


<?php if ( ! empty($feature_list)): ?>
<fieldset>

	<legend accesskey="X" tabindex="<?php echo tab_index() ?>"><?= lang('settings.general.experimental_features') ?></legend>

	<div><?= lang('settings.general.experimental_features.hint') ?></div>
	<br>

	<p>
		<label>Features</label>
		<?php

		foreach ($feature_list as $feature_name) {

			$field = $feature_name;
			$title = lang("features_{$feature_name}");
			$description = lang("features_{$feature_name}_description");
			$value = set_value($field, element($field, $settings_features, '0'), FALSE);
			echo form_hidden($field, '0');
			$input = form_checkbox(array(
				'name' => $field,
				'id' => $field,
				'value' => '1',
				'tabindex' => tab_index(),
				'checked' => ($value == '1')
			));
			echo "<label for='{$field}' class='ni'>{$input} {$title}</label>";
			echo "<p class='hint'>{$description}</p>";
		}

		?>

	</p>
</fieldset>
<?php endif; ?>


<?php

$this->load->view('partials/submit', array(
	'submit' => array(lang('app.action.save'), tab_index()),
	'cancel' => array(lang('app.action.cancel'), tab_index(), 'setup'),
));

echo form_close();
