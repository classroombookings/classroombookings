<?php if (is_array($result)): ?>

<table
	cellpadding="2"
	cellspacing="2"
	width="100%"
	class="border-table"
	border="0"
	style="line-height:1.3;"
>

	<thead>
		<tr class="heading">
			<td class="h"><?= lang('user.import.row') ?></td>
			<td class="h"><?= lang('user.field.username') ?></td>
			<td class="h"><?= lang('user.import.created') ?></td>
			<td class="h"><?= lang('app.status') ?></td>
		</tr>
	</thead>

	<tbody>

		<?php
		foreach ($result as $row) {

			$colour = ($row->status == 'success') ? 'darkgreen' : 'darkred';

			echo '<tr>';
			echo "<td>#{$row->line}</td>";
			echo '<td style="width: 50%">' . html_escape($row->user->username) . '</td>';
			echo '<td>' . ($row->status == 'success' ? lang('app.yes') : lang('app.no')) . '</td>';
			$status_key = sprintf('user.import.status.%s', $row->status);
			$status_line = lang($status_key);
			echo "<td style='font-weight:bold;color:{$colour}'>{$status_line}</td>";
			echo '</tr>';
		}
		?>
	</tbody>

</table>

<?php endif; ?>

<?php

$iconbar = iconbar(array(
	array('users', lang('user.all_users'), 'school_manage_users.png'),
	array('users/import', lang('user.import_more'), 'user_import.png'),
));

echo $iconbar;
