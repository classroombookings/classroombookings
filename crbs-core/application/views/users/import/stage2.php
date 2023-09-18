<?php

function import_status($key) {

	$labels = array(
		'username_empty' => 'Username empty',
		'password_empty' => 'No password',
		'username_exists' => 'User exists',
		'success' => 'Success',
		'db_error' => 'Error',
		'invalid' => 'Failed validation',
	);

	if (array_key_exists($key, $labels)) {
		return $labels[$key];
	}

	return 'Unknown';
}

?>

<?php if (is_array($result)): ?>

<table cellpadding="2" cellspacing="2" width="100%">

	<thead>
		<tr class="heading">
			<td class="h">Row</td>
			<td class="h">Username</td>
			<td class="h">Created</td>
			<td class="h">Status</td>
		</tr>
	</thead>

	<tbody>

		<?php
		foreach ($result as $row) {

			$colour = ($row->status == 'success') ? 'darkgreen' : 'darkred';

			echo '<tr>';
			echo "<td>#{$row->line}</td>";
			echo '<td style="width: 50%">' . html_escape($row->user->username) . '</td>';
			echo '<td>' . ($row->status == 'success' ? 'Yes' : 'No') . '</td>';
			echo "<td style='font-weight:bold;color:{$colour}'>" . import_status($row->status) . "</td>";
			echo '</tr>';
		}
		?>
	</tbody>

</table>

<?php endif; ?>

<?php

$iconbar = iconbar(array(
	array('users', 'All Users', 'school_manage_users.png'),
	array('users/import', 'Import More Users', 'user_import.png'),
));

echo $iconbar;
