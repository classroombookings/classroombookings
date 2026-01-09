<?php

$messages = $this->session->flashdata('saved');
echo "<div class='messages'>{$messages}</div>";

echo iconbar([
	array('weeks/add', lang('week.add.action'), 'add.png'),
]);

?>

<table
	width="100%"
	cellpadding="4"
	cellspacing="2"
	border="0"
	class="border-table"
>

	<thead>
		<tr class="heading">
			<th class="h" width="5%" title="<?= lang('week.field.colour') ?>"></th>
			<th class="h" width="85%" title="<?= lang('week.field.name') ?>"><?= lang('week.field.name') ?></th>
			<th class="n" width="10%" title="<?= lang('app.actions') ?>"><?= lang('app.actions') ?></th>
		</tr>
	</thead>

	<?php if (empty($weeks)): ?>

	<tbody>
		<tr>
			<td colspan="3" align="center" style="padding:16px 0; color: #666"><?= lang('week.no_items') ?></td>
		</tr>
	</tbody>

	<?php else: ?>

	<tbody>
		<?php

		foreach ($weeks as $week) {

			echo "<tr>";

			$dot = week_dot($week);
			echo "<td style='text-align:center'>{$dot}</td>";

			$name = html_escape($week->name);
			$link = anchor('weeks/edit/'.$week->week_id, $name);
			echo "<td>{$link}</td>";

			echo "<td>";
			$actions['edit'] = 'weeks/edit/'.$week->week_id;
			$actions['delete'] = 'weeks/delete/'.$week->week_id;
			$this->load->view('partials/editdelete', $actions);
			echo "</td>";

			echo "</tr>";

		}

		?>
	</tbody>

	<?php endif; ?>

</table>
