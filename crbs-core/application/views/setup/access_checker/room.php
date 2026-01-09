<?php

$uri = 'setup/access_checker/room/'.$room->room_id;

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

	<legend><?= lang('acl.access_checker.user') ?></legend>

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

