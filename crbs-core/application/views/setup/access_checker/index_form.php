<?php

$uri = 'setup/access_checker';

$attrs = [
	'class' => 'cssform cssform-stacked',
	'hx-post' => site_url($uri),
	'hx-target' => '#check_access_result',
	'hx-swap' => 'outerHTML',
	'hx-select' => '#check_access_result',
];

$hidden = [];

echo form_open($uri, $attrs, $hidden);

?>

<fieldset>

	<legend><?= lang('acl.access_checker.user_and_room') ?></legend>

	<p class="input-group">
		<?php
		echo form_label(lang('user.user'), 'check_user_id');
		echo form_dropdown([
			'name' => 'user_id',
			'id' => 'check_user_id',
			'options' => ['' => ''] + $user_options,
			'selected' => set_value('user_id', ''),
			'style' => 'width:100%',
		]);
	?>
	</p>

	<p class="input-group">
		<?php
		echo form_label(lang('room.room'), 'check_room_id');
		echo form_dropdown([
			'name' => 'room_id',
			'id' => 'check_room_id',
			'options' => ['' => ''] + $room_options,
			'selected' => set_value('room_id', ''),
			'style' => 'width:100%',
		]);
	?>
	</p>
	<?php
	echo form_submit([
		'value' => lang('acl.actions.check_access'),
	]);
	?>

</fieldset>

<?php
echo form_close();
