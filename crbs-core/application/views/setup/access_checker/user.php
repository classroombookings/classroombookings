<?php

$uri = 'setup/access_checker/user/'.$user->user_id;

$attrs = [
	'class' => 'cssform cssform-stacked',
	'hx-post' => site_url($uri),
	'hx-target' => 'this',
	'hx-swap' => 'outerHTML',
];

$hidden = [];

echo form_open($uri, $attrs, $hidden);

?>

<fieldset>

	<legend><?= lang('acl.access_checker.room') ?></legend>

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

	<div>
		<br><br>
		<?php $this->load->view('setup/access_checker/_result') ?>
	</div>

</fieldset>

<?= form_close() ?>

