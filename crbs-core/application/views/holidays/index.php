<?php

$messages = $this->session->flashdata('saved');
echo "<div class='messages'>{$messages}</div>";

echo iconbar([
	['holidays/add?session_id=' . $session->session_id, lang('holiday.add.action'), 'add.png'],
]);

?>

<table
	width="100%"
	cellpadding="4"
	cellspacing="2"
	border="0"
	class="border-table"
>

	<col /><col /><col /><col />

	<thead>
		<tr class="heading">
			<th class="h" width="30%" title="<?= lang('holiday.field.name') ?>"><?= lang('holiday.field.name') ?></th>
			<th class="h" width="25%" title="<?= lang('holiday.field.date_start') ?>"><?= lang('holiday.field.date_start') ?></th>
			<th class="h" width="25%" title="<?= lang('holiday.field.date_end') ?>"><?= lang('holiday.field.date_end') ?></th>
			<th class="h" width="10%" title="<?= lang('holiday.field.duration') ?>"><?= lang('holiday.field.duration') ?></th>
			<th class="h" width="10%" title="<?= lang('app.actions') ?>"><?= lang('app.actions') ?></th>
		</tr>
	</thead>


	<?php if (empty($holidays)): ?>

	<tbody>
		<tr>
			<td colspan="5" align="center" style="padding:16px 0; color: #666"><?= lang('holiday.no_items') ?></td>
		</tr>
	</tbody>

	<?php else: ?>

	<tbody>
		<?php

		foreach ($holidays as $holiday) {

			echo "<tr>";

			$name = html_escape($holiday->name);
			$link = anchor('holidays/edit/'.$holiday->holiday_id, $name);
			echo "<td>{$link}</td>";

			$start = $holiday->date_start ? date_output_long($holiday->date_start) : '';
			echo "<td>{$start}</td>";

			$end = $holiday->date_end ? date_output_long($holiday->date_end) : '';
			echo "<td>{$end}</td>";

			// Duration
			$duration = 1 + ($holiday->date_start->diff($holiday->date_end)->format('%a'));
			$duration_str = sprintf('%s %s', $duration, strtolower(lang('cal.days')));
			echo "<td>{$duration_str}</td>";

			echo "<td>";
			$actions['edit'] = 'holidays/edit/'.$holiday->holiday_id;
			$actions['delete'] = 'holidays/delete/'.$holiday->holiday_id;
			$this->load->view('partials/editdelete', $actions);
			echo "</td>";

			echo "</tr>";

		}

		?>
	</tbody>

	<?php endif; ?>

</table>

