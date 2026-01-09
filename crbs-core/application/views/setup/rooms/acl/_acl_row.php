<?php

$is_open = false;

$icons = [
	'user' => 'school_manage_users.png',
	'role' => 'vcard_key.png',
	'department' => 'school_manage_departments.png',
];

$labels = [
	'user' => lang('user.user'),
	'role' => lang('role.role'),
	'department' => lang('department.department'),
];

$type = $labels[$acl->context_type];
$icon = img([
	'src' => asset_url('assets/images/ui/'.$icons[$acl->context_type]),
	'width' => 16,
	'height' => 16,
	'alt' => $type,
	'style' => 'margin-right: 8px',
]);

?>

<details class="collapse has-border" <?= $is_open ? 'open' : '' ?>>

	<summary class="collapse-header">
		<div class="inline-block">
			<div class="flex align-items-center">
				<?= $icon ?>
				<span class="inline-flex" style="width:100px"><?= $type ?></span>
				<span><?= html_escape($acl->label) ?></span>
			</div>
		</div>
	</summary>

	<div class="collapse-body">
		<?php
		$url = site_url('setup/rooms/acl/edit/'.$acl->acl_id);
		echo "<div hx-get='{$url}' hx-trigger='toggle from:closest details' hx-target='this' hx-swap='outerHTML'></div>";
		?>
	</div>

</details>

