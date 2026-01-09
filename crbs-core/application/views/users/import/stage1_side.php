<style>
pre {
	background: #f4f4f4;
	padding: 10px;
}
</style>

<div>
	<h5><?= lang('user.import.csv_format') ?></h5>
	<p><?= lang('user.import.csv_format.hint') ?></p>
	<pre><code><?php
		$fields = [
			strtolower(lang('user.field.username')),
			strtolower(lang('user.field.firstname')),
			strtolower(lang('user.field.lastname')),
			strtolower(lang('user.field.email')),
			strtolower(lang('user.field.password')),
			strtolower(lang('role.role')),
			strtolower(lang('department.department')),
		];
		echo implode(', ', $fields);
	?></code></pre>
	<p><?= lang('user.import.header_row_hint') ?></p>
	<p><?= lang('user.import.existing_user_hint') ?></p>
	<p><?= lang('user.import.role_department_hint') ?></p>
</div>
<br>
